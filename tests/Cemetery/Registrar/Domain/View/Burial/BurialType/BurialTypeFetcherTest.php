<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\View\Burial\BurialType;

use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use Cemetery\Registrar\Domain\View\Burial\BurialType\BurialTypeFetcher;
use Cemetery\Registrar\Domain\View\Burial\BurialType\BurialTypeList;
use Cemetery\Registrar\Domain\View\Burial\BurialType\BurialTypeListItem;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTypeFetcherTest extends TestCase
{
    public function testItReturnsBurialTypeList(): void
    {
        $list = (new BurialTypeFetcher())->findAll();
        $this->assertInstanceOf(BurialTypeList::class, $list);
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(BurialTypeListItem::class, $list->listItems);
        $this->assertCount(4, $list->listItems);
        $this->assertListItemEqualsCoffinInGraveSite($list->listItems[0]);
        $this->assertListItemEqualsUrnInGraveSite($list->listItems[1]);
        $this->assertListItemEqualsUrnInColumbariumNiche($list->listItems[2]);
        $this->assertListItemEqualsAshesUnderMemorialTree($list->listItems[3]);
    }

    private function assertListItemEqualsCoffinInGraveSite(BurialTypeListItem $listItem): void
    {
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE,                     $listItem->value);
        $this->assertSame(BurialType::LABELS[BurialType::COFFIN_IN_GRAVE_SITE], $listItem->label);
    }

    private function assertListItemEqualsUrnInGraveSite(BurialTypeListItem $listItem): void
    {
        $this->assertSame(BurialType::URN_IN_GRAVE_SITE,                     $listItem->value);
        $this->assertSame(BurialType::LABELS[BurialType::URN_IN_GRAVE_SITE], $listItem->label);
    }

    private function assertListItemEqualsUrnInColumbariumNiche(BurialTypeListItem $listItem): void
    {
        $this->assertSame(BurialType::URN_IN_COLUMBARIUM_NICHE,                     $listItem->value);
        $this->assertSame(BurialType::LABELS[BurialType::URN_IN_COLUMBARIUM_NICHE], $listItem->label);
    }

    private function assertListItemEqualsAshesUnderMemorialTree(BurialTypeListItem $listItem): void
    {
        $this->assertSame(BurialType::ASHES_UNDER_MEMORIAL_TREE,                     $listItem->value);
        $this->assertSame(BurialType::LABELS[BurialType::ASHES_UNDER_MEMORIAL_TREE], $listItem->label);
    }
}
