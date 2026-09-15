<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Mapper;

use Generated\Api\Backend\GlossaryKeys\GlossaryKeysTranslationsBackendObject;
use Generated\Api\Backend\GlossaryKeysBackendResource;
use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Spryker\Glue\Glossary\Api\Backend\Request\TranslationEntryExtractor;

class GlossaryKeyResourceMapper implements GlossaryKeyResourceMapperInterface
{
    /**
     * {@inheritDoc}
     *
     * @param array<int, string> $localeNamesIndexedByIdLocale
     */
    public function mapGlossaryKeyTransferToGlossaryKeysBackendResource(
        GlossaryKeyTransfer $glossaryKeyTransfer,
        array $localeNamesIndexedByIdLocale,
        GlossaryKeysBackendResource $glossaryKeysBackendResource
    ): GlossaryKeysBackendResource {
        $activeValuesIndexedByLocaleName = [];
        foreach ($glossaryKeyTransfer->getTranslations() as $translationTransfer) {
            $localeName = $localeNamesIndexedByIdLocale[$translationTransfer->getFkLocaleOrFail()] ?? null;
            if ($localeName === null || $translationTransfer->getIsActive() !== true) {
                continue;
            }

            $activeValuesIndexedByLocaleName[$localeName] = $translationTransfer->getValue();
        }

        $localeNames = array_values($localeNamesIndexedByIdLocale);
        sort($localeNames);

        $translations = [];
        foreach ($localeNames as $localeName) {
            $translations[] = GlossaryKeysTranslationsBackendObject::fromArray([
                TranslationEntryExtractor::KEY_LOCALE_NAME => $localeName,
                TranslationEntryExtractor::KEY_VALUE => $activeValuesIndexedByLocaleName[$localeName] ?? null,
            ]);
        }

        $glossaryKeysBackendResource->key = $glossaryKeyTransfer->getKeyOrFail();
        $glossaryKeysBackendResource->translations = $translations;

        return $glossaryKeysBackendResource;
    }
}
