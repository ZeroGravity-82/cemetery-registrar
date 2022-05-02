<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\Ogrnip;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\Organization\SoleProprietor\OgrnipType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\StringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class OgrnipTypeTest extends StringTypeTest
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
