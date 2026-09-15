<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class GlossaryConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * Used as `item_type` for touch mechanism.
     *
     * @var string
     */
    public const RESOURCE_TYPE_TRANSLATION = 'translation';

    /**
     * @var string
     */
    protected const REDIRECT_URL_DEFAULT = '/glossary';

    protected const int GLOSSARY_KEY_COLLECTION_DEFAULT_LIMIT = 100;

    /**
     * @api
     *
     * @return array<string>
     */
    public function getGlossaryFilePaths()
    {
        $sourceGlossary = glob(APPLICATION_SOURCE_DIR . '/*/*/*/Resources/glossary.yml', GLOB_NOSORT);
        if ($sourceGlossary === false) {
            $sourceGlossary = [];
        }

        $vendorGlossary = glob(APPLICATION_VENDOR_DIR . '/*/*/src/*/*/*/Resources/glossary.yml', GLOB_NOSORT);
        if ($vendorGlossary === false) {
            $vendorGlossary = [];
        }

        $paths = array_merge(
            $sourceGlossary,
            $vendorGlossary,
        );

        return $paths;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getDefaultRedirectUrl(): string
    {
        return static::REDIRECT_URL_DEFAULT;
    }

    /**
     * Specification:
     * - Returns the number of glossary keys `GlossaryFacade::getGlossaryKeyCollection()` returns when the criteria carry no pagination limit.
     *
     * @api
     */
    public function getGlossaryKeyCollectionDefaultLimit(): int
    {
        return static::GLOSSARY_KEY_COLLECTION_DEFAULT_LIMIT;
    }
}
