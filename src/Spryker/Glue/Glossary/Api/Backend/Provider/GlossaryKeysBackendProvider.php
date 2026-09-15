<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Provider;

use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Generated\Shared\Transfer\SortTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractBackendProvider;
use Spryker\Glue\Glossary\Api\Backend\Exception\GlossaryKeysExceptionFactory;
use Spryker\Glue\Glossary\Api\Backend\Reader\GlossaryKeyReaderInterface;

class GlossaryKeysBackendProvider extends AbstractBackendProvider
{
    protected const string URI_VARIABLE_KEY = 'key';

    protected const string QUERY_PARAM_SORT = 'sort';

    protected const string QUERY_PARAM_FILTER = 'filter';

    protected const string FILTER_KEY = 'glossary-keys.key';

    protected const string FILTER_VALUE = 'glossary-keys.value';

    protected const string SORT_DESCENDING_PREFIX = '-';

    /**
     * @var array<string, string>
     */
    protected const array SORTABLE_FIELDS = [
        'key' => GlossaryKeyTransfer::KEY,
    ];

    public function __construct(
        protected GlossaryKeyReaderInterface $glossaryKeyReader,
        protected GlossaryKeysExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function provideItem(): ?object
    {
        $key = (string)$this->getUriVariable(static::URI_VARIABLE_KEY);

        $glossaryKeysBackendResource = $this->glossaryKeyReader->findGlossaryKeyResourceByKey($key);
        if ($glossaryKeysBackendResource === null) {
            throw $this->exceptionFactory->createGlossaryKeyNotFoundException($key);
        }

        return $glossaryKeysBackendResource;
    }

    /**
     * @return array<\Generated\Api\Backend\GlossaryKeysBackendResource>
     */
    protected function provideCollection(): array
    {
        $paginationTransfer = $this->buildPaginationTransfer();

        $resources = $this->glossaryKeyReader->getGlossaryKeyResourceCollection(
            $paginationTransfer,
            $this->buildSortTransfers(),
            $this->getFilterValue(static::FILTER_KEY),
            $this->getFilterValue(static::FILTER_VALUE),
        );

        if ($paginationTransfer->getNbResults() !== null) {
            $this->setCollectionPagination(
                $paginationTransfer->getOffsetOrFail(),
                $paginationTransfer->getLimitOrFail(),
                $paginationTransfer->getNbResultsOrFail(),
            );
        }

        return $resources;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return array<\Generated\Shared\Transfer\SortTransfer>
     */
    protected function buildSortTransfers(): array
    {
        if (!$this->hasRequest()) {
            return [];
        }

        $sortField = trim((string)$this->getRequest()->query->get(static::QUERY_PARAM_SORT, ''));
        if ($sortField === '') {
            return [];
        }

        $isAscending = !str_starts_with($sortField, static::SORT_DESCENDING_PREFIX);
        $publicField = ltrim($sortField, static::SORT_DESCENDING_PREFIX);

        if (!isset(static::SORTABLE_FIELDS[$publicField])) {
            throw $this->exceptionFactory->createUnsupportedSortFieldException($publicField, array_keys(static::SORTABLE_FIELDS));
        }

        return [
            (new SortTransfer())
                ->setField(static::SORTABLE_FIELDS[$publicField])
                ->setIsAscending($isAscending),
        ];
    }

    protected function getFilterValue(string $name): ?string
    {
        if (!$this->hasRequest()) {
            return null;
        }

        $filters = $this->getRequest()->query->all()[static::QUERY_PARAM_FILTER] ?? [];
        if (!is_array($filters) || !isset($filters[$name])) {
            return null;
        }

        $filterValue = trim((string)$filters[$name]);

        return $filterValue === '' ? null : $filterValue;
    }
}
