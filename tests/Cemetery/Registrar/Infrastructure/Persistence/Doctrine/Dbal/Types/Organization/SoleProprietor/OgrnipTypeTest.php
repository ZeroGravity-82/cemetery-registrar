<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Ogrnip;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\SoleProprietor\OgrnipType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OgrnipTypeTest extends AbstractCustomStringTypeTest
{
    protected string $className = OgrnipType::class;
    protected string $typeName  = 'ogrnip';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '315547600024379';
        $this->phpValue = new Ogrnip('315547600024379');
    }
}
