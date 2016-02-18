<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Communication\Controller;

use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Orm\Zed\Glossary\Persistence\Map\SpyGlossaryKeyTableMap;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\Glossary\Business\GlossaryFacade getFacade()
 * @method \Spryker\Zed\Glossary\Persistence\GlossaryQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Glossary\Communication\GlossaryCommunicationFactory getFactory()
 */
class AjaxController extends AbstractController
{

    const SEARCH_TERM = 'term';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function keysAction(Request $request)
    {
        $term = $request->query->get(self::SEARCH_TERM);
        $keys = $this->getFactory()
            ->getQueryContainer()
            ->queryActiveKeysByName('%' . $term . '%')
            ->select([
                SpyGlossaryKeyTableMap::COL_KEY,
            ])
            ->find()
            ->toArray();

        return new JsonResponse($keys);
    }

}
