<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor;

use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractSoleProprietorService
{
    /**
     * @param SoleProprietorRepositoryInterface $soleProprietorRepo
     */
    public function __construct(
        protected readonly SoleProprietorRepositoryInterface $soleProprietorRepo,
    ) {}

    /**
     * @param string $id
     *
     * @return SoleProprietor
     *
     * @throws \RuntimeException when the sole proprietor does not exist
     */
    protected function getSoleProprietor(string $id): SoleProprietor
    {
        $id             = new SoleProprietorId($id);
        $soleProprietor = $this->soleProprietorRepo->findById($id);
        if (!$soleProprietor) {
            throw new \RuntimeException(\sprintf('Индивидуальный предприниматель с ID "%s" не найден.', $id));
        }

        return $soleProprietor;
    }
}
