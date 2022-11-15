<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\FuneralCompany;

use Cemetery\Registrar\Domain\Model\AbstractEntityFactory;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyFactory extends AbstractEntityFactory
{
    /**
     * @throws Exception when generating an invalid funeral company ID
     * @throws Exception when the name is invalid
     */
    public function create(
        ?string $name,
        ?string $note,
    ): FuneralCompany {
        $name = new FuneralCompanyName((string) $name);
        $note = $note !== null ? new FuneralCompanyNote($note) : null;

        return (new FuneralCompany(
            new FuneralCompanyId($this->identityGenerator->getNextIdentity()),
            $name,
        ))
            ->setNote($note);
    }
}
