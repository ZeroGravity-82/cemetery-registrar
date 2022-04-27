<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition;

use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\GeoPosition\AccuracyType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AccuracyTypeTest extends StringTypeTest
{
    protected string $className = AccuracyType::class;

    protected string $typeName = 'geo_accuracy';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '0.25';
        $this->phpValue = new Accuracy('0.25');
    }
}
