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
    public function findViewById(string $id): ?BurialView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): BurialList
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'b.id                                                                AS id',
                'b.code                                                              AS code',
                'dnp.full_name                                                       AS deceasedNaturalPersonFullName',
                'dnp.born_at                                                         AS deceasedNaturalPersonBornAt',
                'dnp.deceased_details->>"$.diedAt"                                   AS deceasedNaturalPersonDeceasedDetailsDiedAt',
                'dnp.deceased_details->>"$.age"                                      AS deceasedNaturalPersonDeceasedDetailsAge',
                'TIMESTAMPDIFF(YEAR, dnp.born_at, dnp.deceased_details->>"$.diedAt") AS deceasedNaturalPersonDeceasedDetailsAgeCalculated',
                'b.buried_at                                                         AS buriedAt',
                'b.burial_place_id->>"$.type"                                        AS burialPlaceType',
                'bpgscb.name                                                         AS burialPlaceGraveSiteCemeteryBlockName',
                'bpgs.row_in_block                                                   AS burialPlaceGraveSiteRowInBlock',
                'bpgs.position_in_row                                                AS burialPlaceGraveSitePositionInRow',
                'bpcnc.name                                                          AS burialPlaceColumbariumNicheColumbariumName',
                'bpcn.row_in_columbarium                                             AS burialPlaceColumbariumNicheRowInColumbarium',
                'bpcn.niche_number                                                   AS burialPlaceColumbariumNicheNumber',
                'bpmt.tree_number                                                    AS burialPlaceMemorialTreeNumber',
                'b.customer_id->>"$.type"                                            AS customerType',
                'cnp.full_name                                                       AS customerNaturalPersonFullName',
                'cnp.address                                                         AS customerNaturalPersonAddress',
                'cnp.phone                                                           AS customerNaturalPersonPhone',
                'csp.name                                                            AS customerSoleProprietorName',
                'csp.registration_address                                            AS customerSoleProprietorRegistrationAddress',
                'csp.actual_location_address                                         AS customerSoleProprietorActualLocationAddress',
                'csp.phone                                                           AS customerSoleProprietorPhone',
                'cjp.name                                                            AS customerJuristicPersonName',
                'cjp.legal_address                                                   AS customerJuristicPersonLegalAddress',
                'cjp.postal_address                                                  AS customerJuristicPersonPostalAddress',
                'cjp.phone                                                           AS customerJuristicPersonPhone',
            )
            ->from('burial', 'b')
            ->andWhere('b.removed_at IS NULL')
            ->orderBy('b.code')
            ->setFirstResult(($page - 1) * $pageSize)
            ->setMaxResults($pageSize);
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        $listData = $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative();
        $totalCount = $this->doCountTotal($term);
        $totalPages = (int) \ceil($totalCount / $pageSize);

        return $this->hydrateList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * {@inheritdoc}
     */
    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    /**
     * @param string $id
     *
     * @return false|array
     */
    private function queryViewData(string $id): false|array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'b.id                                                                AS id',
                'b.code                                                              AS code',
                'b.type                                                              AS type',
                'dnp.id                                                              AS deceasedNaturalPersonId',
                'dnp.full_name                                                       AS deceasedNaturalPersonFullName',
                'dnp.born_at                                                         AS deceasedNaturalPersonBornAt',
                'dnp.deceased_details->>"$.diedAt"                                   AS deceasedNaturalPersonDeceasedDetailsDiedAt',
                'dnp.deceased_details->>"$.age"                                      AS deceasedNaturalPersonDeceasedDetailsAge',
                'TIMESTAMPDIFF(YEAR, dnp.born_at, dnp.deceased_details->>"$.diedAt") AS deceasedNaturalPersonDeceasedDetailsAgeCalculated',
                'dnp.deceased_details->>"$.causeOfDeathId"                           AS deceasedNaturalPersonDeceasedDetailsCauseOfDeathId',
                'dnp.deceased_details->>"$.deathCertificate.series"                  AS deceasedNaturalPersonDeceasedDetailsDeathCertificateSeries',
                'dnp.deceased_details->>"$.deathCertificate.number"                  AS deceasedNaturalPersonDeceasedDetailsDeathCertificateNumber',
                'dnp.deceased_details->>"$.deathCertificate.issuedAt"                AS deceasedNaturalPersonDeceasedDetailsDeathCertificateIssuedAt',
                'dnp.deceased_details->>"$.cremationCertificate.number"              AS deceasedNaturalPersonDeceasedDetailsCremationCertificateNumber',
                'dnp.deceased_details->>"$.cremationCertificate.issuedAt"            AS deceasedNaturalPersonDeceasedDetailsCremationCertificateIssuedAt',
                'b.customer_id->>"$.value"                                           AS customerId',
                'b.customer_id->>"$.type"                                            AS customerType',
                'cnp.full_name                                                       AS customerNaturalPersonFullName',
                'cnp.phone                                                           AS customerNaturalPersonPhone',
                'cnp.phone_additional                                                AS customerNaturalPersonPhoneAdditional',
                'cnp.email                                                           AS customerNaturalPersonEmail',
                'cnp.address                                                         AS customerNaturalPersonAddress',
                'cnp.born_at                                                         AS customerNaturalPersonBornAt',
                'cnp.place_of_birth                                                  AS customerNaturalPersonPlaceOfBirth',
                'cnp.passport->>"$.series"                                           AS customerNaturalPersonPassportSeries',
                'cnp.passport->>"$.number"                                           AS customerNaturalPersonPassportNumber',
                'cnp.passport->>"$.issuedAt"                                         AS customerNaturalPersonPassportIssuedAt',
                'cnp.passport->>"$.issuedBy"                                         AS customerNaturalPersonPassportIssuedBy',
                'cnp.passport->>"$.divisionCode"                                     AS customerNaturalPersonPassportDivisionCode',
                'csp.name                                                            AS customerSoleProprietorName',
                'csp.inn                                                             AS customerSoleProprietorInn',
                'csp.ogrnip                                                          AS customerSoleProprietorOgrnip',
                'csp.okpo                                                            AS customerSoleProprietorOkpo',
                'csp.okved                                                           AS customerSoleProprietorOkved',
                'csp.registration_address                                            AS customerSoleProprietorRegistrationAddress',
                'csp.actual_location_address                                         AS customerSoleProprietorActualLocationAddress',
                'csp.bank_details->>"$.bankName"                                     AS customerSoleProprietorBankDetailsBankName',
                'csp.bank_details->>"$.bik"                                          AS customerSoleProprietorBankDetailsBik',
                'csp.bank_details->>"$.correspondentAccount"                         AS customerSoleProprietorBankDetailsCorrespondentAccount',
                'csp.bank_details->>"$.currentAccount"                               AS customerSoleProprietorBankDetailsCurrentAccount',
                'csp.phone                                                           AS customerSoleProprietorPhone',
                'csp.phone_additional                                                AS customerSoleProprietorPhoneAdditional',
                'csp.fax                                                             AS customerSoleProprietorFax',
                'csp.email                                                           AS customerSoleProprietorEmail',
                'csp.website                                                         AS customerSoleProprietorWebsite',
                'cjp.name                                                            AS customerJuristicPersonName',
                'cjp.inn                                                             AS customerJuristicPersonInn',
                'cjp.kpp                                                             AS customerJuristicPersonKpp',
                'cjp.ogrn                                                            AS customerJuristicPersonOgrn',
                'cjp.okpo                                                            AS customerJuristicPersonOkpo',
                'cjp.okved                                                           AS customerJuristicPersonOkved',
                'cjp.legal_address                                                   AS customerJuristicPersonLegalAddress',
                'cjp.postal_address                                                  AS customerJuristicPersonPostalAddress',
                'cjp.bank_details->>"$.bankName"                                     AS customerJuristicPersonBankDetailsBankName',
                'cjp.bank_details->>"$.bik"                                          AS customerJuristicPersonBankDetailsBik',
                'cjp.bank_details->>"$.correspondentAccount"                         AS customerJuristicPersonBankDetailsCorrespondentAccount',
                'cjp.bank_details->>"$.currentAccount"                               AS customerJuristicPersonBankDetailsCurrentAccount',
                'cjp.phone                                                           AS customerJuristicPersonPhone',
                'cjp.phone_additional                                                AS customerJuristicPersonPhoneAdditional',
                'cjp.fax                                                             AS customerJuristicPersonFax',
                'cjp.general_director                                                AS customerJuristicPersonGeneralDirector',
                'cjp.email                                                           AS customerJuristicPersonEmail',
                'cjp.website                                                         AS customerJuristicPersonWebsite',
                'picnp.id                                                            AS personInChargeId',
                'picnp.full_name                                                     AS personInChargeFullName',
                'picnp.phone                                                         AS personInChargePhone',
                'picnp.phone_additional                                              AS personInChargePhoneAdditional',
                'picnp.email                                                         AS personInChargeEmail',
                'picnp.address                                                       AS personInChargeAddress',
                'picnp.born_at                                                       AS personInChargeBornAt',
                'picnp.place_of_birth                                                AS personInChargePlaceOfBirth',
                'picnp.passport->>"$.series"                                         AS personInChargePassportSeries',
                'picnp.passport->>"$.number"                                         AS personInChargePassportNumber',
                'picnp.passport->>"$.issuedAt"                                       AS personInChargePassportIssuedAt',
                'picnp.passport->>"$.issuedBy"                                       AS personInChargePassportIssuedBy',
                'picnp.passport->>"$.divisionCode"                                   AS personInChargePassportDivisionCode',
                'fc.id                                                               AS funeralCompanyId',
                'b.burial_chain_id                                                   AS burialChainId',
                'b.burial_place_id->>"$.value"                                       AS burialPlaceId',
                'b.burial_place_id->>"$.type"                                        AS burialPlaceType',
                'bpgscb.id                                                           AS burialPlaceGraveSiteCemeteryBlockId',
                'bpgscb.name                                                         AS burialPlaceGraveSiteCemeteryBlockName',
                'bpgs.row_in_block                                                   AS burialPlaceGraveSiteRowInBlock',
                'bpgs.position_in_row                                                AS burialPlaceGraveSitePositionInRow',
                'bpgs.size                                                           AS burialPlaceGraveSiteSize',
                'bpgs.geo_position->>"$.coordinates.latitude"                        AS burialPlaceGraveSiteGeoPositionLatitude',
                'bpgs.geo_position->>"$.coordinates.longitude"                       AS burialPlaceGraveSiteGeoPositionLongitude',
                'bpgs.geo_position->>"$.error"                                       AS burialPlaceGraveSiteGeoPositionError',
                'bpcnc.id                                                            AS burialPlaceColumbariumNicheColumbariumId',
                'bpcnc.name                                                          AS burialPlaceColumbariumNicheColumbariumName',
                'bpcn.row_in_columbarium                                             AS burialPlaceColumbariumNicheRowInColumbarium',
                'bpcn.niche_number                                                   AS burialPlaceColumbariumNicheNumber',
                'bpcn.geo_position->>"$.coordinates.latitude"                        AS burialPlaceColumbariumNicheGeoPositionLatitude',
                'bpcn.geo_position->>"$.coordinates.longitude"                       AS burialPlaceColumbariumNicheGeoPositionLongitude',
                'bpcn.geo_position->>"$.error"                                       AS burialPlaceColumbariumNicheGeoPositionError',
                'bpmt.tree_number                                                    AS burialPlaceMemorialTreeNumber',
                'bpmt.geo_position->>"$.coordinates.latitude"                        AS burialPlaceMemorialTreeGeoPositionLatitude',
                'bpmt.geo_position->>"$.coordinates.longitude"                       AS burialPlaceMemorialTreeGeoPositionLongitude',
                'bpmt.geo_position->>"$.error"                                       AS burialPlaceMemorialTreeGeoPositionError',
                'b.burial_container->>"$.type"                                       AS burialContainerType',
                'b.burial_container->"$.value.size"                                  AS burialContainerCoffinSize',
                'b.burial_container->>"$.value.shape"                                AS burialContainerCoffinShape',
                'b.burial_container->>"$.value.isNonStandard"                        AS burialContainerCoffinIsNonStandard',
                'b.buried_at                                                         AS buriedAt',
                'b.created_at                                                        AS createdAt',
                'b.updated_at                                                        AS updatedAt',
            )
            ->from('burial', 'b')
            ->leftJoin('b',    'natural_person',    'dnp',    'b.deceased_id                 = dnp.id')
            ->leftJoin('b',    'natural_person',    'cnp',    'b.customer_id->>"$.value"     = cnp.id')
            ->leftJoin('b',    'sole_proprietor',   'csp',    'b.customer_id->>"$.value"     = csp.id')
            ->leftJoin('b',    'juristic_person',   'cjp',    'b.customer_id->>"$.value"     = cjp.id')
            ->leftJoin('b',    'natural_person',    'picnp',  'b.person_in_charge_id         = picnp.id')
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
    private function doCountTotal(?string $term): int
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('COUNT(b.id)')
            ->from('burial', 'b')
            ->andWhere('b.removed_at IS NULL');
        $this->appendJoins($queryBuilder);
        $this->appendAndWhereLikeTerm($queryBuilder, $term);
        $this->setTermParameter($queryBuilder, $term);

        return $queryBuilder
            ->executeQuery()
            ->fetchFirstColumn()[0];
    }

    /**
     * @param QueryBuilder $queryBuilder
     */
    private function appendJoins(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->leftJoin('b',    'natural_person',    'dnp',    'b.deceased_id                 = dnp.id')
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
    private function appendAndWhereLikeTerm(QueryBuilder $queryBuilder, ?string $term): void
    {
        if ($this->isTermNotEmpty($term)) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->or(
                        $queryBuilder->expr()->like('b.code', ':term'),
                        $queryBuilder->expr()->like('dnp.full_name', ':term'),
                        $queryBuilder->expr()->like('dnp.born_at', ':term'),
                        $queryBuilder->expr()->like('dnp.deceased_details->>"$.diedAt"', ':term'),
                        $queryBuilder->expr()->like('dnp.deceased_details->>"$.age"', ':term'),
                        $queryBuilder->expr()->like('TIMESTAMPDIFF(YEAR, dnp.born_at, dnp.deceased_details->>"$.diedAt")', ':term'),
                        $queryBuilder->expr()->like('b.buried_at', ':term'),
                        $queryBuilder->expr()->like('bpgscb.name', ':term'),
                        $queryBuilder->expr()->like('bpgs.row_in_block', ':term'),
                        $queryBuilder->expr()->like('bpgs.position_in_row', ':term'),
                        $queryBuilder->expr()->like('bpcnc.name', ':term'),
                        $queryBuilder->expr()->like('bpcn.row_in_columbarium', ':term'),
                        $queryBuilder->expr()->like('bpcn.niche_number', ':term'),
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
                );
        }
    }

    /**
     * @param array $viewData
     *
     * @return BurialView
     */
    private function hydrateView(array $viewData): BurialView
    {
        return new BurialView(
            $viewData['id'],
            $this->formatCode($viewData['code']),
            $viewData['type'],
            $viewData['deceasedNaturalPersonId'],
            $viewData['deceasedNaturalPersonFullName'],
            $viewData['deceasedNaturalPersonBornAt'],
            match ($viewData['deceasedNaturalPersonDeceasedDetailsDiedAt']) {
                'null'  => null,
                default => $viewData['deceasedNaturalPersonDeceasedDetailsDiedAt'],
            },
            match ($viewData['deceasedNaturalPersonDeceasedDetailsAge']) {
                'null'  => match ($viewData['deceasedNaturalPersonDeceasedDetailsAgeCalculated']) {
                        'null'  => null,
                        default => $viewData['deceasedNaturalPersonDeceasedDetailsAgeCalculated'],
                    },
                default => (int) $viewData['deceasedNaturalPersonDeceasedDetailsAge'],
            },
            match ($viewData['deceasedNaturalPersonDeceasedDetailsCauseOfDeathId']) {
                'null'  => null,
                default => $viewData['deceasedNaturalPersonDeceasedDetailsCauseOfDeathId'],
            },
            match ($viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateSeries']) {
                'null'  => null,
                default => $viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateSeries'],
            },
            match ($viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateNumber']) {
                'null'  => null,
                default => $viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateNumber'],
            },
            match ($viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateIssuedAt']) {
                'null'  => null,
                default => $viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateIssuedAt'],
            },
            match ($viewData['deceasedNaturalPersonDeceasedDetailsCremationCertificateNumber']) {
                'null'  => null,
                default => $viewData['deceasedNaturalPersonDeceasedDetailsCremationCertificateNumber'],
            },
            match ($viewData['deceasedNaturalPersonDeceasedDetailsCremationCertificateIssuedAt']) {
                'null'  => null,
                default => $viewData['deceasedNaturalPersonDeceasedDetailsCremationCertificateIssuedAt'],
            },
            $viewData['customerId'],
            $viewData['customerType'],
            $viewData['customerNaturalPersonFullName'],
            $viewData['customerNaturalPersonPhone'],
            $viewData['customerNaturalPersonPhoneAdditional'],
            $viewData['customerNaturalPersonEmail'],
            $viewData['customerNaturalPersonAddress'],
            $viewData['customerNaturalPersonBornAt'],
            $viewData['customerNaturalPersonPlaceOfBirth'],
            $viewData['customerNaturalPersonPassportSeries'],
            $viewData['customerNaturalPersonPassportNumber'],
            $viewData['customerNaturalPersonPassportIssuedAt'],
            $viewData['customerNaturalPersonPassportIssuedBy'],
            match ($viewData['customerNaturalPersonPassportDivisionCode']) {
                'null'  => null,
                default => $viewData['customerNaturalPersonPassportDivisionCode'],
            },
            $viewData['customerSoleProprietorName'],
            $viewData['customerSoleProprietorInn'],
            $viewData['customerSoleProprietorOgrnip'],
            $viewData['customerSoleProprietorOkpo'],
            $viewData['customerSoleProprietorOkved'],
            $viewData['customerSoleProprietorRegistrationAddress'],
            $viewData['customerSoleProprietorActualLocationAddress'],
            $viewData['customerSoleProprietorBankDetailsBankName'],
            $viewData['customerSoleProprietorBankDetailsBik'],
            $viewData['customerSoleProprietorBankDetailsCorrespondentAccount'],
            $viewData['customerSoleProprietorBankDetailsCurrentAccount'],
            $viewData['customerSoleProprietorPhone'],
            $viewData['customerSoleProprietorPhoneAdditional'],
            $viewData['customerSoleProprietorFax'],
            $viewData['customerSoleProprietorEmail'],
            $viewData['customerSoleProprietorWebsite'],
            $viewData['customerJuristicPersonName'],
            $viewData['customerJuristicPersonInn'],
            $viewData['customerJuristicPersonKpp'],
            $viewData['customerJuristicPersonOgrn'],
            $viewData['customerJuristicPersonOkpo'],
            $viewData['customerJuristicPersonOkved'],
            $viewData['customerJuristicPersonLegalAddress'],
            $viewData['customerJuristicPersonPostalAddress'],
            $viewData['customerJuristicPersonBankDetailsBankName'],
            $viewData['customerJuristicPersonBankDetailsBik'],
            $viewData['customerJuristicPersonBankDetailsCorrespondentAccount'],
            $viewData['customerJuristicPersonBankDetailsCurrentAccount'],
            $viewData['customerJuristicPersonPhone'],
            $viewData['customerJuristicPersonPhoneAdditional'],
            $viewData['customerJuristicPersonFax'],
            $viewData['customerJuristicPersonGeneralDirector'],
            $viewData['customerJuristicPersonEmail'],
            $viewData['customerJuristicPersonWebsite'],
            $viewData['personInChargeId'],
            $viewData['personInChargeFullName'],
            $viewData['personInChargePhone'],
            $viewData['personInChargePhoneAdditional'],
            $viewData['personInChargeEmail'],
            $viewData['personInChargeAddress'],
            $viewData['personInChargeBornAt'],
            $viewData['personInChargePlaceOfBirth'],
            $viewData['personInChargePassportSeries'],
            $viewData['personInChargePassportNumber'],
            $viewData['personInChargePassportIssuedAt'],
            $viewData['personInChargePassportIssuedBy'],
            match ($viewData['personInChargePassportDivisionCode']) {
                'null'  => null,
                default => $viewData['personInChargePassportDivisionCode'],
            },
            $viewData['funeralCompanyId'],
            $viewData['burialChainId'],
            $viewData['burialPlaceId'],
            $viewData['burialPlaceType'],
            $viewData['burialPlaceGraveSiteCemeteryBlockId'],
            $viewData['burialPlaceGraveSiteCemeteryBlockName'],
            $viewData['burialPlaceGraveSiteRowInBlock'],
            $viewData['burialPlaceGraveSitePositionInRow'],
            $viewData['burialPlaceGraveSiteSize'],
            $viewData['burialPlaceGraveSiteGeoPositionLatitude'],
            $viewData['burialPlaceGraveSiteGeoPositionLongitude'],
            match ($viewData['burialPlaceGraveSiteGeoPositionError']) {
                'null'  => null,
                default => $viewData['burialPlaceGraveSiteGeoPositionError'],
            },
            $viewData['burialPlaceColumbariumNicheColumbariumId'],
            $viewData['burialPlaceColumbariumNicheColumbariumName'],
            $viewData['burialPlaceColumbariumNicheRowInColumbarium'],
            $viewData['burialPlaceColumbariumNicheNumber'],
            $viewData['burialPlaceColumbariumNicheGeoPositionLatitude'],
            $viewData['burialPlaceColumbariumNicheGeoPositionLongitude'],
            match ($viewData['burialPlaceColumbariumNicheGeoPositionError']) {
                'null'  => null,
                default => $viewData['burialPlaceColumbariumNicheGeoPositionError'],
            },
            $viewData['burialPlaceMemorialTreeNumber'],
            $viewData['burialPlaceMemorialTreeGeoPositionLatitude'],
            $viewData['burialPlaceMemorialTreeGeoPositionLongitude'],
            match ($viewData['burialPlaceMemorialTreeGeoPositionError']) {
                'null'  => null,
                default => $viewData['burialPlaceMemorialTreeGeoPositionError'],
            },
            $viewData['burialContainerType'],
            match ($viewData['burialContainerCoffinSize']) {
                null    => null,
                default => (int) $viewData['burialContainerCoffinSize'],
            },
            $viewData['burialContainerCoffinShape'],
            match ($viewData['burialContainerCoffinIsNonStandard']) {
                'true'  => true,
                'false' => false,
                null    => null,
            },
            $viewData['buriedAt'],
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    /**
     * @param array       $listData
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $term
     * @param int         $totalCount
     * @param int         $totalPages
     *
     * @return BurialList
     */
    private function hydrateList(
        array   $listData,
        int     $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        int     $totalPages,
    ): BurialList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new BurialListItem(
                $listItemData['id'],
                $this->formatCode($listItemData['code']),
                $listItemData['deceasedNaturalPersonFullName'],
                $listItemData['deceasedNaturalPersonBornAt'],
                match ($listItemData['deceasedNaturalPersonDeceasedDetailsDiedAt']) {
                    'null'  => null,
                    default => $listItemData['deceasedNaturalPersonDeceasedDetailsDiedAt'],
                },
                match ($listItemData['deceasedNaturalPersonDeceasedDetailsAge']) {
                    'null'  => match ($listItemData['deceasedNaturalPersonDeceasedDetailsAgeCalculated']) {
                            'null'  => null,
                            default => $listItemData['deceasedNaturalPersonDeceasedDetailsAgeCalculated'],
                        },
                    default => (int) $listItemData['deceasedNaturalPersonDeceasedDetailsAge'],
                },
                $listItemData['buriedAt'],
                $listItemData['burialPlaceType'],
                $listItemData['burialPlaceGraveSiteCemeteryBlockName'],
                $listItemData['burialPlaceGraveSiteRowInBlock'],
                $listItemData['burialPlaceGraveSitePositionInRow'],
                $listItemData['burialPlaceColumbariumNicheColumbariumName'],
                $listItemData['burialPlaceColumbariumNicheRowInColumbarium'],
                $listItemData['burialPlaceColumbariumNicheNumber'],
                $listItemData['burialPlaceMemorialTreeNumber'],
                $listItemData['customerType'],
                $listItemData['customerNaturalPersonFullName'],
                $listItemData['customerNaturalPersonAddress'],
                $listItemData['customerNaturalPersonPhone'],
                $listItemData['customerSoleProprietorName'],
                $listItemData['customerSoleProprietorRegistrationAddress'],
                $listItemData['customerSoleProprietorActualLocationAddress'],
                $listItemData['customerSoleProprietorPhone'],
                $listItemData['customerJuristicPersonName'],
                $listItemData['customerJuristicPersonLegalAddress'],
                $listItemData['customerJuristicPersonPostalAddress'],
                $listItemData['customerJuristicPersonPhone'],
            );
        }

        return new BurialList($listItems, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    private function formatCode(string $code): string
    {
        return \sprintf(BurialCode::CODE_FORMAT, $code);
    }
}
