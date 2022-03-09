<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\AbstractEntityFactory;
use Cemetery\Registrar\Domain\Organization\OrganizationId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyFactory extends AbstractEntityFactory
{
    /**
     * @param OrganizationId $organizationId
     * @param string|null    $note
     *
     * @return FuneralCompany
     */
    public function create(OrganizationId $organizationId, ?string $note): FuneralCompany
    {
        $nextId         = new FuneralCompanyId($this->identityGenerator->getNextIdentity());
        $funeralCompany = new FuneralCompany($nextId, $organizationId);
        if ($note !== null) {
            $funeralCompany->setNote($note);
        }

        return $funeralCompany;
    }
}
