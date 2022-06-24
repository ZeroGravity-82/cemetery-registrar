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
        $this->assertIsArray($list->listItems);
        $this->assertContainsOnlyInstancesOf(CoffinShapeListItem::class, $list->listItems);
        $this->assertCount(4,              $list->listItems);
        $this->assertListItemEqualsTrapezoid($list->listItems[0]);
        $this->assertListItemEqualsGreekWithHandles($list->listItems[1]);
        $this->assertListItemEqualsGreekWithoutHandles($list->listItems[2]);
        $this->assertListItemEqualsAmerican($list->listItems[3]);
    }

    private function assertListItemEqualsTrapezoid(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::TRAPEZOID,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::TRAPEZOID], $listItem->label);
    }

    private function assertListItemEqualsGreekWithHandles(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::GREEK_WITH_HANDLES,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::GREEK_WITH_HANDLES], $listItem->label);
    }

    private function assertListItemEqualsGreekWithoutHandles(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::GREEK_WITHOUT_HANDLES,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::GREEK_WITHOUT_HANDLES], $listItem->label);
    }

    private function assertListItemEqualsAmerican(CoffinShapeListItem $listItem): void
    {
        $this->assertSame(CoffinShape::AMERICAN,                      $listItem->value);
        $this->assertSame(CoffinShape::LABELS[CoffinShape::AMERICAN], $listItem->label);
    }
}
