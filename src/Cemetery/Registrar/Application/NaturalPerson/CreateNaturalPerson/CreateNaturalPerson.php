<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\CreateNaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateNaturalPerson
{
    /**
     * @param NaturalPersonFactory             $naturalPersonFactory
     * @param NaturalPersonRepositoryInterface $naturalPersonRepo
     */
    public function __construct(
        private NaturalPersonFactory             $naturalPersonFactory,
        private NaturalPersonRepositoryInterface $naturalPersonRepo,
    ) {}

    /**
     * @param CreateNaturalPersonRequest $request
     *
     * @return CreateNaturalPersonResponse
     */
    public function execute(CreateNaturalPersonRequest $request): CreateNaturalPersonResponse
    {
        $fullName      = $request->fullName;
        $bornAt        = $request->bornAt;
        $naturalPerson = $this->naturalPersonFactory->create($fullName, $bornAt);
        $this->naturalPersonRepo->save($naturalPerson);

        return new CreateNaturalPersonResponse((string) $naturalPerson->getId());
    }
}
