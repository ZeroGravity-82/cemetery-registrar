<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class ApplicationServiceTest extends TestCase
{
    protected MockObject|EventDispatcher $mockEventDispatcher;
    protected ApplicationService         $service;

    abstract public function testItReturnsSupportedRequestClassName(): void;

    public function setUp(): void
    {
        $this->mockEventDispatcher = $this->createMock(EventDispatcher::class);
    }

    public function testItFailsWithUnsupportedRequestClassName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Единственным аргументом метода "%s::execute" должен быть экземпляр класса "%s", "%s" передан.',
            \get_class($this->service),
            $this->service->supportedRequestClassName(),
            \stdClass::class,
        ));
        $this->service->execute(new \stdClass());
    }
}
