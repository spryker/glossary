<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Business\Key;

use Generated\Shared\Transfer\GlossaryKeyCollectionTransfer;
use Generated\Shared\Transfer\GlossaryKeyCriteriaTransfer;

interface KeyReaderInterface
{
    public function getGlossaryKeyCollection(GlossaryKeyCriteriaTransfer $glossaryKeyCriteriaTransfer): GlossaryKeyCollectionTransfer;

    /**
     * @param array<string> $glossaryKeys
     *
     * @return array<\Generated\Shared\Transfer\GlossaryKeyTransfer>
     */
    public function getGlossaryKeyTransfersByGlossaryKeys(array $glossaryKeys): array;
}
