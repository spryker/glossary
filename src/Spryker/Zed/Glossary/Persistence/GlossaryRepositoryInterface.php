<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Persistence;

interface GlossaryRepositoryInterface
{
    /**
     * @param string $glossaryKey
     * @param array $localeIsoCodes
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer[]
     */
    public function getTranslationsByGlossaryKeyAndLocaleIsoCodes(string $glossaryKey, array $localeIsoCodes): array;

    /**
     * @param string[] $glossaryKeys
     * @param string[] $localeIsoCodes
     *
     * @return \Generated\Shared\Transfer\TranslationTransfer[]
     */
    public function getTranslationsByGlossaryKeysAndLocaleIsoCodes(array $glossaryKeys, array $localeIsoCodes): array;

    /**
     * @param string[] $glossaryKeys
     *
     * @return \Generated\Shared\Transfer\GlossaryKeyTransfer[]
     */
    public function getGlossaryKeyTransfersByGlossaryKeys(array $glossaryKeys): array;
}
