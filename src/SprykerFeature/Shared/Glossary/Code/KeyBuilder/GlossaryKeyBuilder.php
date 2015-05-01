<?php

namespace SprykerFeature\Shared\Glossary\Code\KeyBuilder;



use SprykerFeature\Shared\FrontendExporter\Code\KeyBuilder\KeyBuilderTrait;

trait GlossaryKeyBuilder
{
    use KeyBuilderTrait;

    /**
     * @param string $glossaryKey
     *
     * @return string
     */
    protected function buildKey($glossaryKey)
    {
        return 'translation.' . $glossaryKey;
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'glossary';
    }
}
