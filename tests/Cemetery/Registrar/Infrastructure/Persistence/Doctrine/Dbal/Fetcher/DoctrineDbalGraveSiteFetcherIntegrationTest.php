<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepositoryInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteList;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteListItem;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteView;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher\DoctrineDbalGraveSiteFetcher;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmGraveSiteRepository;
use DataFixtures\BurialPlace\GraveSite\CemeteryBlockFixtures;
use DataFixtures\BurialPlace\GraveSite\GraveSiteFixtures;
use DataFixtures\NaturalPerson\NaturalPersonFixtures;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalGraveSiteFetcherIntegrationTest extends AbstractDoctrineDbalFetcherIntegrationTest
{
    private GraveSiteRepositoryInterface $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmGraveSiteRepository($this->entityManager);
        $this->fetcher = new DoctrineDbalGraveSiteFetcher($this->connection);
        $this->loadFixtures();
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

    public function testItReturnsNullForRemovedGraveSite(): void
    {
        // Prepare database table for testing
        $graveSiteToRemove = $this->repo->findById(new GraveSiteId('GS004'));
        $this->repo->remove($graveSiteToRemove);
        $removedGraveSiteId = $graveSiteToRemove->id()->value();

        // Testing itself
        $view = $this->fetcher->findViewById($removedGraveSiteId);
        $this->assertNull($view);
    }

    public function testItChecksExistenceById(): void
    {
        // Prepare database table for testing
        $graveSiteToRemove = $this->repo->findById(new GraveSiteId('GS004'));
        $this->repo->remove($graveSiteToRemove);
        $removedGraveSiteId = $graveSiteToRemove->id()->value();

        $this->assertTrue($this->fetcher->doesExistById('GS001'));
        $this->assertFalse($this->fetcher->doesExistById('unknown_id'));
        $this->assertFalse($this->fetcher->doesExistById($removedGraveSiteId));
    }

    public function testItChecksExistenceByCemeteryBlockIdAndRowInBlockAndPositionInRow(): void
    {
        $this->markTestIncomplete();
        $graveSiteToRemove = $this->repo->findById(new GraveSiteId('GS004'));
        $this->repo->remove($graveSiteToRemove);
        $removedGraveSiteCemeteryBlockId = $graveSiteToRemove->cemeteryBlockId()->value();
        $removedGraveSiteRowInBlock      = $graveSiteToRemove->rowInBlock()->value();
        $removedGraveSitePositionInRow   = $graveSiteToRemove->positionInRow()?->value();

        $this->assertTrue($this->fetcher->doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
            'CB002',
            3,
            4));
        $this->assertFalse($this->fetcher->doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
            'unknown_cemetery_block_id',
            3,
            4,
        ));
        $this->assertFalse($this->fetcher->doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
            'CB002',
            777,        // non-existing row in block
            4,
        ));
        $this->assertFalse($this->fetcher->doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
            'CB002',
            3,
            777,        // non-existing position in row
        ));
        $this->assertFalse($this->fetcher->doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
            $removedGraveSiteCemeteryBlockId,
            $removedGraveSiteRowInBlock,
            $removedGraveSitePositionInRow,
        ));
        $this->assertFalse($this->fetcher->doesAlreadyUsedCemeteryBlockIdAndRowInBlockAndPositionInRow(
            'CB002',
            3,
            null,
        ));
    }

    public function testItReturnsGraveSitePaginatedListByPage(): void
    {
        $customPageSize = 4;

        // First page
        $listForFirstPage = $this->fetcher->paginate(1, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForFirstPage);
        $this->assertIsArray($listForFirstPage->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForFirstPage->items);
        $this->assertCount(4,              $listForFirstPage->items);
        $this->assertSame(1,               $listForFirstPage->page);
        $this->assertSame($customPageSize, $listForFirstPage->pageSize);
        $this->assertSame(null,            $listForFirstPage->term);
        $this->assertSame(9,               $listForFirstPage->totalCount);
        $this->assertSame(3,               $listForFirstPage->totalPages);
        $this->assertPaginatedListItemEqualsGS001($listForFirstPage->items[0]);  // Items are ordered by cemetery block name,
        $this->assertPaginatedListItemEqualsGS004($listForFirstPage->items[1]);  // then by row in block, then by position in row
        $this->assertPaginatedListItemEqualsGS006($listForFirstPage->items[2]);  // and finally by grave site ID.
        $this->assertPaginatedListItemEqualsGS005($listForFirstPage->items[3]);

        // Second page
        $listForSecondPage = $this->fetcher->paginate(2, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForSecondPage);
        $this->assertIsArray($listForSecondPage->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForSecondPage->items);
        $this->assertCount(4,              $listForSecondPage->items);
        $this->assertSame(2,               $listForSecondPage->page);
        $this->assertSame($customPageSize, $listForSecondPage->pageSize);
        $this->assertSame(null,            $listForSecondPage->term);
        $this->assertSame(9,               $listForSecondPage->totalCount);
        $this->assertSame(3,               $listForSecondPage->totalPages);
        $this->assertPaginatedListItemEqualsGS002($listForSecondPage->items[0]);
        $this->assertPaginatedListItemEqualsGS007($listForSecondPage->items[1]);
        $this->assertPaginatedListItemEqualsGS008($listForSecondPage->items[2]);
        $this->assertPaginatedListItemEqualsGS009($listForSecondPage->items[3]);


        // Third page
        $listForThirdPage = $this->fetcher->paginate(3, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForThirdPage);
        $this->assertIsArray($listForThirdPage->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForThirdPage->items);
        $this->assertCount(1,              $listForThirdPage->items);
        $this->assertSame(3,               $listForThirdPage->page);
        $this->assertSame($customPageSize, $listForThirdPage->pageSize);
        $this->assertSame(null,            $listForThirdPage->term);
        $this->assertSame(9,               $listForThirdPage->totalCount);
        $this->assertSame(3,               $listForThirdPage->totalPages);
        $this->assertPaginatedListItemEqualsGS003($listForThirdPage->items[0]);

        // Fourth page
        $listForFourthPage = $this->fetcher->paginate(4, null, $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $listForFourthPage);
        $this->assertIsArray($listForFourthPage->items);
        $this->assertCount(0,              $listForFourthPage->items);
        $this->assertSame(4,               $listForFourthPage->page);
        $this->assertSame($customPageSize, $listForFourthPage->pageSize);
        $this->assertSame(null,            $listForFourthPage->term);
        $this->assertSame(9,               $listForFourthPage->totalCount);
        $this->assertSame(3,               $listForFourthPage->totalPages);

        // Default page size
        $listForDefaultPageSize = $this->fetcher->paginate(1);
        $this->assertInstanceOf(GraveSiteList::class, $listForDefaultPageSize);
        $this->assertIsArray($listForDefaultPageSize->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $listForDefaultPageSize->items);
        $this->assertCount(9,                      $listForDefaultPageSize->items);
        $this->assertSame(1,                       $listForDefaultPageSize->page);
        $this->assertSame(self::DEFAULT_PAGE_SIZE, $listForDefaultPageSize->pageSize);
        $this->assertSame(null,                    $listForDefaultPageSize->term);
        $this->assertSame(9,                       $listForDefaultPageSize->totalCount);
        $this->assertSame(1,                       $listForDefaultPageSize->totalPages);
    }

    public function testItReturnsGraveSitePaginatedListByPageAndTerm(): void
    {
        $customPageSize = 3;

        $list = $this->fetcher->paginate(1, '??????', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('??????',           $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, '??????', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('??????',           $list->term);
        $this->assertSame(5,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '????????????', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('????????????',        $list->term);
        $this->assertSame(3,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '3', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('3',             $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(2, '3', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(3,              $list->items);
        $this->assertSame(2,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('3',             $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);
        $list = $this->fetcher->paginate(3, '3', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(0,              $list->items);
        $this->assertSame(3,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('3',             $list->term);
        $this->assertSame(6,               $list->totalCount);
        $this->assertSame(2,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '4', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('4',             $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '.5', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('.5',            $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '????????', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('????????',          $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);

        $list = $this->fetcher->paginate(1, '7', $customPageSize);
        $this->assertInstanceOf(GraveSiteList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(GraveSiteListItem::class, $list->items);
        $this->assertCount(2,              $list->items);
        $this->assertSame(1,               $list->page);
        $this->assertSame($customPageSize, $list->pageSize);
        $this->assertSame('7',             $list->term);
        $this->assertSame(2,               $list->totalCount);
        $this->assertSame(1,               $list->totalPages);
    }

    public function testItReturnsGraveSiteTotalCount(): void
    {
        $this->assertSame(9, $this->fetcher->countTotal());
    }

    public function testItDoesNotCountRemovedGraveSiteWhenCalculatingTotalCount(): void
    {
        // Prepare database table for testing
        $graveSiteToRemove = $this->repo->findById(new GraveSiteId('GS004'));
        $this->repo->remove($graveSiteToRemove);

        // Testing itself
        $this->assertSame(8, $this->fetcher->countTotal());
    }

    protected function loadFixtures(): void
    {
        $this->databaseTool->loadFixtures([
            CemeteryBlockFixtures::class,
            GraveSiteFixtures::class,
            NaturalPersonFixtures::class,
        ]);
    }

    private function assertPaginatedListItemEqualsGS001(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS001',    $listItem->id);
        $this->assertSame('????????????????', $listItem->cemeteryBlockName);
        $this->assertSame(1,          $listItem->rowInBlock);
        $this->assertSame(null,       $listItem->positionInRow);
        $this->assertSame(null,       $listItem->size);
        $this->assertSame(null,       $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS002(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS002',                     $listItem->id);
        $this->assertSame('?????????? ??',                   $listItem->cemeteryBlockName);
        $this->assertSame(3,                           $listItem->rowInBlock);
        $this->assertSame(4,                           $listItem->positionInRow);
        $this->assertSame(null,                        $listItem->size);
        $this->assertSame('???????????? ???????????????? ??????????????????', $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS003(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS003',                      $listItem->id);
        $this->assertSame('?????????? ??',                    $listItem->cemeteryBlockName);
        $this->assertSame(7,                            $listItem->rowInBlock);
        $this->assertSame(null,                         $listItem->positionInRow);
        $this->assertSame('2.5',                        $listItem->size);
        $this->assertSame('???????????? ?????????????? ??????????????????????', $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS004(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS004',         $listItem->id);
        $this->assertSame('??????????????????????????', $listItem->cemeteryBlockName);
        $this->assertSame(2,               $listItem->rowInBlock);
        $this->assertSame(4,               $listItem->positionInRow);
        $this->assertSame(null,            $listItem->size);
        $this->assertSame(null,            $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS005(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS005',         $listItem->id);
        $this->assertSame('??????????????????????????', $listItem->cemeteryBlockName);
        $this->assertSame(3,               $listItem->rowInBlock);
        $this->assertSame(11,              $listItem->positionInRow);
        $this->assertSame(null,            $listItem->size);
        $this->assertSame(null,            $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS006(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS006',         $listItem->id);
        $this->assertSame('??????????????????????????', $listItem->cemeteryBlockName);
        $this->assertSame(3,               $listItem->rowInBlock);
        $this->assertSame(10,              $listItem->positionInRow);
        $this->assertSame(null,            $listItem->size);
        $this->assertSame(null,            $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS007(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS007',   $listItem->id);
        $this->assertSame('?????????? ??', $listItem->cemeteryBlockName);
        $this->assertSame(3,         $listItem->rowInBlock);
        $this->assertSame(5,         $listItem->positionInRow);
        $this->assertSame(null,      $listItem->size);
        $this->assertSame(null,      $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS008(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS008',   $listItem->id);
        $this->assertSame('?????????? ??', $listItem->cemeteryBlockName);
        $this->assertSame(3,         $listItem->rowInBlock);
        $this->assertSame(6,         $listItem->positionInRow);
        $this->assertSame(null,      $listItem->size);
        $this->assertSame(null,      $listItem->personInChargeFullName);
    }

    private function assertPaginatedListItemEqualsGS009(GraveSiteListItem $listItem): void
    {
        $this->assertSame('GS009',   $listItem->id);
        $this->assertSame('?????????? ??', $listItem->cemeteryBlockName);
        $this->assertSame(3,         $listItem->rowInBlock);
        $this->assertSame(7,         $listItem->positionInRow);
        $this->assertSame('3.5',     $listItem->size);
        $this->assertSame(null,      $listItem->personInChargeFullName);
    }

    private function testItReturnsGraveSiteViewForGS001(): void
    {
        $view = $this->fetcher->findViewById('GS001');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS001',    $view->id);
        $this->assertSame('CB001',    $view->cemeteryBlockId);
        $this->assertSame('????????????????', $view->cemeteryBlockName);
        $this->assertSame(1,          $view->rowInBlock);
        $this->assertSame(null,       $view->positionInRow);
        $this->assertSame(null,       $view->geoPositionLatitude);
        $this->assertSame(null,       $view->geoPositionLongitude);
        $this->assertSame(null,       $view->geoPositionError);
        $this->assertSame(null,       $view->size);
        $this->assertSame(null,       $view->personInChargeId);
        $this->assertSame(null,       $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS002(): void
    {
        $view = $this->fetcher->findViewById('GS002');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS002',                     $view->id);
        $this->assertSame('CB002',                     $view->cemeteryBlockId);
        $this->assertSame('?????????? ??',                   $view->cemeteryBlockName);
        $this->assertSame(3,                           $view->rowInBlock);
        $this->assertSame(4,                           $view->positionInRow);
        $this->assertSame('54.950357',                 $view->geoPositionLatitude);
        $this->assertSame('82.7972252',                $view->geoPositionLongitude);
        $this->assertSame('0.5',                       $view->geoPositionError);
        $this->assertSame(null,                        $view->size);
        $this->assertSame('NP008',                     $view->personInChargeId);
        $this->assertSame('???????????? ???????????????? ??????????????????', $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS003(): void
    {
        $view = $this->fetcher->findViewById('GS003');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS003',                      $view->id);
        $this->assertSame('CB003',                      $view->cemeteryBlockId);
        $this->assertSame('?????????? ??',                    $view->cemeteryBlockName);
        $this->assertSame(7,                            $view->rowInBlock);
        $this->assertSame(null,                         $view->positionInRow);
        $this->assertSame('50.950357',                  $view->geoPositionLatitude);
        $this->assertSame('80.7972252',                 $view->geoPositionLongitude);
        $this->assertSame(null,                         $view->geoPositionError);
        $this->assertSame('2.5',                        $view->size);
        $this->assertSame('NP007',                      $view->personInChargeId);
        $this->assertSame('???????????? ?????????????? ??????????????????????', $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS004(): void
    {
        $view = $this->fetcher->findViewById('GS004');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS004',         $view->id);
        $this->assertSame('CB004',         $view->cemeteryBlockId);
        $this->assertSame('??????????????????????????', $view->cemeteryBlockName);
        $this->assertSame(2,               $view->rowInBlock);
        $this->assertSame(4,               $view->positionInRow);
        $this->assertSame(null,            $view->geoPositionLatitude);
        $this->assertSame(null,            $view->geoPositionLongitude);
        $this->assertSame(null,            $view->geoPositionError);
        $this->assertSame(null,            $view->size);
        $this->assertSame(null,            $view->personInChargeId);
        $this->assertSame(null,            $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS005(): void
    {
        $view = $this->fetcher->findViewById('GS005');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS005',         $view->id);
        $this->assertSame('CB004',         $view->cemeteryBlockId);
        $this->assertSame('??????????????????????????', $view->cemeteryBlockName);
        $this->assertSame(3,               $view->rowInBlock);
        $this->assertSame(11,              $view->positionInRow);
        $this->assertSame(null,            $view->geoPositionLatitude);
        $this->assertSame(null,            $view->geoPositionLongitude);
        $this->assertSame(null,            $view->geoPositionError);
        $this->assertSame(null,            $view->size);
        $this->assertSame(null,            $view->personInChargeId);
        $this->assertSame(null,            $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS006(): void
    {
        $view = $this->fetcher->findViewById('GS006');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS006',         $view->id);
        $this->assertSame('CB004',         $view->cemeteryBlockId);
        $this->assertSame('??????????????????????????', $view->cemeteryBlockName);
        $this->assertSame(3,               $view->rowInBlock);
        $this->assertSame(10,              $view->positionInRow);
        $this->assertSame(null,            $view->geoPositionLatitude);
        $this->assertSame(null,            $view->geoPositionLongitude);
        $this->assertSame(null,            $view->geoPositionError);
        $this->assertSame(null,            $view->size);
        $this->assertSame(null,            $view->personInChargeId);
        $this->assertSame(null,            $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS007(): void
    {
        $view = $this->fetcher->findViewById('GS007');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS007',   $view->id);
        $this->assertSame('CB002',   $view->cemeteryBlockId);
        $this->assertSame('?????????? ??', $view->cemeteryBlockName);
        $this->assertSame(3,         $view->rowInBlock);
        $this->assertSame(5,         $view->positionInRow);
        $this->assertSame(null,      $view->geoPositionLatitude);
        $this->assertSame(null,      $view->geoPositionLongitude);
        $this->assertSame(null,      $view->geoPositionError);
        $this->assertSame(null,      $view->size);
        $this->assertSame(null,      $view->personInChargeId);
        $this->assertSame(null,      $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS008(): void
    {
        $view = $this->fetcher->findViewById('GS008');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS008',   $view->id);
        $this->assertSame('CB002',   $view->cemeteryBlockId);
        $this->assertSame('?????????? ??', $view->cemeteryBlockName);
        $this->assertSame(3,         $view->rowInBlock);
        $this->assertSame(6,         $view->positionInRow);
        $this->assertSame(null,      $view->geoPositionLatitude);
        $this->assertSame(null,      $view->geoPositionLongitude);
        $this->assertSame(null,      $view->geoPositionError);
        $this->assertSame(null,      $view->size);
        $this->assertSame(null,      $view->personInChargeId);
        $this->assertSame(null,      $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }

    private function testItReturnsGraveSiteViewForGS009(): void
    {
        $view = $this->fetcher->findViewById('GS009');
        $this->assertInstanceOf(GraveSiteView::class, $view);
        $this->assertSame('GS009',   $view->id);
        $this->assertSame('CB002',   $view->cemeteryBlockId);
        $this->assertSame('?????????? ??', $view->cemeteryBlockName);
        $this->assertSame(3,         $view->rowInBlock);
        $this->assertSame(7,         $view->positionInRow);
        $this->assertSame(null,      $view->geoPositionLatitude);
        $this->assertSame(null,      $view->geoPositionLongitude);
        $this->assertSame(null,      $view->geoPositionError);
        $this->assertSame('3.5',     $view->size);
        $this->assertSame(null,      $view->personInChargeId);
        $this->assertSame(null,      $view->personInChargeFullName);
        $this->assertValidDateTimeValue($view->createdAt);
        $this->assertValidDateTimeValue($view->updatedAt);
    }
}
