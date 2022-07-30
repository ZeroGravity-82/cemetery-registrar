<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\CountColumbariumTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountColumbariumTotalService extends ApplicationService
{
    public function __construct(
        private readonly ColumbariumFetcher $columbariumFetcher,
    ) {}

    /**
     * @param CountColumbariumTotalRequest $request
     *
     * @return Notification
     */
    public function validate(ApplicationRequest $request): Notification
    {
        // TODO: Implement validate() method.
    }

    /**
     * @param CountColumbariumTotalRequest $request
     *
     * @return ApplicationSuccessResponse
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new CountColumbariumTotalResponse($this->columbariumFetcher->countTotal());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CountColumbariumTotalRequest::class;
    }
}
