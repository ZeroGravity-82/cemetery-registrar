<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\BurialPlace\GraveSite\PositionInRowType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomIntegerTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PositionInRowTypeTest extends AbstractCustomIntegerTypeTest
{
    protected string $className = PositionInRowType::class;
    protected string $typeName  = 'position_in_row';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 5;
        $this->phpValue = new PositionInRow(5);
    }
}
