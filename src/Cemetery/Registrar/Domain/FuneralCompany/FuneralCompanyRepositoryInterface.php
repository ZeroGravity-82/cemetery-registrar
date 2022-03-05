<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface FuneralCompanyRepositoryInterface
{
    /**
     * Adds the funeral company to the repository. If the funeral company already exists, it will be updated.
     *
     * @param FuneralCompany $funeralCompany
     */
    public function save(FuneralCompany $funeralCompany): void;

    /**
     * Adds the collection of funeral companies to the repository. If any of the funeral companies already exist, they
     * will be updated.
     *
     * @param FuneralCompanyCollection $funeralCompanies
     */
    public function saveAll(FuneralCompanyCollection $funeralCompanies): void;

    /**
     * Returns the funeral company by the ID. If no funeral company found, null will be returned.
     *
     * @param FuneralCompanyId $funeralCompanyId
     *
     * @return FuneralCompany|null
     */
    public function findById(FuneralCompanyId $funeralCompanyId): ?FuneralCompany;

    /**
     * Removes the funeral company from the repository.
     *
     * @param FuneralCompany $funeralCompany
     */
    public function remove(FuneralCompany $funeralCompany): void;

    /**
     * Removes the collection of funeral companies from the repository.
     *
     * @param FuneralCompanyCollection $funeralCompanies
     */
    public function removeAll(FuneralCompanyCollection $funeralCompanies): void;
}
