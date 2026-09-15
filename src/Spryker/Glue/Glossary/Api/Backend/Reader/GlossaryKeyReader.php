<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Reader;

use Generated\Api\Backend\GlossaryKeysBackendResource;
use Generated\Shared\Transfer\GlossaryKeyCollectionTransfer;
use Generated\Shared\Transfer\GlossaryKeyConditionsTransfer;
use Generated\Shared\Transfer\GlossaryKeyCriteriaTransfer;
use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Spryker\Glue\Glossary\Api\Backend\Mapper\GlossaryKeyResourceMapperInterface;
use Spryker\Zed\Glossary\Business\GlossaryFacadeInterface;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;

class GlossaryKeyReader implements GlossaryKeyReaderInterface
{
    /**
     * @var array<string, \Generated\Shared\Transfer\LocaleTransfer>|null
     */
    protected ?array $localeTransfersIndexedByLocaleName = null;

    public function __construct(
        protected GlossaryFacadeInterface $glossaryFacade,
        protected LocaleFacadeInterface $localeFacade,
        protected GlossaryKeyResourceMapperInterface $glossaryKeyResourceMapper,
    ) {
    }

    public function findGlossaryKeyTransferByKey(string $key): ?GlossaryKeyTransfer
    {
        $glossaryKeyCollectionTransfer = $this->glossaryFacade->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())->setGlossaryKeyConditions(
                (new GlossaryKeyConditionsTransfer())->addKey($key),
            ),
        );

        /** @var \Generated\Shared\Transfer\GlossaryKeyTransfer|null $glossaryKeyTransfer */
        $glossaryKeyTransfer = $glossaryKeyCollectionTransfer->getGlossaryKeys()->getIterator()->current();

        return $glossaryKeyTransfer;
    }

    public function findGlossaryKeyResourceByKey(string $key): ?GlossaryKeysBackendResource
    {
        $glossaryKeyTransfer = $this->findGlossaryKeyTransferByKey($key);
        if ($glossaryKeyTransfer === null) {
            return null;
        }

        return $this->glossaryKeyResourceMapper->mapGlossaryKeyTransferToGlossaryKeysBackendResource(
            $glossaryKeyTransfer,
            $this->getLocaleNamesIndexedByIdLocale(),
            new GlossaryKeysBackendResource(),
        );
    }

    /**
     * {@inheritDoc}
     *
     * @param array<\Generated\Shared\Transfer\SortTransfer> $sortTransfers
     *
     * @return array<\Generated\Api\Backend\GlossaryKeysBackendResource>
     */
    public function getGlossaryKeyResourceCollection(
        PaginationTransfer $paginationTransfer,
        array $sortTransfers,
        ?string $keyFragment,
        ?string $translationValueFragment
    ): array {
        $glossaryKeyCriteriaTransfer = (new GlossaryKeyCriteriaTransfer())
            ->setGlossaryKeyConditions(
                (new GlossaryKeyConditionsTransfer())
                    ->setKeyFragment($keyFragment)
                    ->setTranslationValueFragment($translationValueFragment),
            )
            ->setPagination($paginationTransfer);

        foreach ($sortTransfers as $sortTransfer) {
            $glossaryKeyCriteriaTransfer->addSort($sortTransfer);
        }

        $glossaryKeyCollectionTransfer = $this->glossaryFacade->getGlossaryKeyCollection($glossaryKeyCriteriaTransfer);

        if ($glossaryKeyCollectionTransfer->getPagination() !== null) {
            $paginationTransfer->fromArray($glossaryKeyCollectionTransfer->getPaginationOrFail()->toArray(), true);
        }

        return $this->mapGlossaryKeyCollectionToResources($glossaryKeyCollectionTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @return array<string, \Generated\Shared\Transfer\LocaleTransfer>
     */
    public function getLocaleTransfersIndexedByLocaleName(): array
    {
        return $this->localeTransfersIndexedByLocaleName ??= $this->localeFacade->getLocaleCollection();
    }

    /**
     * @return array<\Generated\Api\Backend\GlossaryKeysBackendResource>
     */
    protected function mapGlossaryKeyCollectionToResources(GlossaryKeyCollectionTransfer $glossaryKeyCollectionTransfer): array
    {
        $localeNamesIndexedByIdLocale = $this->getLocaleNamesIndexedByIdLocale();

        $resources = [];
        foreach ($glossaryKeyCollectionTransfer->getGlossaryKeys() as $glossaryKeyTransfer) {
            $resources[] = $this->glossaryKeyResourceMapper->mapGlossaryKeyTransferToGlossaryKeysBackendResource(
                $glossaryKeyTransfer,
                $localeNamesIndexedByIdLocale,
                new GlossaryKeysBackendResource(),
            );
        }

        return $resources;
    }

    /**
     * @return array<int, string>
     */
    protected function getLocaleNamesIndexedByIdLocale(): array
    {
        $localeNamesIndexedByIdLocale = [];
        foreach ($this->getLocaleTransfersIndexedByLocaleName() as $localeTransfer) {
            $localeNamesIndexedByIdLocale[$localeTransfer->getIdLocaleOrFail()] = $localeTransfer->getLocaleNameOrFail();
        }

        return $localeNamesIndexedByIdLocale;
    }
}
