<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Glossary\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\GlossaryKeyConditionsTransfer;
use Generated\Shared\Transfer\GlossaryKeyCriteriaTransfer;
use Generated\Shared\Transfer\GlossaryKeyTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\SortTransfer;
use Spryker\Zed\Glossary\Business\GlossaryBusinessFactory;
use Spryker\Zed\Glossary\Business\GlossaryFacade;
use Spryker\Zed\Glossary\Business\GlossaryFacadeInterface;
use Spryker\Zed\Glossary\Persistence\GlossaryPersistenceFactory;
use Spryker\Zed\Glossary\Persistence\GlossaryRepository;
use Spryker\Zed\Locale\Business\LocaleFacadeInterface;
use SprykerTest\Zed\Glossary\GlossaryBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Glossary
 * @group Business
 * @group Facade
 * @group GetGlossaryKeyCollectionTest
 * Add your own group annotations below this line
 */
class GetGlossaryKeyCollectionTest extends Unit
{
    protected const string KEY_PREFIX = 'glossary-collection-test.';

    protected const string TRANSLATION_VALUE = 'glossary collection test value';

    protected GlossaryBusinessTester $tester;

    public function testGivenKeysConditionWhenGettingCollectionThenOnlyThoseKeysWithAllTranslationsAreReturned(): void
    {
        // Arrange
        $glossaryKey = $this->haveGlossaryKeyWithTranslations();
        $this->haveGlossaryKeyWithTranslations();

        // Act
        $glossaryKeyCollectionTransfer = $this->getGlossaryFacade()->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())->setGlossaryKeyConditions(
                (new GlossaryKeyConditionsTransfer())->addKey($glossaryKey),
            ),
        );

        // Assert
        $this->assertCount(1, $glossaryKeyCollectionTransfer->getGlossaryKeys());
        /** @var \Generated\Shared\Transfer\GlossaryKeyTransfer $glossaryKeyTransfer */
        $glossaryKeyTransfer = $glossaryKeyCollectionTransfer->getGlossaryKeys()->getIterator()->current();
        $this->assertSame($glossaryKey, $glossaryKeyTransfer->getKey());
        $this->assertCount(count($this->getLocaleNames()), $glossaryKeyTransfer->getTranslations(), 'One translation per configured locale must be attached.');
    }

    public function testGivenKeysConditionInDifferentCaseWhenGettingCollectionThenTheStoredKeyIsReturned(): void
    {
        // Arrange
        $glossaryKey = $this->haveGlossaryKeyWithTranslations();

        // Act
        $glossaryKeyCollectionTransfer = $this->getGlossaryFacade()->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())->setGlossaryKeyConditions(
                (new GlossaryKeyConditionsTransfer())->addKey(strtoupper($glossaryKey)),
            ),
        );

        // Assert
        $this->assertCount(1, $glossaryKeyCollectionTransfer->getGlossaryKeys(), 'The keys condition must not depend on the database collation.');
        /** @var \Generated\Shared\Transfer\GlossaryKeyTransfer $glossaryKeyTransfer */
        $glossaryKeyTransfer = $glossaryKeyCollectionTransfer->getGlossaryKeys()->getIterator()->current();
        $this->assertSame($glossaryKey, $glossaryKeyTransfer->getKey());
    }

    public function testGivenKeyFragmentConditionWhenGettingCollectionThenKeysAreMatchedCaseInsensitively(): void
    {
        // Arrange
        $glossaryKey = $this->haveGlossaryKeyWithTranslations();
        $fragment = strtoupper(substr($glossaryKey, strlen(static::KEY_PREFIX)));

        // Act
        $glossaryKeyCollectionTransfer = $this->getGlossaryFacade()->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())->setGlossaryKeyConditions(
                (new GlossaryKeyConditionsTransfer())->setKeyFragment($fragment),
            ),
        );

        // Assert
        $this->assertContains($glossaryKey, $this->extractKeys($glossaryKeyCollectionTransfer->getGlossaryKeys()->getArrayCopy()));
    }

    public function testGivenTranslationValueFragmentConditionWhenGettingCollectionThenOnlyKeysWithActiveMatchingTranslationAreReturned(): void
    {
        // Arrange
        $uniqueValue = uniqid('glossary collection value ', false);
        $activeGlossaryKey = $this->haveGlossaryKeyWithTranslations($uniqueValue);
        $deactivatedGlossaryKey = $this->haveGlossaryKeyWithTranslations($uniqueValue);
        foreach ($this->getLocaleNames() as $localeName) {
            $this->getGlossaryFacade()->deleteTranslation($deactivatedGlossaryKey, (new LocaleTransfer())->setLocaleName($localeName));
        }

        // Act
        $glossaryKeyCollectionTransfer = $this->getGlossaryFacade()->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())->setGlossaryKeyConditions(
                (new GlossaryKeyConditionsTransfer())->setTranslationValueFragment(strtoupper($uniqueValue)),
            ),
        );

        // Assert
        $this->assertSame(
            [$activeGlossaryKey],
            $this->extractKeys($glossaryKeyCollectionTransfer->getGlossaryKeys()->getArrayCopy()),
            'A key is matched once regardless of how many locales match, and deactivated translations are never matched.',
        );
    }

    public function testGivenSortAndPaginationWhenGettingCollectionThenPageIsSelectedInSortedOrderAndTotalIsReported(): void
    {
        // Arrange
        $glossaryKeys = [
            $this->haveGlossaryKeyWithTranslations(null, static::KEY_PREFIX . 'c-' . uniqid('', false)),
            $this->haveGlossaryKeyWithTranslations(null, static::KEY_PREFIX . 'a-' . uniqid('', false)),
            $this->haveGlossaryKeyWithTranslations(null, static::KEY_PREFIX . 'b-' . uniqid('', false)),
        ];
        sort($glossaryKeys);

        // Act
        $glossaryKeyCollectionTransfer = $this->getGlossaryFacade()->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())
                ->setGlossaryKeyConditions((new GlossaryKeyConditionsTransfer())->setKeys($glossaryKeys))
                ->addSort((new SortTransfer())->setField(GlossaryKeyTransfer::KEY)->setIsAscending(false))
                ->setPagination((new PaginationTransfer())->setLimit(1)->setOffset(1)),
        );

        // Assert
        $this->assertSame([$glossaryKeys[1]], $this->extractKeys($glossaryKeyCollectionTransfer->getGlossaryKeys()->getArrayCopy()), 'Descending key sort with offset 1 must return the middle key.');
        $this->assertSame(3, $glossaryKeyCollectionTransfer->getPaginationOrFail()->getNbResults());
    }

    public function testGivenPaginationLimitWithoutOffsetWhenGettingCollectionThenTheFirstPageIsReturned(): void
    {
        // Arrange
        $glossaryKeys = [
            $this->haveGlossaryKeyWithTranslations(null, static::KEY_PREFIX . 'b-' . uniqid('', false)),
            $this->haveGlossaryKeyWithTranslations(null, static::KEY_PREFIX . 'a-' . uniqid('', false)),
            $this->haveGlossaryKeyWithTranslations(null, static::KEY_PREFIX . 'c-' . uniqid('', false)),
        ];
        sort($glossaryKeys);

        // Act
        $glossaryKeyCollectionTransfer = $this->getGlossaryFacade()->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())
                ->setGlossaryKeyConditions((new GlossaryKeyConditionsTransfer())->setKeys($glossaryKeys))
                ->addSort((new SortTransfer())->setField(GlossaryKeyTransfer::KEY)->setIsAscending(true))
                ->setPagination((new PaginationTransfer())->setLimit(2)),
        );

        // Assert
        $this->assertSame([$glossaryKeys[0], $glossaryKeys[1]], $this->extractKeys($glossaryKeyCollectionTransfer->getGlossaryKeys()->getArrayCopy()), 'A limit without an offset must select the first page, not every matching key.');
        $this->assertSame(0, $glossaryKeyCollectionTransfer->getPaginationOrFail()->getOffset());
        $this->assertSame(3, $glossaryKeyCollectionTransfer->getPaginationOrFail()->getNbResults());
    }

    public function testGivenNoPaginationWhenGettingCollectionThenTheConfiguredDefaultLimitIsApplied(): void
    {
        // Arrange
        $glossaryKeys = [
            $this->haveGlossaryKeyWithTranslations(),
            $this->haveGlossaryKeyWithTranslations(),
            $this->haveGlossaryKeyWithTranslations(),
        ];
        $glossaryFacade = $this->createGlossaryFacadeWithDefaultCollectionLimit(2);

        // Act
        $glossaryKeyCollectionTransfer = $glossaryFacade->getGlossaryKeyCollection(
            (new GlossaryKeyCriteriaTransfer())->setGlossaryKeyConditions((new GlossaryKeyConditionsTransfer())->setKeys($glossaryKeys)),
        );

        // Assert
        $this->assertCount(2, $glossaryKeyCollectionTransfer->getGlossaryKeys(), 'Without pagination the configured default limit must cap the collection.');
        $paginationTransfer = $glossaryKeyCollectionTransfer->getPaginationOrFail();
        $this->assertSame(2, $paginationTransfer->getLimit());
        $this->assertSame(0, $paginationTransfer->getOffset());
        $this->assertSame(3, $paginationTransfer->getNbResults());
    }

    protected function createGlossaryFacadeWithDefaultCollectionLimit(int $defaultLimit): GlossaryFacadeInterface
    {
        /** @var \Spryker\Zed\Glossary\GlossaryConfig $glossaryConfigMock */
        $glossaryConfigMock = $this->tester->mockConfigMethod('getGlossaryKeyCollectionDefaultLimit', $defaultLimit);
        $glossaryPersistenceFactory = (new GlossaryPersistenceFactory())->setConfig($glossaryConfigMock);
        $glossaryRepository = (new GlossaryRepository())->setFactory($glossaryPersistenceFactory);
        $glossaryBusinessFactory = (new GlossaryBusinessFactory())->setRepository($glossaryRepository);

        return (new GlossaryFacade())->setFactory($glossaryBusinessFactory);
    }

    protected function haveGlossaryKeyWithTranslations(?string $translationValue = null, ?string $glossaryKey = null): string
    {
        $glossaryKey ??= static::KEY_PREFIX . uniqid('', false);

        $locales = [];
        foreach ($this->getLocaleNames() as $localeName) {
            $locales[$localeName] = $translationValue ?? static::TRANSLATION_VALUE;
        }

        $this->tester->haveTranslation(['glossaryKey' => $glossaryKey, 'locales' => $locales]);

        return $glossaryKey;
    }

    /**
     * @return array<string>
     */
    protected function getLocaleNames(): array
    {
        return array_keys($this->getLocaleFacade()->getLocaleCollection());
    }

    /**
     * @param array<\Generated\Shared\Transfer\GlossaryKeyTransfer> $glossaryKeyTransfers
     *
     * @return array<string>
     */
    protected function extractKeys(array $glossaryKeyTransfers): array
    {
        return array_map(
            static fn (GlossaryKeyTransfer $glossaryKeyTransfer): string => $glossaryKeyTransfer->getKeyOrFail(),
            $glossaryKeyTransfers,
        );
    }

    protected function getGlossaryFacade(): GlossaryFacadeInterface
    {
        return $this->tester->getLocator()->glossary()->facade();
    }

    protected function getLocaleFacade(): LocaleFacadeInterface
    {
        return $this->tester->getLocator()->locale()->facade();
    }
}
