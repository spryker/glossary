<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Reader;

use Generated\Api\Backend\GlossaryKeysBackendResource;
use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Generated\Shared\Transfer\PaginationTransfer;

interface GlossaryKeyReaderInterface
{
    public function findGlossaryKeyTransferByKey(string $key): ?GlossaryKeyTransfer;

    public function findGlossaryKeyResourceByKey(string $key): ?GlossaryKeysBackendResource;

    /**
     * @param array<\Generated\Shared\Transfer\SortTransfer> $sortTransfers
     *
     * @return array<\Generated\Api\Backend\GlossaryKeysBackendResource>
     */
    public function getGlossaryKeyResourceCollection(
        PaginationTransfer $paginationTransfer,
        array $sortTransfers,
        ?string $keyFragment,
        ?string $translationValueFragment
    ): array;

    /**
     * @return array<string, \Generated\Shared\Transfer\LocaleTransfer>
     */
    public function getLocaleTransfersIndexedByLocaleName(): array;
}
