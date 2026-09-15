<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Request;

class TranslationEntryExtractor implements TranslationEntryExtractorInterface
{
    public const string KEY_LOCALE_NAME = 'localeName';

    public const string KEY_VALUE = 'value';

    /**
     * {@inheritDoc}
     *
     * @param array<array<string, mixed>|object> $translationEntries
     *
     * @return array<int, array<string, string|null>>
     */
    public function extract(array $translationEntries): array
    {
        $extractedEntries = [];
        foreach ($translationEntries as $translationEntry) {
            $translationEntry = is_array($translationEntry) ? $translationEntry : get_object_vars($translationEntry);

            $extractedEntries[] = [
                static::KEY_LOCALE_NAME => $this->toNullableString($translationEntry[static::KEY_LOCALE_NAME] ?? null),
                static::KEY_VALUE => $this->toNullableString($translationEntry[static::KEY_VALUE] ?? null),
            ];
        }

        return $extractedEntries;
    }

    protected function toNullableString(mixed $value): ?string
    {
        return $value === null ? null : (string)$value;
    }
}
