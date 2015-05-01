<?php

/*
 * (c) Copyright Spryker Systems GmbH 2015
 */

namespace SprykerFeature\Zed\Glossary\Persistence;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Exception\PropelException;
use SprykerFeature\Zed\Glossary\Persistence\Propel\SpyGlossaryKeyQuery;
use SprykerFeature\Zed\Glossary\Persistence\Propel\SpyGlossaryTranslationQuery;

interface GlossaryQueryContainerInterface
{
    /**
     * @param string $keyName
     *
     * @return SpyGlossaryKeyQuery
     */
    public function queryKey($keyName);

    /**
     * @return SpyGlossaryKeyQuery
     */
    public function queryKeys();

    /**
     * @param string $keyName
     * @param string $localeName
     *
     * @return SpyGlossaryTranslationQuery
     */
    public function queryTranslationByNames($keyName, $localeName);

    /**
     * @param int $idKey
     * @param int $idLocale
     *
     * @return SpyGlossaryTranslationQuery
     */
    public function queryTranslationByIds($idKey, $idLocale);

    /**
     * @return SpyGlossaryTranslationQuery
     */
    public function queryTranslations();

    /**
     * @param string $keyName
     *
     * @return SpyGlossaryTranslationQuery
     */
    public function queryTranslationsByKey($keyName);

    /**
     * @param string $localeName
     *
     * @return SpyGlossaryTranslationQuery
     */
    public function queryTranslationsByLocale($localeName);

    /**
     * @param int $idGlossaryTranslation
     *
     * @return SpyGlossaryTranslationQuery
     */
    public function queryTranslationById($idGlossaryTranslation);

    /**
     * @param SpyGlossaryTranslationQuery $query
     *
     * @return ModelCriteria
     */
    public function joinTranslationQueryWithKeysAndLocales(SpyGlossaryTranslationQuery $query);

    /**
     * @param array $relevantLocales
     *
     * @return ModelCriteria
     * @throws PropelException
     */
    public function queryAllPossibleTranslations(array $relevantLocales);

    /**
     * @param array $relevantLocales
     *
     * @return ModelCriteria
     */
    public function queryAllMissingTranslations(array $relevantLocales);

    /**
     * @param $idKey
     * @param array $relevantLocales
     *
     * @return ModelCriteria
     */
    public function queryMissingTranslationsForKey($idKey, array $relevantLocales);

    /**
     * @param ModelCriteria $query
     *
     * @return ModelCriteria
     */
    public function queryDistinctKeysFromQuery(ModelCriteria $query);

    /**
     * @param ModelCriteria $query
     *
     * @return ModelCriteria
     */
    public function queryDistinctLocalesFromQuery(ModelCriteria $query);
}