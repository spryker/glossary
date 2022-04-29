<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Business;

use Generated\Shared\Transfer\KeyTranslationTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\TranslationTransfer;

interface GlossaryFacadeInterface
{
    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\KeyExistsException
     *
     * @return int
     */
    public function createKey($keyName);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     *
     * @return bool
     */
    public function hasKey($keyName);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     *
     * @return int
     */
    public function getKeyIdentifier($keyName);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $oldKeyName
     * @param string $newKeyName
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingKeyException
     *
     * @return bool
     */
    public function updateKey($oldKeyName, $newKeyName);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     *
     * @return bool
     */
    public function deleteKey($keyName);

    /**
     * Specification:
     *  - Deletes keys from database with the given idKeys
     *
     * @api
     *
     * @param array $idKeys
     *
     * @return void
     */
    public function deleteKeys(array $idKeys);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param string $value
     * @param bool $isActive
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingKeyException
     * @throws \Spryker\Zed\Locale\Business\Exception\MissingLocaleException
     * @throws \Spryker\Zed\Glossary\Business\Exception\TranslationExistsException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function createTranslation($keyName, LocaleTransfer $localeTransfer, $value, $isActive = true);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param string $value
     * @param bool $isActive
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingKeyException
     * @throws \Spryker\Zed\Locale\Business\Exception\MissingLocaleException
     * @throws \Spryker\Zed\Glossary\Business\Exception\TranslationExistsException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function createTranslationForCurrentLocale($keyName, $value, $isActive = true);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param string $value
     * @param bool $isActive
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingKeyException
     * @throws \Spryker\Zed\Locale\Business\Exception\MissingLocaleException
     * @throws \Spryker\Zed\Glossary\Business\Exception\TranslationExistsException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function createAndTouchTranslation($keyName, LocaleTransfer $localeTransfer, $value, $isActive = true);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer|null $localeTransfer
     *
     * @return bool
     */
    public function hasTranslation($keyName, ?LocaleTransfer $localeTransfer = null);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingTranslationException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function getTranslation($keyName, LocaleTransfer $localeTransfer);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param string $value
     * @param bool $isActive
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingTranslationException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function updateTranslation($keyName, $localeTransfer, $value, $isActive = true);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param string $value
     * @param bool $isActive
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingTranslationException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function updateAndTouchTranslation($keyName, LocaleTransfer $localeTransfer, $value, $isActive = true);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\KeyTranslationTransfer $keyTranslationTransfer
     *
     * @return bool
     */
    public function saveGlossaryKeyTranslations(KeyTranslationTransfer $keyTranslationTransfer);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TranslationTransfer $transferTranslation
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingKeyException
     * @throws \Spryker\Zed\Locale\Business\Exception\MissingLocaleException
     * @throws \Spryker\Zed\Glossary\Business\Exception\TranslationExistsException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function saveTranslation(TranslationTransfer $transferTranslation);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TranslationTransfer $transferTranslation
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingKeyException
     * @throws \Spryker\Zed\Locale\Business\Exception\MissingLocaleException
     * @throws \Spryker\Zed\Glossary\Business\Exception\TranslationExistsException
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function saveAndTouchTranslation(TranslationTransfer $transferTranslation);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return bool
     */
    public function deleteTranslation($keyName, LocaleTransfer $localeTransfer);

    /**
     * Specification:
     *  - Deletes translations from database with the given idKeys
     *
     * @api
     *
     * @param array $idKeys
     *
     * @return void
     */
    public function deleteTranslationsByFkKeys(array $idKeys);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     * @param array<string, mixed> $data
     * @param \Generated\Shared\Transfer\LocaleTransfer|null $localeTransfer
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingTranslationException
     *
     * @return string
     */
    public function translate($keyName, array $data = [], ?LocaleTransfer $localeTransfer = null);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param int $idKey
     * @param array<string, mixed> $data
     *
     * @throws \Spryker\Zed\Glossary\Business\Exception\MissingTranslationException
     *
     * @return string
     */
    public function translateByKeyId($idKey, array $data = []);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param int $idKey
     *
     * @return void
     */
    public function touchCurrentTranslationForKeyId($idKey);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param int $idKey
     * @param \Generated\Shared\Transfer\LocaleTransfer|null $localeTransfer
     *
     * @return void
     */
    public function touchTranslationForKeyId($idKey, ?LocaleTransfer $localeTransfer = null);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyName
     *
     * @return int
     */
    public function getOrCreateKey($keyName);

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @return void
     */
    public function install();

    /**
     * Specification:
     * - TODO: Add method specification.
     *
     * @api
     *
     * @param string $keyFragment
     *
     * @return array
     */
    public function getKeySuggestions($keyFragment);

    /**
     * Specification:
     * - Finds all translations for specified key and locales.
     *
     * @api
     *
     * @param string $glossaryKey
     * @param array<\Generated\Shared\Transfer\LocaleTransfer> $localeTransfers
     *
     * @return array<\Generated\Shared\Transfer\TranslationTransfer>
     */
    public function getTranslationsByGlossaryKeyAndLocales(string $glossaryKey, array $localeTransfers): array;

    /**
     * Specification:
     * - Finds all translations for specified keys and locale transfers.
     *
     * @api
     *
     * @param array<string> $glossaryKeys
     * @param array<\Generated\Shared\Transfer\LocaleTransfer> $localeTransfers
     *
     * @return array<\Generated\Shared\Transfer\TranslationTransfer>
     */
    public function getTranslationsByGlossaryKeysAndLocaleTransfers(array $glossaryKeys, array $localeTransfers): array;

    /**
     * Specification:
     * - Returns glossary key transfers by array of glossary keys.
     *
     * @api
     *
     * @param array<string> $glossaryKeys
     *
     * @return array<\Generated\Shared\Transfer\GlossaryKeyTransfer>
     */
    public function getGlossaryKeyTransfersByGlossaryKeys(array $glossaryKeys): array;
}
