<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\View\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeFetcher;
use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeList;
use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeListItem;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeFetcherTest extends TestCase
{
    public function testItReturnsCoffinShapeList(): void
    {
        $list = (new CoffinShapeFetcher())->findAll();
        $this->assertInstanceOf(CoffinShapeList::class, $list);
        $this->assertIsArray($list->items);
        $this->assertContainsOnlyInstancesOf(CoffinShapeListItem::class, $list->items);
        $this->assertCount(4, $list->items);
        $this->assertPaginatedListItemEqualsTrapezoid($list->items[0]);
        $this->assertPaginatedListItemEqualsGreekWithHandles($list->items[1]);
        $this->assertPaginatedListItemEqualsGreekWithoutHandles($list->items[2]);
        $this->assertPaginatedListItemEqualsAmerican($list->items[3]);
    }

    private function assertPaginatedListItemEqualsTrapezoid(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::TRAPEZOID,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::TRAPEZOID], $listItem->label);
    }

    private function assertPaginatedListItemEqualsGreekWithHandles(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::GREEK_WITH_HANDLES,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::GREEK_WITH_HANDLES], $listItem->label);
    }

    private function assertPaginatedListItemEqualsGreekWithoutHandles(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::GREEK_WITHOUT_HANDLES,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::GREEK_WITHOUT_HANDLES], $listItem->label);
    }

    private function assertPaginatedListItemEqualsAmerican(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::AMERICAN,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::AMERICAN], $listItem->label);
    }
}
