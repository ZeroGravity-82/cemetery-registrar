<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialFormView;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Infrastructure\Application\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineDbalBurialFetcher extends Fetcher implements BurialFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getById(string $id): BurialFormView
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'b.id                    AS id',
                'b.code                  AS code',
                'b.type                  AS type',
                'd.id                    AS deceasedId',
                'dnp.id                  AS deceasedNaturalPersonId',
                'dnp.full_name           AS deceasedNaturalPersonFullName',
                'dnp.born_at             AS deceasedNaturalPersonBornAt',
                'd.died_at               AS deceasedDiedAt',
                'd.age                   AS deceasedAge',
                'd.death_certificate_id  AS deceasedDeathCertificateId',
                'd.cause_of_death        AS deceasedCauseOfDeath',
                'bponp.id                AS burialPlaceOwnerId',
                'bponp.full_name         AS burialPlaceOwnerFullName',
                'bponp.phone             AS burialPlaceOwnerPhone',
                'bponp.phone_additional  AS burialPlaceOwnerPhoneAdditional',
                'bponp.email             AS burialPlaceOwnerEmail',
                'bponp.address           AS burialPlaceOwnerAddress',
                'bponp.born_at           AS burialPlaceOwnerBornAt',
                'bponp.place_of_birth    AS burialPlaceOwnerPlaceOfBirth',
                'b.buried_at             AS buriedAt',
                'b.updated_at            AS updatedAt',

                // Doctrine DBAL custom JSON types:
                'bponp.passport          AS burialPlaceOwnerPassportJson',
                'b.burial_container      AS burialContainerJson',
                'b.customer_id           AS customerIdJson',
                'b.funeral_company_id    AS funeralCompanyIdJson',
                'b.burial_place_id       AS burialPlaceIdJson',
            )
            ->from('burial', 'b')
            ->leftJoin('b', 'deceased',       'd',     'b.deceased_id = d.id')
            ->leftJoin('d', 'natural_person', 'dnp',   'd.natural_person_id = dnp.id')
            ->leftJoin('b', 'natural_person', 'bponp', 'b.burial_place_owner_id = bponp.id')
            ->andWhere('b.id = :id')
            ->setParameter('id', $id)
            ->orderBy('b.code')
            ->executeQuery();

        $data = $result->fetchAssociative();
        if ($data === false) {
            throw new \RuntimeException(\sprintf('Захоронение с ID "%s" не найдено.', $id));
        }

        // Parse burial place owner passport data
        $burialPlaceOwnerPassportData = isset($data['burialPlaceOwnerPassportJson'])
            ? \json_decode($data['burialPlaceOwnerPassportJson'], true)
            : null;
        $data['burialPlaceOwnerPassportSeries']       = $burialPlaceOwnerPassportData['series']       ?? null;
        $data['burialPlaceOwnerPassportNumber']       = $burialPlaceOwnerPassportData['number']       ?? null;
        $data['burialPlaceOwnerPassportIssuedAt']     = $burialPlaceOwnerPassportData['issuedAt']     ?? null;
        $data['burialPlaceOwnerPassportIssuedBy']     = $burialPlaceOwnerPassportData['issuedBy']     ?? null;
        $data['burialPlaceOwnerPassportDivisionCode'] = $burialPlaceOwnerPassportData['divisionCode'] ?? null;

        // Parse burial container data
        $burialContainerData = isset($data['burialContainerJson'])
            ? \json_decode($data['burialContainerJson'], true)
            : null;
        $data['burialContainerType']                = $burialContainerData['type'] ?? null;
        $data['burialContainerCoffinSize']          = null;
        $data['burialContainerCoffinShape']         = null;
        $data['burialContainerCoffinIsNonStandard'] = null;
        if ($data['burialContainerType'] === Coffin::CLASS_SHORTCUT) {
            $data['burialContainerCoffinSize']          = $burialContainerData['value']['size'];
            $data['burialContainerCoffinShape']         = $burialContainerData['value']['shape'];
            $data['burialContainerCoffinIsNonStandard'] = $burialContainerData['value']['isNonStandard'];
        }

        // Get associations described with JSON
        $customerIdData = isset($data['customerIdJson'])
            ? \json_decode($data['customerIdJson'], true)
            : null;
        $data['customerId']   = $customerIdData['value'] ?? null;
        $data['customerType'] = $customerIdData['type']  ?? null;
        $result = $this->connection->createQueryBuilder()
            ->select(
            )
            ->from('')
            ->andWhere('c.id = :id')
            ->setParameter('id', )





        $funeralCompanyIdData = isset($data['funeralCompanyIdJson'])
            ? \json_decode($data['funeralCompanyIdJson'], true)
            : null;
        $data['funeralCompanyId']   = $funeralCompanyIdData['value'] ?? null;
        $data['funeralCompanyType'] = $funeralCompanyIdData['type']  ?? null;



        $burialPlaceIdData = isset($data['burialPlaceIdJson'])
            ? \json_decode($data['burialPlaceIdJson'], true)
            : null;
        $data['burialPlaceId']   = $burialPlaceIdData['value'] ?? null;
        $data['burialPlaceType'] = $burialPlaceIdData['type']  ?? null;




        // hydrate view

        // set burialChainId to NULL

        unset($data); // TODO delete
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {

    }
}
