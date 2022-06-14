<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Burial\BurialCode;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcher;
use Cemetery\Registrar\Domain\View\Burial\BurialList;
use Cemetery\Registrar\Domain\View\Burial\BurialListItem;
use Cemetery\Registrar\Domain\View\Burial\BurialView;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialFetcher extends DoctrineDbalFetcher implements BurialFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getViewById(string $id): BurialView
    {
        $burialViewData = $this->queryBurialViewData($id);
        if ($burialViewData === false) {
            throw new \RuntimeException(\sprintf('Захоронение с ID "%s" не найдено.', $id));
        }

        return $this->hydrateBurialView($burialViewData);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): BurialList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'b.id                          AS id',
                'b.code                        AS code',
                'dnp.full_name                 AS deceasedNaturalPersonFullName',
                'dnp.born_at                   AS deceasedNaturalPersonBornAt',
                'd.died_at                     AS deceasedDiedAt',
                'd.age                         AS deceasedAge',
                'b.buried_at                   AS buriedAt',
                'b.burial_place_id->>"$.type"  AS burialPlaceType',
                'bpgscb.name                   AS burialPlaceGraveSiteCemeteryBlockName',
                'bpgs.row_in_block             AS burialPlaceGraveSiteRowInBlock',
                'bpgs.position_in_row          AS burialPlaceGraveSitePositionInRow',
                'bpcnc.name                    AS burialPlaceColumbariumNicheColumbariumName',
                'bpcn.row_in_columbarium       AS burialPlaceColumbariumNicheRowInColumbarium',
                'bpcn.columbarium_niche_number AS burialPlaceColumbariumNicheNumber',
                'bpmt.tree_number              AS burialPlaceMemorialTreeNumber',
                'b.customer_id->>"$.type"      AS customerType',
                'cnp.full_name                 AS customerNaturalPersonFullName',
                'cnp.address                   AS customerNaturalPersonAddress',
                'cnp.phone                     AS customerNaturalPersonPhone',
                'csp.name                      AS customerSoleProprietorName',
                'csp.registration_address      AS customerSoleProprietorRegistrationAddress',
                'csp.actual_location_address   AS customerSoleProprietorActualLocationAddress',
                'csp.phone                     AS customerSoleProprietorPhone',
                'cjp.name                      AS customerJuristicPersonName',
                'cjp.legal_address             AS customerJuristicPersonLegalAddress',
                'cjp.postal_address            AS customerJuristicPersonPostalAddress',
                'cjp.phone                     AS customerJuristicPersonPhone',
            )
            ->from('burial', 'b')
            ->andWhere('b.removed_at IS NULL')
            ->orderBy('b.code')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->addJoinsToQueryBuilder($queryBuilder);
        $this->addWheresToQueryBuilder($queryBuilder, $term);

        $burialViewListData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doGetTotalCount($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydrateBurialViewList($burialViewListData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount(): int
    {
        return $this->doGetTotalCount(null);
    }

    /**
     * @param string $id
     *
     * @return false|array
     */
    private function queryBurialViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'b.id                                          AS id',
                'b.code                                        AS code',
                'b.type                                        AS type',
                'd.id                                          AS deceasedId',
                'dnp.id                                        AS deceasedNaturalPersonId',
                'dnp.full_name                                 AS deceasedNaturalPersonFullName',
                'dnp.born_at                                   AS deceasedNaturalPersonBornAt',
                'd.died_at                                     AS deceasedDiedAt',
                'd.age                                         AS deceasedAge',
                'd.death_certificate_id                        AS deceasedDeathCertificateId',
                'd.cause_of_death                              AS deceasedCauseOfDeath',
                'b.customer_id->>"$.value"                     AS customerId',
                'b.customer_id->>"$.type"                      AS customerType',
                'cnp.full_name                                 AS customerNaturalPersonFullName',
                'cnp.phone                                     AS customerNaturalPersonPhone',
                'cnp.phone_additional                          AS customerNaturalPersonPhoneAdditional',
                'cnp.email                                     AS customerNaturalPersonEmail',
                'cnp.address                                   AS customerNaturalPersonAddress',
                'cnp.born_at                                   AS customerNaturalPersonBornAt',
                'cnp.place_of_birth                            AS customerNaturalPersonPlaceOfBirth',
                'cnp.passport->>"$.series"                     AS customerNaturalPersonPassportSeries',
                'cnp.passport->>"$.number"                     AS customerNaturalPersonPassportNumber',
                'cnp.passport->>"$.issuedAt"                   AS customerNaturalPersonPassportIssuedAt',
                'cnp.passport->>"$.issuedBy"                   AS customerNaturalPersonPassportIssuedBy',
                'cnp.passport->>"$.divisionCode"               AS customerNaturalPersonPassportDivisionCode',
                'csp.name                                      AS customerSoleProprietorName',
                'csp.inn                                       AS customerSoleProprietorInn',
                'csp.ogrnip                                    AS customerSoleProprietorOgrnip',
                'csp.okpo                                      AS customerSoleProprietorOkpo',
                'csp.okved                                     AS customerSoleProprietorOkved',
                'csp.registration_address                      AS customerSoleProprietorRegistrationAddress',
                'csp.actual_location_address                   AS customerSoleProprietorActualLocationAddress',
                'csp.bank_details->>"$.bankName"               AS customerSoleProprietorBankDetailsBankName',
                'csp.bank_details->>"$.bik"                    AS customerSoleProprietorBankDetailsBik',
                'csp.bank_details->>"$.correspondentAccount"   AS customerSoleProprietorBankDetailsCorrespondentAccount',
                'csp.bank_details->>"$.currentAccount"         AS customerSoleProprietorBankDetailsCurrentAccount',
                'csp.phone                                     AS customerSoleProprietorPhone',
                'csp.phone_additional                          AS customerSoleProprietorPhoneAdditional',
                'csp.fax                                       AS customerSoleProprietorFax',
                'csp.email                                     AS customerSoleProprietorEmail',
                'csp.website                                   AS customerSoleProprietorWebsite',
                'cjp.name                                      AS customerJuristicPersonName',
                'cjp.inn                                       AS customerJuristicPersonInn',
                'cjp.kpp                                       AS customerJuristicPersonKpp',
                'cjp.ogrn                                      AS customerJuristicPersonOgrn',
                'cjp.okpo                                      AS customerJuristicPersonOkpo',
                'cjp.okved                                     AS customerJuristicPersonOkved',
                'cjp.legal_address                             AS customerJuristicPersonLegalAddress',
                'cjp.postal_address                            AS customerJuristicPersonPostalAddress',
                'cjp.bank_details->>"$.bankName"               AS customerJuristicPersonBankDetailsBankName',
                'cjp.bank_details->>"$.bik"                    AS customerJuristicPersonBankDetailsBik',
                'cjp.bank_details->>"$.correspondentAccount"   AS customerJuristicPersonBankDetailsCorrespondentAccount',
                'cjp.bank_details->>"$.currentAccount"         AS customerJuristicPersonBankDetailsCurrentAccount',
                'cjp.phone                                     AS customerJuristicPersonPhone',
                'cjp.phone_additional                          AS customerJuristicPersonPhoneAdditional',
                'cjp.fax                                       AS customerJuristicPersonFax',
                'cjp.general_director                          AS customerJuristicPersonGeneralDirector',
                'cjp.email                                     AS customerJuristicPersonEmail',
                'cjp.website                                   AS customerJuristicPersonWebsite',
                'bponp.id                                      AS burialPlaceOwnerId',
                'bponp.full_name                               AS burialPlaceOwnerFullName',
                'bponp.phone                                   AS burialPlaceOwnerPhone',
                'bponp.phone_additional                        AS burialPlaceOwnerPhoneAdditional',
                'bponp.email                                   AS burialPlaceOwnerEmail',
                'bponp.address                                 AS burialPlaceOwnerAddress',
                'bponp.born_at                                 AS burialPlaceOwnerBornAt',
                'bponp.place_of_birth                          AS burialPlaceOwnerPlaceOfBirth',
                'bponp.passport->>"$.series"                   AS burialPlaceOwnerPassportSeries',
                'bponp.passport->>"$.number"                   AS burialPlaceOwnerPassportNumber',
                'bponp.passport->>"$.issuedAt"                 AS burialPlaceOwnerPassportIssuedAt',
                'bponp.passport->>"$.issuedBy"                 AS burialPlaceOwnerPassportIssuedBy',
                'bponp.passport->>"$.divisionCode"             AS burialPlaceOwnerPassportDivisionCode',
                'fc.id                                         AS funeralCompanyId',
                'b.burial_chain_id                             AS burialChainId',
                'b.burial_place_id->>"$.value"                 AS burialPlaceId',
                'b.burial_place_id->>"$.type"                  AS burialPlaceType',
                'bpgscb.id                                     AS burialPlaceGraveSiteCemeteryBlockId',
                'bpgscb.name                                   AS burialPlaceGraveSiteCemeteryBlockName',
                'bpgs.row_in_block                             AS burialPlaceGraveSiteRowInBlock',
                'bpgs.position_in_row                          AS burialPlaceGraveSitePositionInRow',
                'bpgs.size                                     AS burialPlaceGraveSiteSize',
                'bpgs.geo_position->>"$.coordinates.latitude"  AS burialPlaceGraveSiteGeoPositionLatitude',
                'bpgs.geo_position->>"$.coordinates.longitude" AS burialPlaceGraveSiteGeoPositionLongitude',
                'bpgs.geo_position->>"$.error"                 AS burialPlaceGraveSiteGeoPositionError',
                'bpcnc.id                                      AS burialPlaceColumbariumNicheColumbariumId',
                'bpcnc.name                                    AS burialPlaceColumbariumNicheColumbariumName',
                'bpcn.row_in_columbarium                       AS burialPlaceColumbariumNicheRowInColumbarium',
                'bpcn.columbarium_niche_number                 AS burialPlaceColumbariumNicheNumber',
                'bpcn.geo_position->>"$.coordinates.latitude"  AS burialPlaceColumbariumNicheGeoPositionLatitude',
                'bpcn.geo_position->>"$.coordinates.longitude" AS burialPlaceColumbariumNicheGeoPositionLongitude',
                'bpcn.geo_position->>"$.error"                 AS burialPlaceColumbariumNicheGeoPositionError',
                'bpmt.tree_number                              AS burialPlaceMemorialTreeNumber',
                'bpmt.geo_position->>"$.coordinates.latitude"  AS burialPlaceMemorialTreeGeoPositionLatitude',
                'bpmt.geo_position->>"$.coordinates.longitude" AS burialPlaceMemorialTreeGeoPositionLongitude',
                'bpmt.geo_position->>"$.error"                 AS burialPlaceMemorialTreeGeoPositionError',
                'b.burial_container->>"$.type"                 AS burialContainerType',
                'b.burial_container->"$.value.size"            AS burialContainerCoffinSize',
                'b.burial_container->>"$.value.shape"          AS burialContainerCoffinShape',
                'b.burial_container->>"$.value.isNonStandard"  AS burialContainerCoffinIsNonStandard',
                'b.buried_at                                   AS buriedAt',
                'b.updated_at                                  AS updatedAt',
            )
            ->from('burial', 'b')
            ->leftJoin('b',    'deceased',          'd',      'b.deceased_id                 = d.id')
            ->leftJoin('d',    'natural_person',    'dnp',    'd.natural_person_id           = dnp.id')
            ->leftJoin('b',    'natural_person',    'cnp',    'b.customer_id->>"$.value"     = cnp.id')
            ->leftJoin('b',    'sole_proprietor',   'csp',    'b.customer_id->>"$.value"     = csp.id')
            ->leftJoin('b',    'juristic_person',   'cjp',    'b.customer_id->>"$.value"     = cjp.id')
            ->leftJoin('b',    'natural_person',    'bponp',  'b.burial_place_owner_id       = bponp.id')
            ->leftJoin('b',    'funeral_company',   'fc',     'b.funeral_company_id          = fc.id')
            ->leftJoin('b',    'grave_site',        'bpgs',   'b.burial_place_id->>"$.value" = bpgs.id')
            ->leftJoin('bpgs', 'cemetery_block',    'bpgscb', 'bpgs.cemetery_block_id        = bpgscb.id')
            ->leftJoin('b',    'columbarium_niche', 'bpcn',   'b.burial_place_id->>"$.value" = bpcn.id')
            ->leftJoin('bpcn', 'columbarium',       'bpcnc',  'bpcn.columbarium_id           = bpcnc.id')
            ->leftJoin('b',    'memorial_tree',     'bpmt',   'b.burial_place_id->>"$.value" = bpmt.id')
            ->andWhere('b.id = :id')
            ->andWhere('b.removed_at IS NULL')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchAssociative();
    }

    /**
     * @param string|null $term
     *
     * @return int
     */
    private function doGetTotalCount(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(b.id)')
            ->from('burial', 'b')
            ->andWhere('b.removed_at IS NULL');
        $this->addJoinsToQueryBuilder($queryBuilder);
        $this->addWheresToQueryBuilder($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function addJoinsToQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('b',    'deceased',          'd',      'b.deceased_id                 = d.id')
            ->leftJoin('d',    'natural_person',    'dnp',    'd.natural_person_id           = dnp.id')
            ->leftJoin('b',    'grave_site',        'bpgs',   'b.burial_place_id->>"$.value" = bpgs.id')
            ->leftJoin('bpgs', 'cemetery_block',    'bpgscb', 'bpgs.cemetery_block_id        = bpgscb.id')
            ->leftJoin('b',    'columbarium_niche', 'bpcn',   'b.burial_place_id->>"$.value" = bpcn.id')
            ->leftJoin('bpcn', 'columbarium',       'bpcnc',  'bpcn.columbarium_id           = bpcnc.id')
            ->leftJoin('b',    'memorial_tree',     'bpmt',   'b.burial_place_id->>"$.value" = bpmt.id')
            ->leftJoin('b',    'natural_person',    'cnp',    'b.customer_id->>"$.value"     = cnp.id')
            ->leftJoin('b',    'sole_proprietor',   'csp',    'b.customer_id->>"$.value"     = csp.id')
            ->leftJoin('b',    'juristic_person',   'cjp',    'b.customer_id->>"$.value"     = cjp.id');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param string|null  $term
     */
    private function addWheresToQueryBuilder(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($term === null || $term === '') {
            return;
        }
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->like('b.code', ':term'),
                    $queryBuilder->expr()->like('dnp.full_name', ':term'),
                    $queryBuilder->expr()->like('dnp.born_at', ':term'),
                    $queryBuilder->expr()->like('d.died_at', ':term'),
                    $queryBuilder->expr()->like('d.age', ':term'),
                    $queryBuilder->expr()->like('b.buried_at', ':term'),
                    $queryBuilder->expr()->like('bpgscb.name', ':term'),
                    $queryBuilder->expr()->like('bpgs.row_in_block', ':term'),
                    $queryBuilder->expr()->like('bpgs.position_in_row', ':term'),
                    $queryBuilder->expr()->like('bpcnc.name', ':term'),
                    $queryBuilder->expr()->like('bpcn.row_in_columbarium', ':term'),
                    $queryBuilder->expr()->like('bpcn.columbarium_niche_number', ':term'),
                    $queryBuilder->expr()->like('bpmt.tree_number', ':term'),
                    $queryBuilder->expr()->like('cnp.full_name', ':term'),
                    $queryBuilder->expr()->like('cnp.address', ':term'),
                    $queryBuilder->expr()->like('cnp.phone', ':term'),
                    $queryBuilder->expr()->like('csp.name', ':term'),
                    $queryBuilder->expr()->like('csp.registration_address', ':term'),
                    $queryBuilder->expr()->like('csp.actual_location_address', ':term'),
                    $queryBuilder->expr()->like('csp.phone', ':term'),
                    $queryBuilder->expr()->like('cjp.name', ':term'),
                    $queryBuilder->expr()->like('cjp.legal_address', ':term'),
                    $queryBuilder->expr()->like('cjp.postal_address', ':term'),
                    $queryBuilder->expr()->like('cjp.phone', ':term'),
                )
            )
            ->setParameter('term', "%$term%");
    }

    /**
     * @param array $burialViewData
     *
     * @return BurialView
     */
    private function hydrateBurialView(array $burialViewData): BurialView
    {
        return new BurialView(
            $burialViewData['id'],
            $this->formatCode($burialViewData['code']),
            $burialViewData['type'],
            $burialViewData['deceasedId'],
            $burialViewData['deceasedNaturalPersonId'],
            $burialViewData['deceasedNaturalPersonFullName'],
            $burialViewData['deceasedNaturalPersonBornAt'],
            $burialViewData['deceasedDiedAt'],
            $burialViewData['deceasedAge'],
            $burialViewData['deceasedDeathCertificateId'],
            $burialViewData['deceasedCauseOfDeath'],
            $burialViewData['customerId'],
            $burialViewData['customerType'],
            $burialViewData['customerNaturalPersonFullName'],
            $burialViewData['customerNaturalPersonPhone'],
            $burialViewData['customerNaturalPersonPhoneAdditional'],
            $burialViewData['customerNaturalPersonEmail'],
            $burialViewData['customerNaturalPersonAddress'],
            $burialViewData['customerNaturalPersonBornAt'],
            $burialViewData['customerNaturalPersonPlaceOfBirth'],
            $burialViewData['customerNaturalPersonPassportSeries'],
            $burialViewData['customerNaturalPersonPassportNumber'],
            $burialViewData['customerNaturalPersonPassportIssuedAt'],
            $burialViewData['customerNaturalPersonPassportIssuedBy'],
            match ($burialViewData['customerNaturalPersonPassportDivisionCode']) {
                'null'  => null,
                default => $burialViewData['customerNaturalPersonPassportDivisionCode'],
            },
            $burialViewData['customerSoleProprietorName'],
            $burialViewData['customerSoleProprietorInn'],
            $burialViewData['customerSoleProprietorOgrnip'],
            $burialViewData['customerSoleProprietorOkpo'],
            $burialViewData['customerSoleProprietorOkved'],
            $burialViewData['customerSoleProprietorRegistrationAddress'],
            $burialViewData['customerSoleProprietorActualLocationAddress'],
            $burialViewData['customerSoleProprietorBankDetailsBankName'],
            $burialViewData['customerSoleProprietorBankDetailsBik'],
            $burialViewData['customerSoleProprietorBankDetailsCorrespondentAccount'],
            $burialViewData['customerSoleProprietorBankDetailsCurrentAccount'],
            $burialViewData['customerSoleProprietorPhone'],
            $burialViewData['customerSoleProprietorPhoneAdditional'],
            $burialViewData['customerSoleProprietorFax'],
            $burialViewData['customerSoleProprietorEmail'],
            $burialViewData['customerSoleProprietorWebsite'],
            $burialViewData['customerJuristicPersonName'],
            $burialViewData['customerJuristicPersonInn'],
            $burialViewData['customerJuristicPersonKpp'],
            $burialViewData['customerJuristicPersonOgrn'],
            $burialViewData['customerJuristicPersonOkpo'],
            $burialViewData['customerJuristicPersonOkved'],
            $burialViewData['customerJuristicPersonLegalAddress'],
            $burialViewData['customerJuristicPersonPostalAddress'],
            $burialViewData['customerJuristicPersonBankDetailsBankName'],
            $burialViewData['customerJuristicPersonBankDetailsBik'],
            $burialViewData['customerJuristicPersonBankDetailsCorrespondentAccount'],
            $burialViewData['customerJuristicPersonBankDetailsCurrentAccount'],
            $burialViewData['customerJuristicPersonPhone'],
            $burialViewData['customerJuristicPersonPhoneAdditional'],
            $burialViewData['customerJuristicPersonFax'],
            $burialViewData['customerJuristicPersonGeneralDirector'],
            $burialViewData['customerJuristicPersonEmail'],
            $burialViewData['customerJuristicPersonWebsite'],
            $burialViewData['burialPlaceOwnerId'],
            $burialViewData['burialPlaceOwnerFullName'],
            $burialViewData['burialPlaceOwnerPhone'],
            $burialViewData['burialPlaceOwnerPhoneAdditional'],
            $burialViewData['burialPlaceOwnerEmail'],
            $burialViewData['burialPlaceOwnerAddress'],
            $burialViewData['burialPlaceOwnerBornAt'],
            $burialViewData['burialPlaceOwnerPlaceOfBirth'],
            $burialViewData['burialPlaceOwnerPassportSeries'],
            $burialViewData['burialPlaceOwnerPassportNumber'],
            $burialViewData['burialPlaceOwnerPassportIssuedAt'],
            $burialViewData['burialPlaceOwnerPassportIssuedBy'],
            match ($burialViewData['burialPlaceOwnerPassportDivisionCode']) {
                'null'  => null,
                default => $burialViewData['burialPlaceOwnerPassportDivisionCode'],
            },
            $burialViewData['funeralCompanyId'],
            $burialViewData['burialChainId'],
            $burialViewData['burialPlaceId'],
            $burialViewData['burialPlaceType'],
            $burialViewData['burialPlaceGraveSiteCemeteryBlockId'],
            $burialViewData['burialPlaceGraveSiteCemeteryBlockName'],
            $burialViewData['burialPlaceGraveSiteRowInBlock'],
            $burialViewData['burialPlaceGraveSitePositionInRow'],
            $burialViewData['burialPlaceGraveSiteSize'],
            $burialViewData['burialPlaceGraveSiteGeoPositionLatitude'],
            $burialViewData['burialPlaceGraveSiteGeoPositionLongitude'],
            match ($burialViewData['burialPlaceGraveSiteGeoPositionError']) {
                'null'  => null,
                default => $burialViewData['burialPlaceGraveSiteGeoPositionError'],
            },
            $burialViewData['burialPlaceColumbariumNicheColumbariumId'],
            $burialViewData['burialPlaceColumbariumNicheColumbariumName'],
            $burialViewData['burialPlaceColumbariumNicheRowInColumbarium'],
            $burialViewData['burialPlaceColumbariumNicheNumber'],
            $burialViewData['burialPlaceColumbariumNicheGeoPositionLatitude'],
            $burialViewData['burialPlaceColumbariumNicheGeoPositionLongitude'],
            match ($burialViewData['burialPlaceColumbariumNicheGeoPositionError']) {
                'null'  => null,
                default => $burialViewData['burialPlaceColumbariumNicheGeoPositionError'],
            },
            $burialViewData['burialPlaceMemorialTreeNumber'],
            $burialViewData['burialPlaceMemorialTreeGeoPositionLatitude'],
            $burialViewData['burialPlaceMemorialTreeGeoPositionLongitude'],
            match ($burialViewData['burialPlaceMemorialTreeGeoPositionError']) {
                'null'  => null,
                default => $burialViewData['burialPlaceMemorialTreeGeoPositionError'],
            },
            $burialViewData['burialContainerType'],
            match ($burialViewData['burialContainerCoffinSize']) {
                null    => null,
                default => (int) $burialViewData['burialContainerCoffinSize'],
            },
            $burialViewData['burialContainerCoffinShape'],
            match ($burialViewData['burialContainerCoffinIsNonStandard']) {
                'true'  => true,
                'false' => false,
                null    => null,
            },
            $burialViewData['buriedAt'],
            $burialViewData['updatedAt'],
        );
    }

    /**
     * @param array       $burialViewListData
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $term
     * @param int         $totalCount
     * @param int         $totalPages
     *
     * @return BurialList
     */
    private function hydrateBurialViewList(
        array   $burialViewListData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): BurialList {
        $burialListItems = [];
        foreach ($burialViewListData as $burialListItemData) {
            $burialListItems[] = new BurialListItem(
                $burialListItemData['id'],
                $this->formatCode($burialListItemData['code']),
                $burialListItemData['deceasedNaturalPersonFullName'],
                $burialListItemData['deceasedNaturalPersonBornAt'],
                $burialListItemData['deceasedDiedAt'],
                $burialListItemData['deceasedAge'],
                $burialListItemData['buriedAt'],
                $burialListItemData['burialPlaceType'],
                $burialListItemData['burialPlaceGraveSiteCemeteryBlockName'],
                $burialListItemData['burialPlaceGraveSiteRowInBlock'],
                $burialListItemData['burialPlaceGraveSitePositionInRow'],
                $burialListItemData['burialPlaceColumbariumNicheColumbariumName'],
                $burialListItemData['burialPlaceColumbariumNicheRowInColumbarium'],
                $burialListItemData['burialPlaceColumbariumNicheNumber'],
                $burialListItemData['burialPlaceMemorialTreeNumber'],
                $burialListItemData['customerType'],
                $burialListItemData['customerNaturalPersonFullName'],
                $burialListItemData['customerNaturalPersonAddress'],
                $burialListItemData['customerNaturalPersonPhone'],
                $burialListItemData['customerSoleProprietorName'],
                $burialListItemData['customerSoleProprietorRegistrationAddress'],
                $burialListItemData['customerSoleProprietorActualLocationAddress'],
                $burialListItemData['customerSoleProprietorPhone'],
                $burialListItemData['customerJuristicPersonName'],
                $burialListItemData['customerJuristicPersonLegalAddress'],
                $burialListItemData['customerJuristicPersonPostalAddress'],
                $burialListItemData['customerJuristicPersonPhone'],
            );
        }

        return new BurialList($burialListItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * @param $code
     *
     * @return string
     */
    private function formatCode($code): string
    {
        return \sprintf(BurialCode::CODE_FORMAT, $code);
    }
}
