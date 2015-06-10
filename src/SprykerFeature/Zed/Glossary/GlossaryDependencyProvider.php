<?php

namespace SprykerFeature\Zed\Glossary;

use SprykerEngine\Zed\Kernel\AbstractBundleDependencyProvider;
use SprykerEngine\Zed\Kernel\Container;

class GlossaryDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_TOUCH = 'touch facade';

    const FACADE_LOCALE = 'locale facade';

    const PLUGIN_VALIDATOR = 'validator plugin';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[GlossaryDependencyProvider::FACADE_LOCALE] = function (Container $container) {
            return $container->getLocator()->locale()->facade();
        };

        $container[GlossaryDependencyProvider::PLUGIN_VALIDATOR] = function (Container $container) {
            return $container->getLocator()->application()->pluginPimple()->getApplication()['validator'];
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[GlossaryDependencyProvider::FACADE_TOUCH] = function (Container $container) {
            return $container->getLocator()->touch()->facade();
        };

        $container[GlossaryDependencyProvider::FACADE_LOCALE] = function (Container $container) {
            return $container->getLocator()->locale()->facade();
        };

        return $container;
    }

}
