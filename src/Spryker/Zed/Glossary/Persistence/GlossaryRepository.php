<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Persistence;

use Generated\Shared\Transfer\GlossaryKeyCollectionTransfer;
use Generated\Shared\Transfer\GlossaryKeyCriteriaTransfer;
use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\TranslationTransfer;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryKeyTableMap;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryTranslationTableMap;
use Orm\Zed\Glossary\Persistence\SpyGlossaryKeyQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\Glossary\Persistence\GlossaryPersistenceFactory getFactory()
 */
class GlossaryRepository extends AbstractRepository implements GlossaryRepositoryInterface
{
    /**
     * @var array<string, string>
     */
    protected const array SORTABLE_COLUMNS = [
        GlossaryKeyTransfer::KEY => SpyGlossaryKeyTableMap::COL_KEY,
    ];

    protected const string LIKE_WILDCARD = '%';

    protected const int DEFAULT_PAGINATION_OFFSET = 0;

    public function getGlossaryKeyCollection(GlossaryKeyCriteriaTransfer $glossaryKeyCriteriaTransfer): GlossaryKeyCollectionTransfer
    {
        $glossaryKeyQuery = $this->getFactory()->createGlossaryKeyQuery();
        $glossaryKeyQuery = $this->applyGlossaryKeyFilters($glossaryKeyQuery, $glossaryKeyCriteriaTransfer);
        $glossaryKeyQuery = $this->applyGlossaryKeySorting($glossaryKeyQuery, $glossaryKeyCriteriaTransfer);

        $paginationTransfer = $this->resolveGlossaryKeyPagination($glossaryKeyCriteriaTransfer);
        $glossaryKeyQuery = $this->applyGlossaryKeyPagination($glossaryKeyQuery, $paginationTransfer);
        $glossaryKeyCollectionTransfer = (new GlossaryKeyCollectionTransfer())->setPagination($paginationTransfer);

        $glossaryKeyTransfers = $this->mapGlossaryKeyEntitiesToGlossaryKeyTransfers($glossaryKeyQuery->find());
        $glossaryKeyTransfers = $this->expandGlossaryKeyTransfersWithTranslations($glossaryKeyTransfers);

        foreach ($glossaryKeyTransfers as $glossaryKeyTransfer) {
            $glossaryKeyCollectionTransfer->addGlossaryKey($glossaryKeyTransfer);
        }

        return $glossaryKeyCollectionTransfer;
    }

    protected function applyGlossaryKeyFilters(
        SpyGlossaryKeyQuery $glossaryKeyQuery,
        GlossaryKeyCriteriaTransfer $glossaryKeyCriteriaTransfer
    ): SpyGlossaryKeyQuery {
        $glossaryKeyConditionsTransfer = $glossaryKeyCriteriaTransfer->getGlossaryKeyConditions();
        if ($glossaryKeyConditionsTransfer === null) {
            return $glossaryKeyQuery;
        }

        if ($glossaryKeyConditionsTransfer->getKeys()) {
            $glossaryKeyQuery->where(
                'LOWER(' . SpyGlossaryKeyTableMap::COL_KEY . ') IN ?',
                array_map('mb_strtolower', $glossaryKeyConditionsTransfer->getKeys()),
            );
        }

        if ($glossaryKeyConditionsTransfer->getKeyFragment() !== null) {
            $glossaryKeyQuery->where(
                'LOWER(' . SpyGlossaryKeyTableMap::COL_KEY . ') LIKE ?',
                $this->wrapWithLikeWildcards($glossaryKeyConditionsTransfer->getKeyFragment()),
            );
        }

        if ($glossaryKeyConditionsTransfer->getTranslationValueFragment() !== null) {
            $glossaryKeyQuery
                ->useSpyGlossaryTranslationQuery()
                    ->filterByIsActive(true)
                    ->where(
                        'LOWER(' . SpyGlossaryTranslationTableMap::COL_VALUE . ') LIKE ?',
                        $this->wrapWithLikeWildcards($glossaryKeyConditionsTransfer->getTranslationValueFragment()),
                    )
                ->endUse()
                ->distinct();
        }

        return $glossaryKeyQuery;
    }

    protected function applyGlossaryKeySorting(
        SpyGlossaryKeyQuery $glossaryKeyQuery,
        GlossaryKeyCriteriaTransfer $glossaryKeyCriteriaTransfer
    ): SpyGlossaryKeyQuery {
        foreach ($glossaryKeyCriteriaTransfer->getSortCollection() as $sortTransfer) {
            $sortColumn = static::SORTABLE_COLUMNS[$sortTransfer->getFieldOrFail()] ?? null;
            if ($sortColumn === null) {
                continue;
            }

            $glossaryKeyQuery->orderBy($sortColumn, $sortTransfer->getIsAscending() ? Criteria::ASC : Criteria::DESC);
        }

        return $glossaryKeyQuery->orderByIdGlossaryKey(Criteria::ASC);
    }

    protected function resolveGlossaryKeyPagination(GlossaryKeyCriteriaTransfer $glossaryKeyCriteriaTransfer): PaginationTransfer
    {
        $paginationTransfer = $glossaryKeyCriteriaTransfer->getPagination() ?? new PaginationTransfer();

        return $paginationTransfer
            ->setLimit($paginationTransfer->getLimit() ?? $this->getFactory()->getConfig()->getGlossaryKeyCollectionDefaultLimit())
            ->setOffset($paginationTransfer->getOffset() ?? static::DEFAULT_PAGINATION_OFFSET);
    }

    protected function applyGlossaryKeyPagination(SpyGlossaryKeyQuery $glossaryKeyQuery, PaginationTransfer $paginationTransfer): SpyGlossaryKeyQuery
    {
        $paginationTransfer->setNbResults($glossaryKeyQuery->count());

        return $glossaryKeyQuery
            ->limit($paginationTransfer->getLimitOrFail())
            ->offset($paginationTransfer->getOffsetOrFail());
    }

    /**
     * @param array<\Generated\Shared\Transfer\GlossaryKeyTransfer> $glossaryKeyTransfers
     *
     * @return array<\Generated\Shared\Transfer\GlossaryKeyTransfer>
     */
    protected function expandGlossaryKeyTransfersWithTranslations(array $glossaryKeyTransfers): array
    {
        if ($glossaryKeyTransfers === []) {
            return [];
        }

        $glossaryKeyTransfersIndexedByIdGlossaryKey = [];
        foreach ($glossaryKeyTransfers as $glossaryKeyTransfer) {
            $glossaryKeyTransfersIndexedByIdGlossaryKey[$glossaryKeyTransfer->getIdGlossaryKeyOrFail()] = $glossaryKeyTransfer;
        }

        $glossaryTranslationEntities = $this->getFactory()->createGlossaryTranslationQuery()
            ->filterByFkGlossaryKey_In(array_keys($glossaryKeyTransfersIndexedByIdGlossaryKey))
            ->find();

        $glossaryMapper = $this->getFactory()->createGlossaryMapper();
        foreach ($glossaryTranslationEntities as $glossaryTranslationEntity) {
            $translationTransfer = $glossaryMapper->mapGlossaryTranslationEntityToTranslationTransfer($glossaryTranslationEntity, new TranslationTransfer());
            $glossaryKeyTransfersIndexedByIdGlossaryKey[$glossaryTranslationEntity->getFkGlossaryKey()]->addTranslation($translationTransfer);
        }

        return $glossaryKeyTransfers;
    }

    protected function wrapWithLikeWildcards(string $fragment): string
    {
        return static::LIKE_WILDCARD . mb_strtolower($fragment) . static::LIKE_WILDCARD;
    }

    /**
     * @param string $glossaryKey
     * @param array<string> $localeIsoCodes
     *
     * @return array<\Generated\Shared\Transfer\TranslationTransfer>
     */
    public function getTranslationsByGlossaryKeyAndLocaleIsoCodes(string $glossaryKey, array $localeIsoCodes): array
    {
        /** @var \Orm\Zed\Glossary\Persistence\SpyGlossaryTranslationQuery $glossaryTranslationQuery */
        $glossaryTranslationQuery = $this->getFactory()->createGlossaryTranslationQuery()
            ->joinWithGlossaryKey()
            ->useGlossaryKeyQuery()
                ->filterByKey($glossaryKey)
            ->endUse();

        /** @var \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Glossary\Persistence\SpyGlossaryTranslation> $glossaryTranslationEntities */
        $glossaryTranslationEntities = $glossaryTranslationQuery
            ->useLocaleQuery()
                ->filterByLocaleName_In($localeIsoCodes)
            ->endUse()
            ->find();

        if ($glossaryTranslationEntities->count() === 0) {
            return [];
        }

        return $this->mapGlossaryTranslationEntitiesToTranslationTransfers($glossaryTranslationEntities);
    }

    /**
     * @param array<string> $glossaryKeys
     * @param array<string> $localeIsoCodes
     *
     * @return array<\Generated\Shared\Transfer\TranslationTransfer>
     */
    public function getTranslationsByGlossaryKeysAndLocaleIsoCodes(array $glossaryKeys, array $localeIsoCodes): array
    {
        /** @var \Orm\Zed\Glossary\Persistence\SpyGlossaryTranslationQuery $glossaryTranslationQuery */
        $glossaryTranslationQuery = $this->getFactory()->createGlossaryTranslationQuery()
            ->joinWithGlossaryKey()
            ->useGlossaryKeyQuery()
                ->filterByKey_In($glossaryKeys)
            ->endUse();

        /** @var \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Glossary\Persistence\SpyGlossaryTranslation> $glossaryTranslationEntities */
        $glossaryTranslationEntities = $glossaryTranslationQuery
            ->useLocaleQuery()
                ->filterByLocaleName_In($localeIsoCodes)
            ->endUse()
            ->find();

        if ($glossaryTranslationEntities->count() === 0) {
            return [];
        }

        return $this->mapGlossaryTranslationEntitiesToTranslationTransfers($glossaryTranslationEntities);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Glossary\Persistence\SpyGlossaryTranslation> $glossaryTranslationEntities
     *
     * @return array<\Generated\Shared\Transfer\TranslationTransfer>
     */
    protected function mapGlossaryTranslationEntitiesToTranslationTransfers(ObjectCollection $glossaryTranslationEntities): array
    {
        $translationTransfers = [];
        $glossaryMapper = $this->getFactory()
            ->createGlossaryMapper();

        foreach ($glossaryTranslationEntities as $glossaryTranslationEntity) {
            $translationTransfer = new TranslationTransfer();
            $translationTransfer = $glossaryMapper
                ->mapGlossaryTranslationEntityToTranslationTransfer($glossaryTranslationEntity, $translationTransfer);

            $translationTransfers[] = $translationTransfer;
        }

        return $translationTransfers;
    }

    /**
     * @param array<string> $glossaryKeys
     *
     * @return array<\Generated\Shared\Transfer\GlossaryKeyTransfer>
     */
    public function getGlossaryKeyTransfersByGlossaryKeys(array $glossaryKeys): array
    {
        $glossaryKeyEntities = $this->getFactory()->createGlossaryKeyQuery()
            ->filterByKey_In($glossaryKeys)
            ->find();

        if ($glossaryKeyEntities->count() === 0) {
            return [];
        }

        return $this->mapGlossaryKeyEntitiesToGlossaryKeyTransfers($glossaryKeyEntities);
    }

    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\Glossary\Persistence\SpyGlossaryKey> $glossaryKeyEntities
     *
     * @return array<\Generated\Shared\Transfer\GlossaryKeyTransfer>
     */
    protected function mapGlossaryKeyEntitiesToGlossaryKeyTransfers(Collection $glossaryKeyEntities): array
    {
        $glossaryKeyTransfers = [];
        $glossaryMapper = $this->getFactory()->createGlossaryMapper();

        foreach ($glossaryKeyEntities as $glossaryKeyEntity) {
            $glossaryKeyTransfer = new GlossaryKeyTransfer();
            $glossaryKeyTransfer = $glossaryMapper
                ->mapGlossaryKeyEntityToGlossaryKeyTransfer($glossaryKeyEntity, $glossaryKeyTransfer);

            $glossaryKeyTransfers[] = $glossaryKeyTransfer;
        }

        return $glossaryKeyTransfers;
    }
}
