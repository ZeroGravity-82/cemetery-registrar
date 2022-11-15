<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ShowNaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcherInterface;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowNaturalPersonService extends ApplicationService
{
    public function __construct(
        ShowNaturalPersonRequestValidator     $requestValidator,
        private NaturalPersonFetcherInterface $naturalPersonFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @param ShowNaturalPersonRequest $request
     *
     * @throws NotFoundException when the natural person is not found
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ShowNaturalPersonResponse(
            $this->getNaturalPersonView($request->id),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ShowNaturalPersonRequest::class;
    }

    /**
     * @throws NotFoundException when the natural person is not found
     */
    private function getNaturalPersonView(string $id): NaturalPersonView
    {
        $view = $this->naturalPersonFetcher->findViewById($id);
        if ($view === null) {
            throw new NotFoundException(\sprintf('Физлицо с ID "%s" не найдено.', $id));
        }

        return $view;
    }
}
