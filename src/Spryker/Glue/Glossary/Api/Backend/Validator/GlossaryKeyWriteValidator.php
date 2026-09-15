<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Validator;

use Generated\Api\Backend\GlossaryKeysBackendResource;
use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Spryker\Glue\Glossary\Api\Backend\Request\TranslationEntryExtractor;
use Spryker\Glue\Glossary\Api\Backend\Request\TranslationEntryExtractorInterface;

class GlossaryKeyWriteValidator implements GlossaryKeyWriteValidatorInterface
{
    protected const string ERROR_KEY_EXISTS = 'A glossary key "%s" already exists.';

    protected const string ERROR_KEY_IMMUTABLE = 'key is immutable and cannot be changed.';

    protected const string ERROR_LOCALE_NAME_MISSING = 'translations entries must provide a localeName.';

    protected const string ERROR_LOCALE_UNKNOWN = 'Locale "%s" is not available.';

    protected const string ERROR_LOCALE_DUPLICATE = 'Locale "%s" is given more than once in translations.';

    protected const string ERROR_VALUE_EMPTY = 'The translation value for locale "%s" must not be empty.';

    protected const string ERROR_VALUE_REQUIRED_ON_CREATE = 'The translation value for locale "%s" is required when creating a key.';

    public function __construct(protected TranslationEntryExtractorInterface $translationEntryExtractor)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string> $localeNames
     *
     * @return array<string>
     */
    public function validateCreate(
        GlossaryKeysBackendResource $glossaryKeysBackendResource,
        ?GlossaryKeyTransfer $existingGlossaryKeyTransfer,
        array $localeNames
    ): array {
        $errors = [];

        if ($existingGlossaryKeyTransfer !== null) {
            $errors[] = sprintf(static::ERROR_KEY_EXISTS, $existingGlossaryKeyTransfer->getKeyOrFail());
        }

        return array_merge($errors, $this->validateTranslations($glossaryKeysBackendResource->translations ?? [], $localeNames, false));
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string> $localeNames
     *
     * @return array<string>
     */
    public function validateUpdate(
        GlossaryKeysBackendResource $glossaryKeysBackendResource,
        GlossaryKeyTransfer $currentGlossaryKeyTransfer,
        array $localeNames
    ): array {
        $errors = [];

        if (
            $glossaryKeysBackendResource->key !== null
            && mb_strtolower($glossaryKeysBackendResource->key) !== mb_strtolower($currentGlossaryKeyTransfer->getKeyOrFail())
        ) {
            $errors[] = static::ERROR_KEY_IMMUTABLE;
        }

        return array_merge($errors, $this->validateTranslations($glossaryKeysBackendResource->translations ?? [], $localeNames, true));
    }

    /**
     * @param array<array<string, mixed>|object> $translationEntries
     * @param array<string> $localeNames
     *
     * @return array<string>
     */
    protected function validateTranslations(array $translationEntries, array $localeNames, bool $isNullValueAllowed): array
    {
        $errors = [];
        $seenLocaleNames = [];

        foreach ($this->translationEntryExtractor->extract($translationEntries) as $translationEntry) {
            $localeName = $translationEntry[TranslationEntryExtractor::KEY_LOCALE_NAME];
            $value = $translationEntry[TranslationEntryExtractor::KEY_VALUE];

            if ($localeName === null || $localeName === '') {
                $errors[] = static::ERROR_LOCALE_NAME_MISSING;

                continue;
            }

            if (!in_array($localeName, $localeNames, true)) {
                $errors[] = sprintf(static::ERROR_LOCALE_UNKNOWN, $localeName);

                continue;
            }

            if (isset($seenLocaleNames[$localeName])) {
                $errors[] = sprintf(static::ERROR_LOCALE_DUPLICATE, $localeName);

                continue;
            }

            $seenLocaleNames[$localeName] = true;

            if ($value === null && !$isNullValueAllowed) {
                $errors[] = sprintf(static::ERROR_VALUE_REQUIRED_ON_CREATE, $localeName);

                continue;
            }

            if ($value !== null && trim($value) === '') {
                $errors[] = sprintf(static::ERROR_VALUE_EMPTY, $localeName);
            }
        }

        return $errors;
    }
}
