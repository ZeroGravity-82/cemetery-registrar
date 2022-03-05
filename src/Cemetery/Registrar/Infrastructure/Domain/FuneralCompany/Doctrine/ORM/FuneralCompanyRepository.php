<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Domain\FuneralCompany\Doctrine\ORM;

use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyRepository implements FuneralCompanyRepositoryInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function save(FuneralCompany $funeralCompany): void
    {
        $this->entityManager->persist($funeralCompany);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll(FuneralCompanyCollection $funeralCompanies): void
    {
        foreach ($funeralCompanies as $funeralCompany) {
            $this->entityManager->persist($funeralCompany);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById(FuneralCompanyId $funeralCompanyId): ?FuneralCompany
    {
        return $this->entityManager->getRepository(FuneralCompany::class)->find((string) $funeralCompanyId);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(FuneralCompany $funeralCompany): void
    {
        $this->entityManager->remove($funeralCompany);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll(FuneralCompanyCollection $funeralCompanies): void
    {
        foreach ($funeralCompanies as $funeralCompany) {
            $this->entityManager->remove($funeralCompany);
        }
        $this->entityManager->flush();
    }
}
