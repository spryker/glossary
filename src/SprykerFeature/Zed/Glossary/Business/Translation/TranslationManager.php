<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\Glossary\Business\Translation;

use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\TranslationTransfer;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;
use SprykerEngine\Zed\Locale\Business\Exception\MissingLocaleException;
use SprykerFeature\Zed\Glossary\Business\Exception\MissingKeyException;
use SprykerFeature\Zed\Glossary\Business\Exception\MissingTranslationException;
use SprykerFeature\Zed\Glossary\Business\Exception\TranslationExistsException;
use SprykerFeature\Zed\Glossary\Business\Key\KeyManagerInterface;
use SprykerFeature\Zed\Glossary\Dependency\Facade\GlossaryToLocaleInterface;
use SprykerFeature\Zed\Glossary\Dependency\Facade\GlossaryToTouchInterface;
use SprykerFeature\Zed\Glossary\Persistence\GlossaryQueryContainerInterface;
use SprykerFeature\Zed\Glossary\Persistence\Propel\Map\SpyGlossaryTranslationTableMap;
use SprykerFeature\Zed\Glossary\Persistence\Propel\SpyGlossaryTranslation;

class TranslationManager implements TranslationManagerInterface
{

    const TOUCH_TRANSLATION = 'translation';

    /**
     * @var GlossaryQueryContainerInterface
     */
    protected $glossaryQueryContainer;

    /**
     * @var GlossaryToTouchInterface
     */
    protected $touchFacade;

    /**
     * @var KeyManagerInterface
     */
    protected $keyManager;

    /**
     * @var GlossaryToLocaleInterface
     */
    protected $localeFacade;


    /**
     * @param GlossaryQueryContainerInterface $glossaryQueryContainer
     * @param GlossaryToTouchInterface $touchFacade
     * @param GlossaryToLocaleInterface $localeFacade
     * @param KeyManagerInterface $keyManager
     */
    public function __construct(
        GlossaryQueryContainerInterface $glossaryQueryContainer,
        GlossaryToTouchInterface $touchFacade,
        GlossaryToLocaleInterface $localeFacade,
        KeyManagerInterface $keyManager
    ) {
        $this->glossaryQueryContainer = $glossaryQueryContainer;
        $this->touchFacade = $touchFacade;
        $this->keyManager = $keyManager;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param array $formData
     *
     * @return bool
     * @throws MissingKeyException
     */
    public function saveGlossaryKeyTranslations(array $formData)
    {
        if (false === isset($formData['key']) || empty($formData['key'])) {
            throw new MissingKeyException('Glossary Key cannot be empty');
        }

        if (true === $this->keyManager->hasKey($formData['key'])) {
            $glossaryKey = $this->keyManager->getKey($formData['key'])->getIdGlossaryKey();
        } else {
            $glossaryKey = $this->keyManager->createKey($formData['key']);
        }

        $availableLocales = $this->localeFacade->getAvailableLocales();

        foreach ($availableLocales as $localeId => $localeName) {
            if (array_key_exists('locale_' . $localeId, $formData)) {
                $locale = $this->localeFacade->getLocale($availableLocales[$localeId]);
                $translationTransfer = $this->createTranslationTransfer(
                    $locale,
                    $glossaryKey,
                    $formData['locale_' . $locale->getIdLocale()]
                );

                $this->saveTranslation($translationTransfer);
            }
        }

        return true;
    }

    /**
     * @param array $formData
     * @param LocaleTransfer $locale
     * @param int $glossaryKey
     *
     * @return TranslationTransfer
     */
    private function createTranslationTransfer(LocaleTransfer $locale, $glossaryKey, $value)
    {
        $translationTransfer = new TranslationTransfer();
        $translationTransfer->setValue($value);
        $translationTransfer->setFkGlossaryKey($glossaryKey);
        $translationTransfer->setFkLocale($locale->getIdLocale());

        return $translationTransfer;
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     *
     * @return bool
     */
    protected function hasTranslationByIds($idKey, $idLocale)
    {
        $translationCount = $this->glossaryQueryContainer
            ->queryTranslationByIds($idKey, $idLocale)
            ->count()
        ;

        return $translationCount > 0;
    }

    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return TranslationTransfer
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     */
    public function createTranslation($keyName, LocaleTransfer $locale, $value, $isActive)
    {
        $idKey = $this->keyManager->getKey($keyName)->getPrimaryKey();
        $idLocale = $locale->getIdLocale();

        if (null === $idLocale) {
            $idLocale = $this->localeFacade->getLocale($locale->getLocaleName())->getIdLocale();
        }

        return $this->createTranslationByIds($idKey, $idLocale, $value, $isActive);
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     *
     * @throws TranslationExistsException
     */
    protected function checkTranslationDoesNotExist($idKey, $idLocale)
    {
        if ($this->hasTranslationByIds($idKey, $idLocale)) {
            throw new TranslationExistsException(
                sprintf(
                    'Tried to create a translation for keyId %s, localeId %s, but it already exists',
                    $idKey,
                    $idLocale
                )
            );
        };
    }


    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     *
     * @return bool
     */
    public function hasTranslation($keyName, LocaleTransfer $locale)
    {
        $translationCount = $this->glossaryQueryContainer
            ->queryTranslationByNames($keyName, $locale->getLocaleName())
            ->count()
        ;

        return $translationCount > 0;
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     * @param string $value
     * @param bool $isActive
     *
     * @return TranslationTransfer
     * @throws \Exception
     * @throws PropelException
     */
    protected function createTranslationByIds($idKey, $idLocale, $value, $isActive)
    {
        $this->checkTranslationDoesNotExist($idKey, $idLocale);

        $translation = new SpyGlossaryTranslation();

        $translation
            ->setFkGlossaryKey($idKey)
            ->setFkLocale($idLocale)
            ->setValue($value)
            ->setIsActive($isActive)
        ;

        $translation->save();

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idItem
     */
    protected function insertActiveTouchRecord($idItem)
    {
        $this->touchFacade->touchActive(
            self::TOUCH_TRANSLATION,
            $idItem
        );
    }

    /**
     * @param SpyGlossaryTranslation $translation
     *
     * @return TranslationTransfer
     */
    protected function convertEntityToTranslationTransfer(SpyGlossaryTranslation $translation)
    {
        $transferTranslation = new TranslationTransfer();
        $transferTranslation->fromArray($translation->toArray());

        return $transferTranslation;
    }

    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return TranslationTransfer
     * @throws MissingTranslationException
     * @throws PropelException
     */
    public function updateTranslation($keyName, LocaleTransfer $locale, $value, $isActive)
    {
        $translation = $this->getUpdatedTranslationEntity($keyName, $locale, $value, $isActive);

        return $this->doUpdateTranslation($translation);
    }

    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getUpdatedTranslationEntity($keyName, $locale, $value, $isActive)
    {
        $translation = $this->getTranslationEntityByNames($keyName, $locale->getLocaleName());

        $translation->setValue($value);
        $translation->setIsActive($isActive);

        return $translation;
    }

    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     *
     * @return TranslationTransfer
     * @throws MissingTranslationException
     */
    public function getTranslationByKeyName($keyName, LocaleTransfer $locale)
    {
        $translation = $this->getTranslationEntityByNames($keyName, $locale->getLocaleName());

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idItem
     */
    protected function insertDeletedTouchRecord($idItem)
    {
        $this->touchFacade->touchDeleted(
            self::TOUCH_TRANSLATION,
            $idItem
        );
    }

    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     *
     * @return bool
     */
    public function deleteTranslation($keyName, LocaleTransfer $locale)
    {
        if (!$this->hasTranslation($keyName, $locale)) {
            return true;
        }

        $translation = $this->getTranslationEntityByNames($keyName, $locale->getLocaleName());

        $translation->setIsActive(false);

        if ($translation->isModified()) {
            $translation->save();
            $this->insertDeletedTouchRecord($translation->getPrimaryKey());
        }

        return true;
    }

    /**
     * @param string $keyName
     * @param array $data
     *
     * @return string
     * @throws MissingTranslationException
     */
    public function translate($keyName, array $data = [])
    {
        $locale = $this->localeFacade->getCurrentLocale();
        $translation = $this->getTranslationByKeyName($keyName, $locale);

        return str_replace(array_keys($data), array_values($data), $translation->getValue());
    }

    /**
     * @param TranslationTransfer $transferTranslation
     *
     * @return TranslationTransfer
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     * @throws MissingTranslationException
     */
    public function saveTranslation(TranslationTransfer $transferTranslation)
    {
        if ($this->hasTranslationByIds($transferTranslation->getFkGlossaryKey(), $transferTranslation->getFkLocale())) {
            $translationEntity = $this->getTranslationByIds(
                $transferTranslation->getFkGlossaryKey(),
                $transferTranslation->getFkLocale()
            );
            $translationEntity->setValue($transferTranslation->getValue());
            $translationEntity->save();

            $translation = (new TranslationTransfer())->fromArray($translationEntity->toArray());
        } else {
            $translation = $this->createTranslationFromTransfer($transferTranslation);
        }

        return $translation;
    }

    /**
     * @param TranslationTransfer $transferTranslation
     *
     * @return TranslationTransfer
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     * @throws MissingTranslationException
     */
    public function saveAndTouchTranslation(TranslationTransfer $transferTranslation)
    {
        if (is_null($transferTranslation->getIdGlossaryTranslation())) {
            $translationEntity = $this->createAndTouchTranslationFromTransfer($transferTranslation);
            $transferTranslation->setIdGlossaryTranslation($translationEntity->getIdGlossaryTranslation());

            return $transferTranslation;
        } else {
            $translationEntity = $this->getTranslationFromTransfer($transferTranslation);
            $this->doUpdateAndTouchTranslation($translationEntity);

            return $transferTranslation;
        }
    }

    /**
     * @param TranslationTransfer $transferTranslation
     *
     * @return TranslationTransfer
     */
    protected function createTranslationFromTransfer(TranslationTransfer $transferTranslation)
    {
        $newTransferTranslation = $this->createTranslationByIds(
            $transferTranslation->getFkGlossaryKey(),
            $transferTranslation->getFkLocale(),
            $transferTranslation->getValue(),
            $transferTranslation->getIsActive()
        );

        return $newTransferTranslation;
    }

    /**
     * @param TranslationTransfer $transferTranslation
     *
     * @return SpyGlossaryTranslation
     */
    protected function createAndTouchTranslationFromTransfer(TranslationTransfer $transferTranslation)
    {
        Propel::getConnection()->beginTransaction();

        $transferTranslationNew = $this->createTranslationFromTransfer($transferTranslation);

        if ($transferTranslationNew->getIsActive()) {
            $this->insertActiveTouchRecord($transferTranslationNew->getIdGlossaryTranslation());
        }

        Propel::getConnection()->commit();

        return $transferTranslationNew;
    }

    /**
     * @param int $idKey
     * @param int $idLocale
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationByIds($idKey, $idLocale)
    {
        $translation = $this->glossaryQueryContainer
            ->queryTranslationByIds($idKey, $idLocale)
            ->findOne()
        ;

        if (!$translation) {
            throw new MissingTranslationException(
                sprintf('Could not find a translation for keyId %s, localeId %s', $idKey, $idLocale)
            );
        }

        return $translation;
    }

    /**
     * @param TranslationTransfer $transferTranslation
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationFromTransfer(TranslationTransfer $transferTranslation)
    {
        $translation = $this->getTranslationEntityById($transferTranslation->getIdGlossaryTranslation());
        $translation->fromArray($transferTranslation->toArray());

        return $translation;
    }

    /**
     * @param int $idKey
     * @param array $data
     *
     * @return string
     * @throws MissingTranslationException
     */
    public function translateByKeyId($idKey, array $data = [])
    {
        $idLocale = $this->localeFacade->getCurrentLocale()->getIdLocale();
        $translation = $this->getTranslationByIds($idKey, $idLocale);

        return str_replace(array_keys($data), array_values($data), $translation->getValue());
    }

    /**
     * @param string $keyName
     * @param string $value
     * @param bool $isActive
     *
     * @return SpyGlossaryTranslation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     */
    public function createTranslationForCurrentLocale($keyName, $value, $isActive = true)
    {
        $idKey = $this->keyManager->getKey($keyName)->getPrimaryKey();
        $idLocale = $this->localeFacade->getCurrentLocale()->getIdLocale();

        return $this->createTranslationByIds($idKey, $idLocale, $value, $isActive);
    }

    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return SpyGlossaryTranslation
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws TranslationExistsException
     */
    public function createAndTouchTranslation($keyName, LocaleTransfer $locale, $value, $isActive = true)
    {
        Propel::getConnection()->beginTransaction();

        $translation = $this->createTranslation($keyName, $locale, $value, $isActive);
        if ($isActive) {
            $this->insertActiveTouchRecord($translation->getIdGlossaryTranslation());
        }
        Propel::getConnection()->commit();

        return $translation;
    }

    /**
     * @param string $keyName
     * @param LocaleTransfer $locale
     * @param string $value
     * @param bool $isActive
     *
     * @return TranslationTransfer
     * @throws MissingKeyException
     * @throws MissingLocaleException
     * @throws MissingTranslationException
     */
    public function updateAndTouchTranslation($keyName, LocaleTransfer $locale, $value, $isActive = true)
    {
        $translation = $this->getUpdatedTranslationEntity($keyName, $locale, $value, $isActive);

        return $this->doUpdateAndTouchTranslation($translation);
    }

    /**
     * @param SpyGlossaryTranslation $translation

     * @return TranslationTransfer
     */
    protected function doUpdateTranslation(SpyGlossaryTranslation $translation)
    {
        if ($translation->isModified()) {
            $translation->save();
        }

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param SpyGlossaryTranslation $translation

     * @return TranslationTransfer
     * @throws \Exception
     * @throws PropelException
     */
    protected function doUpdateAndTouchTranslation(SpyGlossaryTranslation $translation)
    {
        if (!$translation->isModified()) {
            return $translation;
        }

        Propel::getConnection()->beginTransaction();

        $isActiveModified = $translation->isColumnModified(
            SpyGlossaryTranslationTableMap::COL_IS_ACTIVE
        );

        $translation->save();

        if ($translation->getIsActive()) {
            $this->insertActiveTouchRecord($translation->getIdGlossaryTranslation());
        } elseif ($isActiveModified) {
            $this->insertDeletedTouchRecord($translation->getIdGlossaryTranslation());
        }

        Propel::getConnection()->commit();

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idTranslation
     *
     * @return TranslationTransfer
     * @throws MissingTranslationException
     */
    protected function getTranslationById($idTranslation)
    {
        $translation = $this->getTranslationEntityById($idTranslation);

        return $this->convertEntityToTranslationTransfer($translation);
    }

    /**
     * @param int $idKey
     */
    public function touchCurrentTranslationForKeyId($idKey)
    {
        $idLocale = $this->localeFacade->getCurrentLocale()->getIdLocale();
        $translation = $this->getTranslationByIds($idKey, $idLocale);
        $this->insertActiveTouchRecord($translation->getIdGlossaryTranslation());
    }

    /**
     * @param string $keyName
     * @param string $localeName
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationEntityByNames($keyName, $localeName)
    {
        $translation = $this->glossaryQueryContainer
            ->queryTranslationByNames($keyName, $localeName)
            ->findOne()
        ;
        if (!$translation) {
            throw new MissingTranslationException(
                sprintf('Could not find a translation for key %s, locale %s', $keyName, $localeName)
            );
        }
        return $translation;
    }

    /**
     * @param int $idTranslation
     *
     * @return SpyGlossaryTranslation
     * @throws MissingTranslationException
     */
    protected function getTranslationEntityById($idTranslation)
    {
        $translation = $this->glossaryQueryContainer->queryTranslations()->findPk($idTranslation);
        if (!$translation) {
            throw new MissingTranslationException(
                sprintf('Could not find a translation with id %s', $idTranslation)
            );
        }
        return $translation;
    }
}
