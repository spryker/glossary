<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Communication\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method \Spryker\Zed\Glossary\Business\GlossaryFacadeInterface getFacade()
 * @method \Spryker\Zed\Glossary\Communication\GlossaryCommunicationFactory getFactory()
 * @method \Spryker\Zed\Glossary\Persistence\GlossaryQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Glossary\GlossaryConfig getConfig()
 * @method \Spryker\Zed\Glossary\Persistence\GlossaryRepositoryInterface getRepository()
 */
class UpdateTranslationForm extends TranslationForm
{
    /**
     * @var string
     */
    public const TYPE_DATA_EMPTY = 'empty_data';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addGlossaryKeyField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_GLOSSARY_KEY, TextType::class, [
            'label' => 'Name',
            'attr' => [
                'readonly' => 'readonly',
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $locales
     *
     * @return $this
     */
    protected function addLocaleCollection(FormBuilderInterface $builder, array $locales)
    {
        $builder->add(static::FIELD_LOCALES, CollectionType::class, $this->buildLocaleFieldConfiguration(static::TYPE_DATA_EMPTY, $locales));

        return $this;
    }
}
