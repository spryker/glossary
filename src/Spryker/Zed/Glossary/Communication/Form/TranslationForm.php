<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Glossary\Communication\Form;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Map\TableMap;
use Orm\Zed\Locale\Persistence\Map\SpyLocaleTableMap;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryTranslationTableMap;
use Orm\Zed\Glossary\Persistence\SpyGlossaryKeyQuery;
use Orm\Zed\Glossary\Persistence\SpyGlossaryTranslationQuery;
use Spryker\Zed\Gui\Communication\Form\AbstractForm;
use Spryker\Zed\Gui\Communication\Form\Type\AutosuggestType;
use Symfony\Component\Form\FormBuilderInterface;

class TranslationForm extends AbstractForm
{

    const UPDATE = 'update';
    const URL_PARAMETER_GLOSSARY_KEY = 'fk-glossary-key';
    const NAME = 'Name';
    const LOCALE = 'translation_locale_name';
    const FIELD_GLOSSARY_KEY = 'glossary_key';
    const FIELD_LOCALES = 'locales';
    const FIELD_VALUE = 'value';
    const TYPE_DATA = 'data';
    const TYPE_DATA_EMPTY = 'empty_data';

    /**
     * @var SpyGlossaryTranslationQuery
     */
    protected $glossaryTranslationQuery;

    /**
     * @var SpyGlossaryKeyQuery
     */
    protected $glossaryKeyQuery;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param \Orm\Zed\Glossary\Persistence\SpyGlossaryTranslationQuery $glossaryTranslationQuery
     * @param \Orm\Zed\Glossary\Persistence\SpyGlossaryKeyQuery $glossaryKeyQuery
     * @param array $locales
     * @param string $type
     */
    public function __construct(SpyGlossaryTranslationQuery $glossaryTranslationQuery, SpyGlossaryKeyQuery $glossaryKeyQuery, array $locales, $type)
    {
        $this->glossaryTranslationQuery = $glossaryTranslationQuery;
        $this->glossaryKeyQuery = $glossaryKeyQuery;
        $this->locales = $locales;
        $this->type = $type;
    }

    /**
     * @return null
     */
    protected function getDataClass()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'translation';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (self::UPDATE === $this->type) {
            $builder->add(self::FIELD_GLOSSARY_KEY, 'text', [
                'label' => self::NAME,
                'attr' => [
                    'readonly' => 'readonly',
                ],
            ]);
        } else {
            $builder->add(self::FIELD_GLOSSARY_KEY, new AutosuggestType(), [
                'label' => self::NAME,
                'url' => '/glossary/key/suggest',
                'constraints' => $this->getFieldDefaultConstraints(),
            ]);
        }

        $builder->add(self::FIELD_LOCALES, 'collection', $this->buildLocaleFieldConfiguration());
    }

    /**
     * @return array
     */
    protected function buildLocaleFieldConfiguration()
    {
        $translationFields = array_fill_keys($this->locales, '');

        $dataTypeField = self::TYPE_DATA_EMPTY;
        if (empty($this->request->get(self::URL_PARAMETER_GLOSSARY_KEY))) {
            $dataTypeField = self::TYPE_DATA;
        }

        return [
            'type' => 'textarea',
            'label' => false,
            $dataTypeField => $translationFields,
            'constraints' => $this->getFieldDefaultConstraints(),
            'options' => [
                'attr' => [
                    'class' => 'html-editor',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getFieldDefaultConstraints()
    {
        return [
            $this->getConstraints()->createConstraintNotBlank(),
            $this->getConstraints()->createConstraintRequired(),
        ];
    }

    /**
     * @return array
     */
    public function populateFormFields()
    {
        $defaultData = [];

        $fkGlossaryKey = $this->getRequest()->query->get(self::URL_PARAMETER_GLOSSARY_KEY);

        if (!empty($fkGlossaryKey)) {
            $glossaryKeyEntity = $this->getGlossaryKey($fkGlossaryKey);
            $defaultData[self::FIELD_GLOSSARY_KEY] = $glossaryKeyEntity->getKey();
        }

        $translationCollection = $this->getGlossaryKeyTranslations($fkGlossaryKey);

        if (!empty($translationCollection)) {
            foreach ($translationCollection as $translation) {
                $defaultData[self::FIELD_LOCALES][$translation[static::LOCALE]] = $translation[self::FIELD_VALUE];
            }
        }

        return $defaultData;
    }

    /**
     * @param int $fkGlossaryKey
     *
     * @return array
     */
    protected function getGlossaryKeyTranslations($fkGlossaryKey)
    {
        return $this->glossaryTranslationQuery
            ->useLocaleQuery(null, Criteria::LEFT_JOIN)
            ->leftJoinSpyGlossaryTranslation(SpyGlossaryTranslationTableMap::TABLE_NAME)
            ->addJoinCondition(SpyGlossaryTranslationTableMap::TABLE_NAME, SpyGlossaryTranslationTableMap::COL_FK_GLOSSARY_KEY . ' = ?', (int) $fkGlossaryKey)
            ->where(SpyLocaleTableMap::COL_LOCALE_NAME . ' IN ?', $this->locales)
            ->groupBy(SpyLocaleTableMap::COL_ID_LOCALE)
            ->withColumn(SpyLocaleTableMap::COL_LOCALE_NAME, self::LOCALE)
            ->withColumn(SpyGlossaryTranslationTableMap::COL_VALUE, self::FIELD_VALUE)
            ->find()
            ->toArray(null, false, TableMap::TYPE_COLNAME);
    }

    /**
     * @param int $fkGlossaryKey
     *
     * @return \Orm\Zed\Glossary\Persistence\SpyGlossaryKey
     */
    protected function getGlossaryKey($fkGlossaryKey)
    {
        return $this->glossaryKeyQuery->findOneByIdGlossaryKey($fkGlossaryKey);
    }

}
