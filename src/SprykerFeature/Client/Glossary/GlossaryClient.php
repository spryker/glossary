<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Client\Glossary;

use SprykerEngine\Client\Kernel\AbstractClient;
use SprykerFeature\Client\Glossary\Storage\GlossaryStorageInterface;

/**
 * @method GlossaryDependencyContainer getDependencyContainer()
 */
class GlossaryClient extends AbstractClient implements GlossaryClientInterface
{

    /**
     * @param string $id
     * @param string $localeName
     * @param array $parameters
     *
     * @return string
     */
    public function translate($id, $localeName, array $parameters = [])
    {
        return $this->createTranslator($localeName)->translate($id, $parameters);
    }

    /**
     * @param $localeName
     *
     * @return GlossaryStorageInterface
     */
    private function createTranslator($localeName)
    {
        return $this->getDependencyContainer()->createTranslator($localeName);
    }

}
