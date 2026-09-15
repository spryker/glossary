<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Mapper;

use Generated\Api\Backend\GlossaryKeysBackendResource;
use Generated\Shared\Transfer\GlossaryKeyTransfer;

interface GlossaryKeyResourceMapperInterface
{
    /**
     * @param array<int, string> $localeNamesIndexedByIdLocale
     */
    public function mapGlossaryKeyTransferToGlossaryKeysBackendResource(
        GlossaryKeyTransfer $glossaryKeyTransfer,
        array $localeNamesIndexedByIdLocale,
        GlossaryKeysBackendResource $glossaryKeysBackendResource
    ): GlossaryKeysBackendResource;
}
