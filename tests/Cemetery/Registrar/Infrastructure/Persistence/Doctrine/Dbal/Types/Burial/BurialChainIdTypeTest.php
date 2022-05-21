<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Burial\BurialChainId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\BurialChainIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomStringTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialChainIdTypeTest extends CustomStringTypeTest
{
    protected string $className = BurialChainIdType::class;
    protected string $typeName  = 'burial_chain_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = 'BCH001';
        $this->phpValue = new BurialChainId('BCH001');
    }
}
