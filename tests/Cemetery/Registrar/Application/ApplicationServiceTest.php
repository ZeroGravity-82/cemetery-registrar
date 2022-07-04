<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application;

use Cemetery\Registrar\Application\ApplicationService;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ApplicationServiceTest extends TestCase
{
    protected ApplicationService $service;

    public function testItFailsWithUnsupportedRequestClassName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'The only argument of the "%s::execute" method must be instance of "%s" class, "%s" given.',
            \get_class($this->service),
            $this->service->supportedRequestClassName(),
            \stdClass::class,
        ));
        $this->service->execute(new \stdClass());
    }
}
