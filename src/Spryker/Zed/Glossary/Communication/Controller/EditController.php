<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Communication\Controller;

use Generated\Shared\Transfer\KeyTranslationTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\Glossary\Communication\GlossaryCommunicationFactory getFactory()
 * @method \Spryker\Zed\Glossary\Business\GlossaryFacadeInterface getFacade()
 * @method \Spryker\Zed\Glossary\Persistence\GlossaryQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Glossary\Persistence\GlossaryRepositoryInterface getRepository()
 */
class EditController extends AbstractController
{
    /**
     * @var string
     */
    public const FORM_UPDATE_TYPE = 'update';

    /**
     * @var string
     */
    public const URL_PARAMETER_GLOSSARY_KEY = 'fk-glossary-key';

    /**
     * @var string
     */
    public const MESSAGE_UPDATE_SUCCESS = 'Translation %d was updated successfully.';

    /**
     * @var string
     */
    public const MESSAGE_UPDATE_ERROR = 'Glossary entry was not updated.';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $idGlossaryKey = $this->castId($request->query->get(static::URL_PARAMETER_GLOSSARY_KEY));

        $formData = $this
            ->getFactory()
            ->createTranslationDataProvider()
            ->getData(
                $idGlossaryKey,
                $this->getFactory()->getEnabledLocales(),
            );

        if ($formData === []) {
            $this->addErrorMessage("Glossary with id %s doesn't exist", ['%s' => $idGlossaryKey]);

            return $this->redirectResponse($this->getFactory()->getConfig()->getDefaultRedirectUrl());
        }

        $glossaryForm = $this
            ->getFactory()
            ->getTranslationUpdateForm($formData);

        $glossaryForm->handleRequest($request);

        if ($glossaryForm->isSubmitted() && $glossaryForm->isValid()) {
            $data = $glossaryForm->getData();

            $keyTranslationTransfer = new KeyTranslationTransfer();
            $keyTranslationTransfer->fromArray($data, true);

            $glossaryFacade = $this->getFacade();

            if ($glossaryFacade->saveGlossaryKeyTranslations($keyTranslationTransfer)) {
                $this->addSuccessMessage(static::MESSAGE_UPDATE_SUCCESS, ['%d' => $idGlossaryKey]);

                return $this->redirectResponse('/glossary');
            }

            $this->addErrorMessage(static::MESSAGE_UPDATE_ERROR);

            return $this->redirectResponse('/glossary');
        }

        return $this->viewResponse([
            'form' => $glossaryForm->createView(),
            'idGlossaryKey' => $idGlossaryKey,
        ]);
    }
}
