<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorIdTest extends AbstractEntityIdTest
{
    protected string $className = SoleProprietorId::class;
}