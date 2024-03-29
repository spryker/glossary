<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Glossary\Business\Internal;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Glossary\Business\Key\KeyManagerInterface;
use Spryker\Zed\Glossary\Business\Translation\TranslationManagerInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Symfony\Component\Yaml\Yaml;

class GlossaryInstaller implements GlossaryInstallerInterface
{
    use TransactionTrait;

    /**
     * @var \Spryker\Zed\Glossary\Business\Translation\TranslationManagerInterface
     */
    protected $translationManager;

    /**
     * @var \Spryker\Zed\Glossary\Business\Key\KeyManagerInterface
     */
    protected $keyManager;

    /**
     * @var array
     */
    protected $paths;

    /**
     * @param \Spryker\Zed\Glossary\Business\Translation\TranslationManagerInterface $translationManager
     * @param \Spryker\Zed\Glossary\Business\Key\KeyManagerInterface $keyManager
     * @param array $paths
     */
    public function __construct(
        TranslationManagerInterface $translationManager,
        KeyManagerInterface $keyManager,
        array $paths = []
    ) {
        $this->translationManager = $translationManager;
        $this->keyManager = $keyManager;
        $this->paths = $paths;
    }

    /**
     * @return array
     */
    public function install()
    {
        $results = [];

        foreach ($this->paths as $filePath) {
            $translations = $this->parseYamlFile($filePath);
            $result = $this->installKeysAndTranslations($translations);
            $results[$filePath] = $result;
        }

        return $results;
    }

    /**
     * @param string $filePath
     *
     * @return array
     */
    protected function parseYamlFile($filePath)
    {
        $yamlParser = new Yaml();

        /** @var string $fileContent */
        $fileContent = file_get_contents($filePath);

        return $yamlParser->parse($fileContent);
    }

    /**
     * @param array $translations
     *
     * @return array
     */
    protected function installKeysAndTranslations(array $translations)
    {
        return $this->getTransactionHandler()->handleTransaction(function () use ($translations): array {
            return $this->executeInstallKeysAndTranslationsTransaction($translations);
        });
    }

    /**
     * @param array $translations
     *
     * @return array
     */
    protected function executeInstallKeysAndTranslationsTransaction(array $translations): array
    {
        $results = [];
        foreach ($translations as $keyName => $data) {
            $results[$keyName]['created'] = false;
            if (!$this->keyManager->hasKey($keyName)) {
                $this->keyManager->createKey($keyName);
                $results[$keyName]['created'] = true;
            }

            foreach ($data['translations'] as $localeName => $text) {
                $locale = new LocaleTransfer();
                $locale->setLocaleName($localeName);
                $results[$keyName]['translation'][$localeName]['text'] = $text;
                $results[$keyName]['translation'][$localeName]['created'] = false;
                $results[$keyName]['translation'][$localeName]['updated'] = false;

                if (!$this->translationManager->hasTranslation($keyName, $locale)) {
                    $this->translationManager->createAndTouchTranslation($keyName, $locale, $text, true);
                    $results[$keyName]['translation'][$localeName]['created'] = true;
                } elseif ($this->translationManager->getTranslationByKeyName($keyName, $locale)->getValue() !== $text) {
                    $this->translationManager->updateAndTouchTranslation($keyName, $locale, $text, true);
                    $results[$keyName]['translation'][$localeName]['updated'] = true;
                }
            }
        }

        return $results;
    }
}
