<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Writer;

use Generated\Shared\Transfer\GlossaryKeyTransfer;

interface GlossaryKeyWriterInterface
{
    /**
     * @param array<array<string, mixed>|object> $translationEntries
     * @param array<string, \Generated\Shared\Transfer\LocaleTransfer> $localeTransfersIndexedByLocaleName
     */
    public function createGlossaryKey(string $key, array $translationEntries, array $localeTransfersIndexedByLocaleName): void;

    /**
     * @param array<array<string, mixed>|object> $translationEntries
     * @param array<string, \Generated\Shared\Transfer\LocaleTransfer> $localeTransfersIndexedByLocaleName
     */
    public function updateGlossaryKeyTranslations(
        GlossaryKeyTransfer $glossaryKeyTransfer,
        array $translationEntries,
        array $localeTransfersIndexedByLocaleName
    ): void;
}
