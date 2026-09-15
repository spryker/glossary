<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Validator;

use Generated\Api\Backend\GlossaryKeysBackendResource;
use Generated\Shared\Transfer\GlossaryKeyTransfer;

interface GlossaryKeyWriteValidatorInterface
{
    /**
     * @param array<string> $localeNames
     *
     * @return array<string>
     */
    public function validateCreate(
        GlossaryKeysBackendResource $glossaryKeysBackendResource,
        ?GlossaryKeyTransfer $existingGlossaryKeyTransfer,
        array $localeNames
    ): array;

    /**
     * @param array<string> $localeNames
     *
     * @return array<string>
     */
    public function validateUpdate(
        GlossaryKeysBackendResource $glossaryKeysBackendResource,
        GlossaryKeyTransfer $currentGlossaryKeyTransfer,
        array $localeNames
    ): array;
}
