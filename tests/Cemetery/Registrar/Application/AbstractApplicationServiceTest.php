<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractApplicationServiceTest extends TestCase
{
    protected MockObject|EventDispatcher $mockEventDispatcher;
    protected AbstractApplicationService $service;

    public function setUp(): void
    {
        $this->mockEventDispatcher = $this->createMock(EventDispatcher::class);
    }

    public function testItFailsWithUnsupportedRequestClassName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Единственным аргументом метода "%s::validate" должен быть экземпляр класса "%s", "%s" передан.',
            \get_class($this->service),
            $this->supportedRequestClassName(),
            FakeRequestClass::class,
        ));
        $this->service->validate(new FakeRequestClass());
    }

    abstract protected function supportedRequestClassName(): string;
}

class FakeRequestClass extends AbstractApplicationRequest
{
}
