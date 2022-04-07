<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyId
{
    /**
     * @param JuristicPersonId|SoleProprietorId $id
     */
    public function __construct(
        private JuristicPersonId|SoleProprietorId $id,
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \json_encode(['value' => $this->getId()->getValue(), 'type' => $this->getIdType()]);
    }

    /**
     * @return JuristicPersonId|SoleProprietorId
     */
    public function getId(): JuristicPersonId|SoleProprietorId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIdType(): string
    {
        $parts = \explode('\\', \get_class($this->getId()));

        return \end($parts);
    }

    /**
     * @param self $funeralCompanyId
     *
     * @return bool
     */
    public function isEqual(self $funeralCompanyId): bool
    {
        $isSameIdValue = $funeralCompanyId->getId()->getValue() === $this->getId()->getValue();
        $isSameIdType  = \get_class($funeralCompanyId->getId()) === \get_class($this->getId());

        return $isSameIdValue && $isSameIdType;
    }
}
