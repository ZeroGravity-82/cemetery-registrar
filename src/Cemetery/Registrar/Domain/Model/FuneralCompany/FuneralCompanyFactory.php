<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\OrganizationId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyFactory extends EntityFactory
{
    /**
     * @param string      $organizationId
     * @param string      $organizationType
     * @param string|null $note
     *
     * @return FuneralCompany
     */
    public function create(
        string  $organizationId,
        string  $organizationType,
        ?string $note,
    ): FuneralCompany {
        $organizationId = match ($organizationType) {
            JuristicPerson::CLASS_SHORTCUT => new OrganizationId(new JuristicPersonId($organizationId)),
            SoleProprietor::CLASS_SHORTCUT => new OrganizationId(new SoleProprietorId($organizationId)),
            default                        => $this->throwUnsupportedOrganizationTypeException($organizationType),
        };
        $note = $note !== null ? new FuneralCompanyNote($note) : null;

        return (new FuneralCompany(
            new FuneralCompanyId($this->identityGenerator->getNextIdentity()),
            $organizationId,
        ))
            ->setNote($note);
    }

    /**
     * @param string $organizationType
     *
     * @throws \RuntimeException about unsupported organization type
     */
    private function throwUnsupportedOrganizationTypeException(string $organizationType): void
    {
        throw new \RuntimeException(
            \sprintf('Неподдерживаемый тип организации "%s".', $organizationType)
        );
    }
}
