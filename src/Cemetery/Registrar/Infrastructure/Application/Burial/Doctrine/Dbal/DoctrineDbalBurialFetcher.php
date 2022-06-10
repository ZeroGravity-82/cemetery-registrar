<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\ListBurials\BurialFetcher;
use Cemetery\Registrar\Application\ListBurials\BurialViewList;
use Cemetery\Registrar\Application\ListBurials\BurialViewListItem;
use Cemetery\Registrar\Application\UpdateBurial\BurialFormView;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Infrastructure\Application\Fetcher;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialFetcher extends Fetcher implements BurialFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getFormViewById(string $id): BurialFormView
    {
        $burialFormViewData = $this->queryBurialFormViewData($id);
        if ($burialFormViewData === false) {
            throw new \RuntimeException(\sprintf('Захоронение с ID "%s" не найдено.', $id));
        }

        return $this->hydrateBurialFormView($burialFormViewData);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): BurialViewList
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
    private function queryBurialFormViewData(string $id): false|array
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
     * @param array $burialFormViewData
     *
     * @return BurialFormView
     */
    private function hydrateBurialFormView(array $burialFormViewData): BurialFormView
    {
        return new BurialFormView(
            $burialFormViewData['id'],
            $this->formatCode($burialFormViewData['code']),
            $burialFormViewData['type'],
            $burialFormViewData['deceasedId'],
            $burialFormViewData['deceasedNaturalPersonId'],
            $burialFormViewData['deceasedNaturalPersonFullName'],
            $burialFormViewData['deceasedNaturalPersonBornAt'],
            $burialFormViewData['deceasedDiedAt'],
            $burialFormViewData['deceasedAge'],
            $burialFormViewData['deceasedDeathCertificateId'],
            $burialFormViewData['deceasedCauseOfDeath'],
            $burialFormViewData['customerId'],
            $burialFormViewData['customerType'],
            $burialFormViewData['customerNaturalPersonFullName'],
            $burialFormViewData['customerNaturalPersonPhone'],
            $burialFormViewData['customerNaturalPersonPhoneAdditional'],
            $burialFormViewData['customerNaturalPersonEmail'],
            $burialFormViewData['customerNaturalPersonAddress'],
            $burialFormViewData['customerNaturalPersonBornAt'],
            $burialFormViewData['customerNaturalPersonPlaceOfBirth'],
            $burialFormViewData['customerNaturalPersonPassportSeries'],
            $burialFormViewData['customerNaturalPersonPassportNumber'],
            $burialFormViewData['customerNaturalPersonPassportIssuedAt'],
            $burialFormViewData['customerNaturalPersonPassportIssuedBy'],
            match ($burialFormViewData['customerNaturalPersonPassportDivisionCode']) {
                'null'  => null,
                default => $burialFormViewData['customerNaturalPersonPassportDivisionCode'],
            },
            $burialFormViewData['customerSoleProprietorName'],
            $burialFormViewData['customerSoleProprietorInn'],
            $burialFormViewData['customerSoleProprietorOgrnip'],
            $burialFormViewData['customerSoleProprietorOkpo'],
            $burialFormViewData['customerSoleProprietorOkved'],
            $burialFormViewData['customerSoleProprietorRegistrationAddress'],
            $burialFormViewData['customerSoleProprietorActualLocationAddress'],
            $burialFormViewData['customerSoleProprietorBankDetailsBankName'],
            $burialFormViewData['customerSoleProprietorBankDetailsBik'],
            $burialFormViewData['customerSoleProprietorBankDetailsCorrespondentAccount'],
            $burialFormViewData['customerSoleProprietorBankDetailsCurrentAccount'],
            $burialFormViewData['customerSoleProprietorPhone'],
            $burialFormViewData['customerSoleProprietorPhoneAdditional'],
            $burialFormViewData['customerSoleProprietorFax'],
            $burialFormViewData['customerSoleProprietorEmail'],
            $burialFormViewData['customerSoleProprietorWebsite'],
            $burialFormViewData['customerJuristicPersonName'],
            $burialFormViewData['customerJuristicPersonInn'],
            $burialFormViewData['customerJuristicPersonKpp'],
            $burialFormViewData['customerJuristicPersonOgrn'],
            $burialFormViewData['customerJuristicPersonOkpo'],
            $burialFormViewData['customerJuristicPersonOkved'],
            $burialFormViewData['customerJuristicPersonLegalAddress'],
            $burialFormViewData['customerJuristicPersonPostalAddress'],
            $burialFormViewData['customerJuristicPersonBankDetailsBankName'],
            $burialFormViewData['customerJuristicPersonBankDetailsBik'],
            $burialFormViewData['customerJuristicPersonBankDetailsCorrespondentAccount'],
            $burialFormViewData['customerJuristicPersonBankDetailsCurrentAccount'],
            $burialFormViewData['customerJuristicPersonPhone'],
            $burialFormViewData['customerJuristicPersonPhoneAdditional'],
            $burialFormViewData['customerJuristicPersonFax'],
            $burialFormViewData['customerJuristicPersonGeneralDirector'],
            $burialFormViewData['customerJuristicPersonEmail'],
            $burialFormViewData['customerJuristicPersonWebsite'],
            $burialFormViewData['burialPlaceOwnerId'],
            $burialFormViewData['burialPlaceOwnerFullName'],
            $burialFormViewData['burialPlaceOwnerPhone'],
            $burialFormViewData['burialPlaceOwnerPhoneAdditional'],
            $burialFormViewData['burialPlaceOwnerEmail'],
            $burialFormViewData['burialPlaceOwnerAddress'],
            $burialFormViewData['burialPlaceOwnerBornAt'],
            $burialFormViewData['burialPlaceOwnerPlaceOfBirth'],
            $burialFormViewData['burialPlaceOwnerPassportSeries'],
            $burialFormViewData['burialPlaceOwnerPassportNumber'],
            $burialFormViewData['burialPlaceOwnerPassportIssuedAt'],
            $burialFormViewData['burialPlaceOwnerPassportIssuedBy'],
            match ($burialFormViewData['burialPlaceOwnerPassportDivisionCode']) {
                'null'  => null,
                default => $burialFormViewData['burialPlaceOwnerPassportDivisionCode'],
            },
            $burialFormViewData['funeralCompanyId'],
            $burialFormViewData['burialChainId'],
            $burialFormViewData['burialPlaceId'],
            $burialFormViewData['burialPlaceType'],
            $burialFormViewData['burialPlaceGraveSiteCemeteryBlockId'],
            $burialFormViewData['burialPlaceGraveSiteCemeteryBlockName'],
            $burialFormViewData['burialPlaceGraveSiteRowInBlock'],
            $burialFormViewData['burialPlaceGraveSitePositionInRow'],
            $burialFormViewData['burialPlaceGraveSiteSize'],
            $burialFormViewData['burialPlaceGraveSiteGeoPositionLatitude'],
            $burialFormViewData['burialPlaceGraveSiteGeoPositionLongitude'],
            match ($burialFormViewData['burialPlaceGraveSiteGeoPositionError']) {
                'null'  => null,
                default => $burialFormViewData['burialPlaceGraveSiteGeoPositionError'],
            },
            $burialFormViewData['burialPlaceColumbariumNicheColumbariumId'],
            $burialFormViewData['burialPlaceColumbariumNicheColumbariumName'],
            $burialFormViewData['burialPlaceColumbariumNicheRowInColumbarium'],
            $burialFormViewData['burialPlaceColumbariumNicheNumber'],
            $burialFormViewData['burialPlaceColumbariumNicheGeoPositionLatitude'],
            $burialFormViewData['burialPlaceColumbariumNicheGeoPositionLongitude'],
            match ($burialFormViewData['burialPlaceColumbariumNicheGeoPositionError']) {
                'null'  => null,
                default => $burialFormViewData['burialPlaceColumbariumNicheGeoPositionError'],
            },
            $burialFormViewData['burialPlaceMemorialTreeNumber'],
            $burialFormViewData['burialPlaceMemorialTreeGeoPositionLatitude'],
            $burialFormViewData['burialPlaceMemorialTreeGeoPositionLongitude'],
            match ($burialFormViewData['burialPlaceMemorialTreeGeoPositionError']) {
                'null'  => null,
                default => $burialFormViewData['burialPlaceMemorialTreeGeoPositionError'],
            },
            $burialFormViewData['burialContainerType'],
            match ($burialFormViewData['burialContainerCoffinSize']) {
                null    => null,
                default => (int) $burialFormViewData['burialContainerCoffinSize'],
            },
            $burialFormViewData['burialContainerCoffinShape'],
            match ($burialFormViewData['burialContainerCoffinIsNonStandard']) {
                'true'  => true,
                'false' => false,
                null    => null,
            },
            $burialFormViewData['buriedAt'],
            $burialFormViewData['updatedAt'],
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
     * @return BurialViewList
     */
    private function hydrateBurialViewList(
        array   $burialViewListData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): BurialViewList {
        $burialViewListItems = [];
        foreach ($burialViewListData as $burialViewListItemData) {
            $burialViewListItems[] = new BurialViewListItem(
                $burialViewListItemData['id'],
                $this->formatCode($burialViewListItemData['code']),
                $burialViewListItemData['deceasedNaturalPersonFullName'],
                $burialViewListItemData['deceasedNaturalPersonBornAt'],
                $burialViewListItemData['deceasedDiedAt'],
                $burialViewListItemData['deceasedAge'],
                $burialViewListItemData['buriedAt'],
                $burialViewListItemData['burialPlaceType'],
                $burialViewListItemData['burialPlaceGraveSiteCemeteryBlockName'],
                $burialViewListItemData['burialPlaceGraveSiteRowInBlock'],
                $burialViewListItemData['burialPlaceGraveSitePositionInRow'],
                $burialViewListItemData['burialPlaceColumbariumNicheColumbariumName'],
                $burialViewListItemData['burialPlaceColumbariumNicheRowInColumbarium'],
                $burialViewListItemData['burialPlaceColumbariumNicheNumber'],
                $burialViewListItemData['burialPlaceMemorialTreeNumber'],
                $burialViewListItemData['customerType'],
                $burialViewListItemData['customerNaturalPersonFullName'],
                $burialViewListItemData['customerNaturalPersonAddress'],
                $burialViewListItemData['customerNaturalPersonPhone'],
                $burialViewListItemData['customerSoleProprietorName'],
                $burialViewListItemData['customerSoleProprietorRegistrationAddress'],
                $burialViewListItemData['customerSoleProprietorActualLocationAddress'],
                $burialViewListItemData['customerSoleProprietorPhone'],
                $burialViewListItemData['customerJuristicPersonName'],
                $burialViewListItemData['customerJuristicPersonLegalAddress'],
                $burialViewListItemData['customerJuristicPersonPostalAddress'],
                $burialViewListItemData['customerJuristicPersonPhone'],
            );
        }

        return new BurialViewList($burialViewListItems, $page, $pageSize, $term, $totalCount, $totalPages);
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
