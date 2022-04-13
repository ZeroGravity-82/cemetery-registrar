<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $coffin = new Coffin();
    }


}
