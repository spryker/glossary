<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Business;

use Generated\Shared\Transfer\KeyTranslationTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\TranslationTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\Glossary\Business\GlossaryBusinessFactory getFactory()
 * @method \Spryker\Zed\Glossary\Persistence\GlossaryRepositoryInterface getRepository()
 */
class GlossaryFacade extends AbstractFacade implements GlossaryFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     *
     * @return int
     */
    public function createKey($keyName)
    {
        $keyManager = $this->getFactory()->createKeyManager();

        return $keyManager->createKey($keyName);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     *
     * @return bool
     */
    public function hasKey($keyName)
    {
        $keyManager = $this->getFactory()->createKeyManager();

        return $keyManager->hasKey($keyName);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     *
     * @return int
     */
    public function getKeyIdentifier($keyName)
    {
        $keyManager = $this->getFactory()->createKeyManager();

        return $keyManager->getKey($keyName)->getPrimaryKey();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $oldKeyName
     * @param string $newKeyName
     *
     * @return bool
     */
    public function updateKey($oldKeyName, $newKeyName)
    {
        $keyManager = $this->getFactory()->createKeyManager();

        return $keyManager->updateKey($oldKeyName, $newKeyName);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     *
     * @return bool
     */
    public function deleteKey($keyName)
    {
        $keyManager = $this->getFactory()->createKeyManager();

        return $keyManager->deleteKey($keyName);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $idKeys
     *
     * @return void
     */
    public function deleteKeys(array $idKeys)
    {
        $keyManager = $this->getFactory()->createKeyManager();

        $keyManager->deleteKeys($idKeys);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param string $value
     * @param bool $isActive
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function createTranslation($keyName, LocaleTransfer $localeTransfer, $value, $isActive = true)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->createTranslation($keyName, $localeTransfer, $value, $isActive);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     * @param string $value
     * @param bool $isActive
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function createTranslationForCurrentLocale($keyName, $value, $isActive = true)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->createTranslationForCurrentLocale($keyName, $value, $isActive);
    }

    /**
     * {@inheritDoc}
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
    public function createAndTouchTranslation($keyName, LocaleTransfer $localeTransfer, $value, $isActive = true)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->createAndTouchTranslation($keyName, $localeTransfer, $value, $isActive);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer|null $localeTransfer
     *
     * @return bool
     */
    public function hasTranslation($keyName, ?LocaleTransfer $localeTransfer = null)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->hasTranslation($keyName, $localeTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function getTranslation($keyName, LocaleTransfer $localeTransfer)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->getTranslationByKeyName($keyName, $localeTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     * @param string $value
     * @param bool $isActive
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function updateTranslation($keyName, $localeTransfer, $value, $isActive = true)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->updateTranslation($keyName, $localeTransfer, $value, $isActive);
    }

    /**
     * {@inheritDoc}
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
    public function updateAndTouchTranslation($keyName, LocaleTransfer $localeTransfer, $value, $isActive = true)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->updateAndTouchTranslation($keyName, $localeTransfer, $value, $isActive);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\KeyTranslationTransfer $keyTranslationTransfer
     *
     * @return bool
     */
    public function saveGlossaryKeyTranslations(KeyTranslationTransfer $keyTranslationTransfer)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->saveGlossaryKeyTranslations($keyTranslationTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TranslationTransfer $transferTranslation
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function saveTranslation(TranslationTransfer $transferTranslation)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->saveTranslation($transferTranslation);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\TranslationTransfer $transferTranslation
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer
     */
    public function saveAndTouchTranslation(TranslationTransfer $transferTranslation)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->saveAndTouchTranslation($transferTranslation);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     * @param \Generated\Shared\Transfer\LocaleTransfer $localeTransfer
     *
     * @return bool
     */
    public function deleteTranslation($keyName, LocaleTransfer $localeTransfer)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->deleteTranslation($keyName, $localeTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array $idKeys
     *
     * @return void
     */
    public function deleteTranslationsByFkKeys(array $idKeys)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        $translationManager->deleteTranslationsByFkKeys($idKeys);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     * @param array<string, mixed> $data
     * @param \Generated\Shared\Transfer\LocaleTransfer|null $localeTransfer
     *
     * @return string
     */
    public function translate($keyName, array $data = [], ?LocaleTransfer $localeTransfer = null)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->translate($keyName, $data, $localeTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idKey
     * @param array<string, mixed> $data
     *
     * @return string
     */
    public function translateByKeyId($idKey, array $data = [])
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        return $translationManager->translateByKeyId($idKey, $data);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idKey
     *
     * @return void
     */
    public function touchCurrentTranslationForKeyId($idKey)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        $translationManager->touchCurrentTranslationForKeyId($idKey);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idKey
     * @param \Generated\Shared\Transfer\LocaleTransfer|null $localeTransfer
     *
     * @return void
     */
    public function touchTranslationForKeyId($idKey, ?LocaleTransfer $localeTransfer = null)
    {
        $translationManager = $this->getFactory()->createTranslationManager();

        $translationManager->touchTranslationForKeyId($idKey, $localeTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyName
     *
     * @return int
     */
    public function getOrCreateKey($keyName)
    {
        $keyManager = $this->getFactory()->createKeyManager();

        return $keyManager->getOrCreateKey($keyName);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return void
     */
    public function install()
    {
        $this->getFactory()->createInstaller()->install();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $keyFragment
     *
     * @return array
     */
    public function getKeySuggestions($keyFragment)
    {
        return $this->getFactory()
            ->createKeyManager()
            ->getKeySuggestions($keyFragment);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $glossaryKey
     * @param array<\Generated\Shared\Transfer\LocaleTransfer> $localeTransfers
     *
     * @return array<\Generated\Shared\Transfer\TranslationTransfer>
     */
    public function getTranslationsByGlossaryKeyAndLocales(string $glossaryKey, array $localeTransfers): array
    {
        return $this->getFactory()
            ->createTranslationReader()
            ->getTranslationsByGlossaryKeyAndLocaleTransfers($glossaryKey, $localeTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<string> $glossaryKeys
     * @param array<\Generated\Shared\Transfer\LocaleTransfer> $localeTransfers
     *
     * @return array<\Generated\Shared\Transfer\TranslationTransfer>
     */
    public function getTranslationsByGlossaryKeysAndLocaleTransfers(array $glossaryKeys, array $localeTransfers): array
    {
        return $this->getFactory()
            ->createTranslationReader()
            ->getTranslationsByGlossaryKeysAndLocaleTransfers($glossaryKeys, $localeTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<string> $glossaryKeys
     *
     * @return array<\Generated\Shared\Transfer\GlossaryKeyTransfer>
     */
    public function getGlossaryKeyTransfersByGlossaryKeys(array $glossaryKeys): array
    {
        return $this->getFactory()
            ->createGlossaryKeyReader()
            ->getGlossaryKeyTransfersByGlossaryKeys($glossaryKeys);
    }
}
