<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Processor;

use Generated\Api\Backend\GlossaryKeysBackendResource;
use Spryker\ApiPlatform\State\Processor\AbstractBackendProcessor;
use Spryker\Glue\Glossary\Api\Backend\Exception\GlossaryKeysExceptionFactory;
use Spryker\Glue\Glossary\Api\Backend\Reader\GlossaryKeyReaderInterface;
use Spryker\Glue\Glossary\Api\Backend\Validator\GlossaryKeyWriteValidatorInterface;
use Spryker\Glue\Glossary\Api\Backend\Writer\GlossaryKeyWriterInterface;

class GlossaryKeysBackendProcessor extends AbstractBackendProcessor
{
    protected const string URI_VARIABLE_KEY = 'key';

    public function __construct(
        protected GlossaryKeyReaderInterface $glossaryKeyReader,
        protected GlossaryKeyWriteValidatorInterface $glossaryKeyWriteValidator,
        protected GlossaryKeyWriterInterface $glossaryKeyWriter,
        protected GlossaryKeysExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): GlossaryKeysBackendResource
    {
        /** @var \Generated\Api\Backend\GlossaryKeysBackendResource $glossaryKeysBackendResource */
        $glossaryKeysBackendResource = $data;
        $key = (string)$glossaryKeysBackendResource->key;

        $localeTransfersIndexedByLocaleName = $this->glossaryKeyReader->getLocaleTransfersIndexedByLocaleName();

        $errors = $this->glossaryKeyWriteValidator->validateCreate(
            $glossaryKeysBackendResource,
            $this->glossaryKeyReader->findGlossaryKeyTransferByKey($key),
            array_keys($localeTransfersIndexedByLocaleName),
        );
        if ($errors !== []) {
            throw $this->exceptionFactory->createValidationException($errors);
        }

        $this->glossaryKeyWriter->createGlossaryKey(
            $key,
            $glossaryKeysBackendResource->translations ?? [],
            $localeTransfersIndexedByLocaleName,
        );

        return $this->getGlossaryKeyResourceOrFail($key);
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPatch(mixed $data): GlossaryKeysBackendResource
    {
        /** @var \Generated\Api\Backend\GlossaryKeysBackendResource $glossaryKeysBackendResource */
        $glossaryKeysBackendResource = $data;
        $key = (string)$this->getUriVariable(static::URI_VARIABLE_KEY);

        $currentGlossaryKeyTransfer = $this->glossaryKeyReader->findGlossaryKeyTransferByKey($key);
        if ($currentGlossaryKeyTransfer === null) {
            throw $this->exceptionFactory->createGlossaryKeyNotFoundException($key);
        }

        $localeTransfersIndexedByLocaleName = $this->glossaryKeyReader->getLocaleTransfersIndexedByLocaleName();

        $errors = $this->glossaryKeyWriteValidator->validateUpdate(
            $glossaryKeysBackendResource,
            $currentGlossaryKeyTransfer,
            array_keys($localeTransfersIndexedByLocaleName),
        );
        if ($errors !== []) {
            throw $this->exceptionFactory->createValidationException($errors);
        }

        $this->glossaryKeyWriter->updateGlossaryKeyTranslations(
            $currentGlossaryKeyTransfer,
            $glossaryKeysBackendResource->translations ?? [],
            $localeTransfersIndexedByLocaleName,
        );

        return $this->getGlossaryKeyResourceOrFail($currentGlossaryKeyTransfer->getKeyOrFail());
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getGlossaryKeyResourceOrFail(string $key): GlossaryKeysBackendResource
    {
        $glossaryKeysBackendResource = $this->glossaryKeyReader->findGlossaryKeyResourceByKey($key);
        if ($glossaryKeysBackendResource === null) {
            throw $this->exceptionFactory->createGlossaryKeyNotFoundException($key);
        }

        return $glossaryKeysBackendResource;
    }
}
