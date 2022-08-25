<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace;

use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\BurialPlaceIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdTypeTest extends CustomJsonTypeTest
{
    protected string $className                                  = BurialPlaceIdType::class;
    protected string $typeName                                   = 'burial_place_id';
    protected string $phpValueClassName                          = BurialPlaceId::class;
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат декодированного значения для ID';

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToDatabaseValue(string $dbValue, BurialPlaceId $phpValue): void
    {
        $resultingDbValue = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $this->assertJson($resultingDbValue);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('type', $decodedResultDbValue);
        $this->assertArrayHasKey('value', $decodedResultDbValue);
        $this->assertSame(
            match (true) {
                $phpValue instanceof GraveSiteId        => GraveSite::CLASS_SHORTCUT,
                $phpValue instanceof ColumbariumNicheId => ColumbariumNiche::CLASS_SHORTCUT,
                $phpValue instanceof MemorialTreeId     => MemorialTree::CLASS_SHORTCUT,
            },
            $decodedResultDbValue['type']
        );
        $this->assertSame($phpValue->value(), $decodedResultDbValue['value']);
    }

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToPhpValue(string $dbValue, BurialPlaceId $phpValue): void
    {
        /** @var BurialPlaceId $resultingPhpValue */
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(\get_class($phpValue), $resultingPhpValue);
        $this->assertSame($phpValue->value(), $resultingPhpValue->value());
    }

    protected function getConversionData(): iterable
    {
        // database value, PHP value
        yield ['{"type":"GRAVE_SITE","value":"GS001"}',        new GraveSiteId('GS001')];
        yield ['{"type":"COLUMBARIUM_NICHE","value":"CN001"}', new ColumbariumNicheId('CN001')];
        yield ['{"type":"MEMORIAL_TREE","value":"MT001"}',     new MemorialTreeId('MT001')];
    }
}
