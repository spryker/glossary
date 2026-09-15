<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Request;

interface TranslationEntryExtractorInterface
{
    /**
     * @param array<array<string, mixed>|object> $translationEntries
     *
     * @return array<int, array<string, string|null>>
     */
    public function extract(array $translationEntries): array;
}
