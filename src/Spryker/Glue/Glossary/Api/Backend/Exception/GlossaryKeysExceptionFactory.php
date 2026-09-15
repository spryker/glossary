<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Glossary\Api\Backend\Exception;

use Spryker\ApiPlatform\Exception\GlueApiException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GlossaryKeysExceptionFactory
{
    protected const string MESSAGE_GLOSSARY_KEY_NOT_FOUND = 'Glossary key "%s" not found.';

    protected const string MESSAGE_UNSUPPORTED_SORT_FIELD = 'Sorting by "%s" is not supported. Supported sort fields: %s.';

    public function createGlossaryKeyNotFoundException(string $key): NotFoundHttpException
    {
        return new NotFoundHttpException(sprintf(static::MESSAGE_GLOSSARY_KEY_NOT_FOUND, $key));
    }

    /**
     * @param array<string> $supportedSortFields
     */
    public function createUnsupportedSortFieldException(string $sortField, array $supportedSortFields): GlueApiException
    {
        return new GlueApiException(
            statusCode: Response::HTTP_BAD_REQUEST,
            message: sprintf(static::MESSAGE_UNSUPPORTED_SORT_FIELD, $sortField, implode(', ', $supportedSortFields)),
        );
    }

    /**
     * @param array<string> $errorMessages
     */
    public function createValidationException(array $errorMessages): GlueApiException
    {
        $errors = [];
        foreach ($errorMessages as $errorMessage) {
            $errors[] = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'detail' => $errorMessage,
            ];
        }

        return (new GlueApiException(
            statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            message: $errorMessages[0] ?? '',
        ))->setErrors($errors);
    }
}
