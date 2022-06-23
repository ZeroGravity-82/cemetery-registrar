<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumRepository;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumList;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalColumbariumFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmColumbariumRepository;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalColumbariumFetcherIntegrationTest extends FetcherIntegrationTest
{
    private ColumbariumRepository $columbariumRepo;
    private ColumbariumFetcher    $columbariumFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->columbariumRepo    = new DoctrineOrmColumbariumRepository($this->entityManager);
        $this->columbariumFetcher = new DoctrineDbalColumbariumFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItReturnsColumbariumViewById(): void
    {
        $this->testItReturnsColumbariumViewForC001();
        $this->testItReturnsColumbariumViewForC002();
        $this->testItReturnsColumbariumViewForC003();
    }

    public function testItFailsToReturnColumbariumViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundColumbariumById('unknown_id');
        $this->columbariumFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnColumbariumViewForRemovedColumbarium(): void
    {
        // Prepare database table for testing
        $columbariumToRemove = $this->columbariumRepo->findById(new ColumbariumId('C002'));
        $this->columbariumRepo->remove($columbariumToRemove);
        $removedColumbariumId = $columbariumToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundColumbariumById($removedColumbariumId);
        $this->columbariumFetcher->getViewById($removedColumbariumId);
    }

    public function testItReturnsColumbariumListItems(): void
    {
        // All at once
        $listForAll = $this->columbariumFetcher->findAll();
        $this->assertInstanceOf(ColumbariumList::class, $listForAll);
        $this->assertIsArray($listForAll->listItems);
        $this->assertContainsOnlyInstancesOf(ColumbariumListItem::class, $listForAll->listItems);
        $this->assertCount(4, $listForAll->listItems);
        $this->assertListItemEqualsC003($listForAll->listItems[0]);  // Items are ordered by name
        $this->assertListItemEqualsC001($listForAll->listItems[1]);
        $this->assertListItemEqualsC004($listForAll->listItems[2]);
        $this->assertListItemEqualsC002($listForAll->listItems[3]);
    }

    public function testItReturnsColumbariumTotalCount(): void
    {
        $this->assertSame(4, $this->columbariumFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedColumbariumWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $columbariumToRemove = $this->columbariumRepo->findById(new ColumbariumId('C002'));
        $this->columbariumRepo->remove($columbariumToRemove);

        // Testing itself
        $this->assertSame(3, $this->columbariumFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            ColumbariumFixtures::class,
        ]);
    }

    private function assertListItemEqualsC001(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C001',     $listItem->id);
        $this->assertSame('западный', $listItem->name);
    }

    private function assertListItemEqualsC002(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C002',  $listItem->id);
        $this->assertSame('южный', $listItem->name);
    }

    private function assertListItemEqualsC003(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C003',      $listItem->id);
        $this->assertSame('восточный', $listItem->name);
    }

    private function assertListItemEqualsC004(ColumbariumListItem $listItem): void
    {
        $this->assertSame('C004',     $listItem->id);
        $this->assertSame('северный', $listItem->name);
    }

    private function testItReturnsColumbariumViewForC001(): void
    {
        $view = $this->columbariumFetcher->getViewById('C001');
        $this->assertInstanceOf(ColumbariumView::class, $view);
        $this->assertSame('C001',     $view->id);
        $this->assertSame('западный', $view->name);
        $this->assertSame(null,       $view->geoPositionLatitude);
        $this->assertSame(null,       $view->geoPositionLongitude);
        $this->assertSame(null,       $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumViewForC002(): void
    {
        $view = $this->columbariumFetcher->getViewById('C002');
        $this->assertInstanceOf(ColumbariumView::class, $view);
        $this->assertSame('C002',        $view->id);
        $this->assertSame('южный',       $view->name);
        $this->assertSame('54.95035712', $view->geoPositionLatitude);
        $this->assertSame('82.79252',    $view->geoPositionLongitude);
        $this->assertSame('0.5',         $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsColumbariumViewForC003(): void
    {
        $view = $this->columbariumFetcher->getViewById('C003');
        $this->assertInstanceOf(ColumbariumView::class, $view);
        $this->assertSame('C003',         $view->id);
        $this->assertSame('восточный',    $view->name);
        $this->assertSame('-50.95',       $view->geoPositionLatitude);
        $this->assertSame('-179.7972252', $view->geoPositionLongitude);
        $this->assertSame(null,           $view->geoPositionError);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function expectExceptionForNotFoundColumbariumById(string $columbariumId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Колумбарий с ID "%s" не найден.', $columbariumId));
    }
}
