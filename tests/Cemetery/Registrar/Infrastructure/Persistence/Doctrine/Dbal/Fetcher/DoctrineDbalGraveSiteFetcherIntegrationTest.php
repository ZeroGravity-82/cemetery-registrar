<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalGraveSiteFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmGraveSiteRepository;
use DataFixtures\BurialPlace\GraveSite\CemeteryBlockFixtures;
use DataFixtures\BurialPlace\GraveSite\GraveSiteFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalGraveSiteFetcherIntegrationTest extends FetcherIntegrationTest
{
    private const DEFAULT_PAGE_SIZE = 20;

    private GraveSiteRepository $graveSiteRepo;
    private GraveSiteFetcher    $graveSiteFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->graveSiteRepo    = new DoctrineOrmGraveSiteRepository($this->entityManager);
        $this->graveSiteFetcher = new DoctrineDbalGraveSiteFetcher($this->connection);
        $this->loadFixtures();
    }

    public function testItHasValidPageSizeConstant(): void
    {
        $this->assertSame(self::DEFAULT_PAGE_SIZE, GraveSiteFetcher::DEFAULT_PAGE_SIZE);
    }

    public function testItReturnsGraveSiteViewById(): void
    {
        $this->testItReturnsGraveSiteViewForGS001();
        $this->testItReturnsGraveSiteViewForGS002();
        $this->testItReturnsGraveSiteViewForGS003();
        $this->testItReturnsGraveSiteViewForGS004();
        $this->testItReturnsGraveSiteViewForGS005();
        $this->testItReturnsGraveSiteViewForGS006();
        $this->testItReturnsGraveSiteViewForGS007();
        $this->testItReturnsGraveSiteViewForGS008();
        $this->testItReturnsGraveSiteViewForGS009();
    }

    public function testItFailsToReturnGraveSiteViewByUnknownId(): void
    {
        $this->expectExceptionForNotFoundGraveSiteById('unknown_id');
        $this->graveSiteFetcher->getViewById('unknown_id');
    }

    public function testItFailsToReturnGraveSiteViewForRemovedGraveSite(): void
    {
        // Prepare database table for testing
        $graveSiteToRemove = $this->graveSiteRepo->findById(new GraveSiteId('GS004'));
        $this->graveSiteRepo->remove($graveSiteToRemove);
        $removedGraveSiteId = $graveSiteToRemove->id()->value();

        // Testing itself
        $this->expectExceptionForNotFoundGraveSiteById($removedGraveSiteId);
        $this->graveSiteFetcher->getViewById($removedGraveSiteId);
    }

    public function testItReturnsGraveSiteListItemsByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->graveSiteFetcher->findAll(1, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForFirstPage->listItems);
        $this->assertCount(4,              $listForFirstPage->listItems);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(9,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertListItemEqualsGS001($listForFirstPage->listItems[0]);  // Items are ordered by cemetery block name,
        $this->assertListItemEqualsGS004($listForFirstPage->listItems[1]);  // then by row in block, then by position in row
        $this->assertListItemEqualsGS006($listForFirstPage->listItems[2]);  // and finally by grave site ID.
        $this->assertListItemEqualsGS005($listForFirstPage->listItems[3]);

        // Second page
        $listForSecondPage = $this->graveSiteFetcher->findAll(2, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForSecondPage->listItems);
        $this->assertCount(4,              $listForSecondPage->listItems);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(9,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertListItemEqualsGS002($listForSecondPage->listItems[0]);
        $this->assertListItemEqualsGS007($listForSecondPage->listItems[1]);
        $this->assertListItemEqualsGS008($listForSecondPage->listItems[2]);
        $this->assertListItemEqualsGS009($listForSecondPage->listItems[3]);


        // Third page
        $listForThirdPage = $this->graveSiteFetcher->findAll(3, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForThirdPage->listItems);
        $this->assertCount(1,              $listForThirdPage->listItems);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(9,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertListItemEqualsGS003($listForThirdPage->listItems[0]);

        // Fourth page
        $listForFourthPage = $this->graveSiteFetcher->findAll(4, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->listItems);
        $this->assertCount(0,              $listForFourthPage->listItems);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(9,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->graveSiteFetcher->findAll(1);
        $this->assertInstanceOf(GraveSiteList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForDefaultPageSize->listItems);
        $this->assertCount(9,                      $listForDefaultPageSize->listItems);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(9,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsGraveSiteListItemsByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->graveSiteFetcher->findAll(1, 'общ', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('общ',           $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->graveSiteFetcher->findAll(2, 'общ', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('общ',            $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->graveSiteFetcher->findAll(1, 'Мусуль', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('Мусуль',        $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->graveSiteFetcher->findAll(1, '3', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('3',             $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->graveSiteFetcher->findAll(2, '3', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(3,              $list->listItems);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('3',             $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->graveSiteFetcher->findAll(3, '3', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(0,              $list->listItems);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('3',             $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->graveSiteFetcher->findAll(1, '4', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('4',             $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->graveSiteFetcher->findAll(1, '.5', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->listItems);
        $this->assertCount(2,              $list->listItems);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('.5',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsGraveSiteTotalCount(): void
    {
        $this->assertSame(9, $this->graveSiteFetcher->countTotal());
    }

    public function testItDoesNotCountRemovedGraveSiteWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $graveSiteToRemove = $this->graveSiteRepo->findById(new GraveSiteId('GS004'));
        $this->graveSiteRepo->remove($graveSiteToRemove);

        // Testing itself
        $this->assertSame(8, $this->graveSiteFetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            CemeteryBlockFixtures::class,
            GraveSiteFixtures::class,
        ]);
    }

    private function assertListItemEqualsGS001(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS001',                                     $listItem->id);
        $this->assertSame('воинский',                                  $listItem->cemeteryBlockName);
        $this->assertSame(1,                                           $listItem->rowInBlock);
        $this->assertSame(null,                                        $listItem->positionInRow);
        $this->assertSame(null,                                        $listItem->size);
    }

    private function assertListItemEqualsGS002(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS002',                                     $listItem->id);
        $this->assertSame('общий А',                                   $listItem->cemeteryBlockName);
        $this->assertSame(3,                                           $listItem->rowInBlock);
        $this->assertSame(4,                                           $listItem->positionInRow);
        $this->assertSame(null,                                        $listItem->size);
    }

    private function assertListItemEqualsGS003(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS003',                                     $listItem->id);
        $this->assertSame('общий Б',                                   $listItem->cemeteryBlockName);
        $this->assertSame(7,                                           $listItem->rowInBlock);
        $this->assertSame(null,                                        $listItem->positionInRow);
        $this->assertSame('2.5',                                       $listItem->size);
    }

    private function assertListItemEqualsGS004(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS004',                                     $listItem->id);
        $this->assertSame('мусульманский',                             $listItem->cemeteryBlockName);
        $this->assertSame(2,                                           $listItem->rowInBlock);
        $this->assertSame(4,                                           $listItem->positionInRow);
        $this->assertSame(null,                                        $listItem->size);
    }

    private function assertListItemEqualsGS005(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS005',                                     $listItem->id);
        $this->assertSame('мусульманский',                             $listItem->cemeteryBlockName);
        $this->assertSame(3,                                           $listItem->rowInBlock);
        $this->assertSame(11,                                          $listItem->positionInRow);
        $this->assertSame(null,                                        $listItem->size);
    }

    private function assertListItemEqualsGS006(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS006',                                     $listItem->id);
        $this->assertSame('мусульманский',                             $listItem->cemeteryBlockName);
        $this->assertSame(3,                                           $listItem->rowInBlock);
        $this->assertSame(10,                                          $listItem->positionInRow);
        $this->assertSame(null,                                        $listItem->size);
    }


    private function assertListItemEqualsGS007(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS007',                                     $listItem->id);
        $this->assertSame('общий А',                                   $listItem->cemeteryBlockName);
        $this->assertSame(3,                                           $listItem->rowInBlock);
        $this->assertSame(5,                                           $listItem->positionInRow);
        $this->assertSame(null,                                        $listItem->size);
    }

    private function assertListItemEqualsGS008(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS008',                                     $listItem->id);
        $this->assertSame('общий А',                                   $listItem->cemeteryBlockName);
        $this->assertSame(3,                                           $listItem->rowInBlock);
        $this->assertSame(6,                                           $listItem->positionInRow);
        $this->assertSame(null,                                        $listItem->size);
    }

    private function assertListItemEqualsGS009(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS009',                                     $listItem->id);
        $this->assertSame('общий А',                                   $listItem->cemeteryBlockName);
        $this->assertSame(3,                                           $listItem->rowInBlock);
        $this->assertSame(7,                                           $listItem->positionInRow);
        $this->assertSame('3.5',                                       $listItem->size);
    }

    private function testItReturnsGraveSiteViewForGS001(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS001');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS001',                                     $view->id);
        $this->assertSame('CB001',                                     $view->cemeteryBlockId);
        $this->assertSame(1,                                           $view->rowInBlock);
        $this->assertSame(null,                                        $view->positionInRow);
        $this->assertSame(null,                                        $view->geoPositionLatitude);
        $this->assertSame(null,                                        $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame(null,                                        $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS002(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS002');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS002',                                     $view->id);
        $this->assertSame('CB002',                                     $view->cemeteryBlockId);
        $this->assertSame(3,                                           $view->rowInBlock);
        $this->assertSame(4,                                           $view->positionInRow);
        $this->assertSame('54.950357',                                 $view->geoPositionLatitude);
        $this->assertSame('82.7972252',                                $view->geoPositionLongitude);
        $this->assertSame('0.5',                                       $view->geoPositionError);
        $this->assertSame(null,                                        $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS003(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS003');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS003',                                     $view->id);
        $this->assertSame('CB003',                                     $view->cemeteryBlockId);
        $this->assertSame(7,                                           $view->rowInBlock);
        $this->assertSame(null,                                        $view->positionInRow);
        $this->assertSame('50.950357',                                 $view->geoPositionLatitude);
        $this->assertSame('80.7972252',                                $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame('2.5',                                       $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS004(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS004');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS004',                                     $view->id);
        $this->assertSame('CB004',                                     $view->cemeteryBlockId);
        $this->assertSame(2,                                           $view->rowInBlock);
        $this->assertSame(4,                                           $view->positionInRow);
        $this->assertSame(null,                                        $view->geoPositionLatitude);
        $this->assertSame(null,                                        $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame(null,                                        $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS005(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS005');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS005',                                     $view->id);
        $this->assertSame('CB004',                                     $view->cemeteryBlockId);
        $this->assertSame(3,                                           $view->rowInBlock);
        $this->assertSame(11,                                          $view->positionInRow);
        $this->assertSame(null,                                        $view->geoPositionLatitude);
        $this->assertSame(null,                                        $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame(null,                                        $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS006(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS006');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS006',                                     $view->id);
        $this->assertSame('CB004',                                     $view->cemeteryBlockId);
        $this->assertSame(3,                                           $view->rowInBlock);
        $this->assertSame(10,                                          $view->positionInRow);
        $this->assertSame(null,                                        $view->geoPositionLatitude);
        $this->assertSame(null,                                        $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame(null,                                        $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS007(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS007');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS007',                                     $view->id);
        $this->assertSame('CB002',                                     $view->cemeteryBlockId);
        $this->assertSame(3,                                           $view->rowInBlock);
        $this->assertSame(5,                                           $view->positionInRow);
        $this->assertSame(null,                                        $view->geoPositionLatitude);
        $this->assertSame(null,                                        $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame(null,                                        $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS008(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS008');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS008',                                     $view->id);
        $this->assertSame('CB002',                                     $view->cemeteryBlockId);
        $this->assertSame(3,                                           $view->rowInBlock);
        $this->assertSame(6,                                           $view->positionInRow);
        $this->assertSame(null,                                        $view->geoPositionLatitude);
        $this->assertSame(null,                                        $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame(null,                                        $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS009(): void
    {
        $view = $this->graveSiteFetcher->getViewById('GS009');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS009',                                     $view->id);
        $this->assertSame('CB002',                                     $view->cemeteryBlockId);
        $this->assertSame(3,                                           $view->rowInBlock);
        $this->assertSame(7,                                           $view->positionInRow);
        $this->assertSame(null,                                        $view->geoPositionLatitude);
        $this->assertSame(null,                                        $view->geoPositionLongitude);
        $this->assertSame(null,                                        $view->geoPositionError);
        $this->assertSame('3.5',                                       $view->size);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function expectExceptionForNotFoundGraveSiteById(string $graveSiteId): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Участок на кладбище с ID "%s" не найден.', $graveSiteId));
    }
}
