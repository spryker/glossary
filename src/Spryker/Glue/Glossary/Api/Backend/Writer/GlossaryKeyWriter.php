<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Writer;

use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Glue\Glossary\Api\Backend\Request\TranslationEntryExtractor;
use Spryker\Glue\Glossary\Api\Backend\Request\TranslationEntryExtractorInterface;
use Spryker\Zed\Glossary\Business\GlossaryFacadeInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;

class GlossaryKeyWriter implements GlossaryKeyWriterInterface
{
    use TransactionTrait;

    protected const string REMOVED_TRANSLATION_VALUE = '';

    public function __construct(
        protected GlossaryFacadeInterface $glossaryFacade,
        protected TranslationEntryExtractorInterface $translationEntryExtractor,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @param array<array<string, mixed>|object> $translationEntries
     * @param array<string, \Generated\Shared\Transfer\LocaleTransfer> $localeTransfersIndexedByLocaleName
     */
    public function createGlossaryKey(string $key, array $translationEntries, array $localeTransfersIndexedByLocaleName): void
    {
        $this->getTransactionHandler()->handleTransaction(function () use ($key, $translationEntries, $localeTransfersIndexedByLocaleName): void {
            $this->glossaryFacade->createKey($key);

            foreach ($this->translationEntryExtractor->extract($translationEntries) as $translationEntry) {
                $this->glossaryFacade->createAndTouchTranslation(
                    $key,
                    $localeTransfersIndexedByLocaleName[$translationEntry[TranslationEntryExtractor::KEY_LOCALE_NAME]],
                    (string)$translationEntry[TranslationEntryExtractor::KEY_VALUE],
                );
            }
        });
    }

    /**
     * {@inheritDoc}
     *
     * @param array<array<string, mixed>|object> $translationEntries
     * @param array<string, \Generated\Shared\Transfer\LocaleTransfer> $localeTransfersIndexedByLocaleName
     */
    public function updateGlossaryKeyTranslations(
        GlossaryKeyTransfer $glossaryKeyTransfer,
        array $translationEntries,
        array $localeTransfersIndexedByLocaleName
    ): void {
        $key = $glossaryKeyTransfer->getKeyOrFail();
        $storedLocaleIds = $this->getStoredTranslationLocaleIds($glossaryKeyTransfer);

        $this->getTransactionHandler()->handleTransaction(function () use ($key, $translationEntries, $localeTransfersIndexedByLocaleName, $storedLocaleIds): void {
            foreach ($this->translationEntryExtractor->extract($translationEntries) as $translationEntry) {
                $localeTransfer = $localeTransfersIndexedByLocaleName[$translationEntry[TranslationEntryExtractor::KEY_LOCALE_NAME]];
                $value = $translationEntry[TranslationEntryExtractor::KEY_VALUE];
                $hasStoredTranslation = in_array($localeTransfer->getIdLocaleOrFail(), $storedLocaleIds, true);

                $this->applyTranslationEntry($key, $localeTransfer, $value, $hasStoredTranslation);
            }
        });
    }

    protected function applyTranslationEntry(string $key, LocaleTransfer $localeTransfer, ?string $value, bool $hasStoredTranslation): void
    {
        if ($value === null) {
            if ($hasStoredTranslation) {
                $this->glossaryFacade->updateAndTouchTranslation($key, $localeTransfer, static::REMOVED_TRANSLATION_VALUE, false);
            }

            return;
        }

        if ($hasStoredTranslation) {
            $this->glossaryFacade->updateAndTouchTranslation($key, $localeTransfer, $value);

            return;
        }

        $this->glossaryFacade->createAndTouchTranslation($key, $localeTransfer, $value);
    }

    /**
     * @return array<int>
     */
    protected function getStoredTranslationLocaleIds(GlossaryKeyTransfer $glossaryKeyTransfer): array
    {
        $storedLocaleIds = [];
        foreach ($glossaryKeyTransfer->getTranslations() as $translationTransfer) {
            $storedLocaleIds[] = $translationTransfer->getFkLocaleOrFail();
        }

        return $storedLocaleIds;
    }
}
