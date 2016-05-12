<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Glossary;

use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Spryker\Client\Glossary\GlossaryFactory getFactory()
 */
class GlossaryClient extends AbstractClient implements GlossaryClientInterface
{

    const DEFAULT_CACHE_TTL_IN_SECONDS = 3600;

    /**
     * @api
     *
     * @param string $id
     * @param string $localeName
     * @param array $parameters
     *
     * @return string
     */
    public function translate($id, $localeName, array $parameters = [])
    {
        return $this
            ->getFactory()
            ->createTranslator($localeName)
            ->translate($id, $parameters);
    }

    /**
     * @api
     *
     * @param string $id
     * @param string $localeName
     * @param string $requestCacheKey
     * @param array $parameters
     *
     * @return string
     */
    public function cachedTranslate($id, $localeName, $requestCacheKey, array $parameters = [])
    {
        return $this
            ->getFactory()
            ->createCachedTranslator($localeName, $requestCacheKey)
            ->translate($id, $parameters);
    }

    /**
     * @api
     *
     * @param string $localeName
     * @param string $requestCacheKey
     * @param int|null $ttl
     *
     * @return void
     */
    public function saveCache($localeName, $requestCacheKey, $ttl = null)
    {
        $this
            ->getFactory()
            ->createCachedTranslator($localeName, $requestCacheKey)
            ->saveCache($ttl);
    }

}
