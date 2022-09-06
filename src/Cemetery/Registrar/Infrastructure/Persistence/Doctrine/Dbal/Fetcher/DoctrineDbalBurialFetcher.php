<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Fetcher;

use Cemetery\Registrar\Domain\Model\Burial\BurialCode;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcher;
use Cemetery\Registrar\Domain\View\Burial\BurialList;
use Cemetery\Registrar\Domain\View\Burial\BurialListItem;
use Cemetery\Registrar\Domain\View\Burial\BurialView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineDbalBurialFetcher extends DoctrineDbalFetcher implements BurialFetcher
{
    protected string $tableName = 'burial';

    public function findViewById(string $id): ?BurialView
    {
        $viewData = $this->queryViewData($id);

        return $viewData ? $this->hydrateView($viewData) : null;
    }

    public function findAll(?int $page = null, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): BurialList
    {
        $sql  = $this->buildFindAllSql($page, $term, $pageSize);
        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        $listData   = $result->fetchAllAssociative();
        $totalCount = $page !== null ? $this->doCountTotal($term) : \count($listData);
        $totalPages = $page !== null ? (int) \ceil($totalCount / $pageSize) : null;

        return $this->hydrateList($listData, $page, $pageSize, $term, $totalCount, $totalPages);
    }

    public function countTotal(): int
    {
        return $this->doCountTotal(null);
    }

    private function queryViewData(string $id): false|array
    {
        $sql  = $this->buildFindViewByIdSql();
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery();

        return $result->fetchAllAssociative()[0] ?? false;
    }

    private function doCountTotal(?string $term): int
    {
        $sql  = $this->buildCountTotalSql($term);
        $stmt = $this->connection->prepare($sql);
        $this->bindTermValue($stmt, $term);
        $result = $stmt->executeQuery();

        return $result->fetchFirstColumn()[0];
    }

    private function buildCountTotalSql(?string $term): string
    {
        $sql = \sprintf('SELECT COUNT(b.id) FROM %s AS b', $this->tableName);
        $sql = $this->appendFindAllJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);

        return $this->appendAndWhereLikeTermSql($sql, $term);
    }


    private function buildFindViewByIdSql(): string
    {
        $sql = <<<FIND_VIEW_BY_ID_SELECT_SQL
SELECT b.id                                                                              AS id,
       b.code                                                                            AS code,
       b.type                                                                            AS type,
       dnp.id                                                                            AS deceasedNaturalPersonId,
       dnp.full_name                                                                     AS deceasedNaturalPersonFullName,
       DATE_FORMAT(dnp.born_at, '%d.%m.%Y')                                              AS deceasedNaturalPersonBornAtFormatted,
       DATE_FORMAT(dnp.deceased_details->>"$.diedAt", '%d.%m.%Y')                        AS deceasedNaturalPersonDeceasedDetailsDiedAtFormatted,
       IF(
          dnp.deceased_details->>"$.age" <> 'null',
          dnp.deceased_details->>"$.age",
          NULL
       )                                                                                 AS deceasedNaturalPersonDeceasedDetailsAge,
       TIMESTAMPDIFF(YEAR, dnp.born_at, dnp.deceased_details->>"$.diedAt")               AS deceasedNaturalPersonDeceasedDetailsAgeCalculated,
       IF(
          dnp.deceased_details->>"$.causeOfDeathId" <> 'null',
          dnp.deceased_details->>"$.causeOfDeathId",
          NULL
       )                                                                                 AS deceasedNaturalPersonDeceasedDetailsCauseOfDeathId,
       dnp.deceased_details->>"$.deathCertificate.series"                                AS deceasedNaturalPersonDeceasedDetailsDeathCertificateSeries,
       dnp.deceased_details->>"$.deathCertificate.number"                                AS deceasedNaturalPersonDeceasedDetailsDeathCertificateNumber,
       DATE_FORMAT(dnp.deceased_details->>"$.deathCertificate.issuedAt", '%d.%m.%Y')     AS deceasedNaturalPersonDeceasedDetailsDeathCertificateIssuedAt,
       dnp.deceased_details->>"$.cremationCertificate.number"                            AS deceasedNaturalPersonDeceasedDetailsCremationCertificateNumber,
       DATE_FORMAT(dnp.deceased_details->>"$.cremationCertificate.issuedAt", '%d.%m.%Y') AS deceasedNaturalPersonDeceasedDetailsCremationCertificateIssuedAt,
       b.customer_id->>"$.value"                                                         AS customerId,
       b.customer_id->>"$.type"                                                          AS customerType,
       cnp.full_name                                                                     AS customerNaturalPersonFullName,
       cnp.phone                                                                         AS customerNaturalPersonPhone,
       cnp.phone_additional                                                              AS customerNaturalPersonPhoneAdditional,
       cnp.email                                                                         AS customerNaturalPersonEmail,
       cnp.address                                                                       AS customerNaturalPersonAddress,
       DATE_FORMAT(cnp.born_at, '%d.%m.%Y')                                              AS customerNaturalPersonBornAtFormatted,
       cnp.place_of_birth                                                                AS customerNaturalPersonPlaceOfBirth,
       cnp.passport->>"$.series"                                                         AS customerNaturalPersonPassportSeries,
       cnp.passport->>"$.number"                                                         AS customerNaturalPersonPassportNumber,
       DATE_FORMAT(cnp.passport->>"$.issuedAt", '%d.%m.%Y')                              AS customerNaturalPersonPassportIssuedAt,
       cnp.passport->>"$.issuedBy"                                                       AS customerNaturalPersonPassportIssuedBy,
       IF(
          cnp.passport->>"$.divisionCode" <> 'null',
          cnp.passport->>"$.divisionCode",
          NULL
       )                                                                                 AS customerNaturalPersonPassportDivisionCode,
       csp.name                                                                          AS customerSoleProprietorName,
       csp.inn                                                                           AS customerSoleProprietorInn,
       csp.ogrnip                                                                        AS customerSoleProprietorOgrnip,
       csp.okpo                                                                          AS customerSoleProprietorOkpo,
       csp.okved                                                                         AS customerSoleProprietorOkved,
       csp.registration_address                                                          AS customerSoleProprietorRegistrationAddress,
       csp.actual_location_address                                                       AS customerSoleProprietorActualLocationAddress,
       csp.bank_details->>"$.bankName"                                                   AS customerSoleProprietorBankDetailsBankName,
       csp.bank_details->>"$.bik"                                                        AS customerSoleProprietorBankDetailsBik,
       csp.bank_details->>"$.correspondentAccount"                                       AS customerSoleProprietorBankDetailsCorrespondentAccount,
       csp.bank_details->>"$.currentAccount"                                             AS customerSoleProprietorBankDetailsCurrentAccount,
       csp.phone                                                                         AS customerSoleProprietorPhone,
       csp.phone_additional                                                              AS customerSoleProprietorPhoneAdditional,
       csp.fax                                                                           AS customerSoleProprietorFax,
       csp.email                                                                         AS customerSoleProprietorEmail,
       csp.website                                                                       AS customerSoleProprietorWebsite,
       cjp.name                                                                          AS customerJuristicPersonName,
       cjp.inn                                                                           AS customerJuristicPersonInn,
       cjp.kpp                                                                           AS customerJuristicPersonKpp,
       cjp.ogrn                                                                          AS customerJuristicPersonOgrn,
       cjp.okpo                                                                          AS customerJuristicPersonOkpo,
       cjp.okved                                                                         AS customerJuristicPersonOkved,
       cjp.legal_address                                                                 AS customerJuristicPersonLegalAddress,
       cjp.postal_address                                                                AS customerJuristicPersonPostalAddress,
       cjp.bank_details->>"$.bankName"                                                   AS customerJuristicPersonBankDetailsBankName,
       cjp.bank_details->>"$.bik"                                                        AS customerJuristicPersonBankDetailsBik,
       cjp.bank_details->>"$.correspondentAccount"                                       AS customerJuristicPersonBankDetailsCorrespondentAccount,
       cjp.bank_details->>"$.currentAccount"                                             AS customerJuristicPersonBankDetailsCurrentAccount,
       cjp.phone                                                                         AS customerJuristicPersonPhone,
       cjp.phone_additional                                                              AS customerJuristicPersonPhoneAdditional,
       cjp.fax                                                                           AS customerJuristicPersonFax,
       cjp.general_director                                                              AS customerJuristicPersonGeneralDirector,
       cjp.email                                                                         AS customerJuristicPersonEmail,
       cjp.website                                                                       AS customerJuristicPersonWebsite,
       bpgspic.id                                                                        AS graveSitePersonInChargeId,
       bpgspic.full_name                                                                 AS graveSitePersonInChargeFullName,
       bpgspic.phone                                                                     AS graveSitePersonInChargePhone,
       bpgspic.phone_additional                                                          AS graveSitePersonInChargePhoneAdditional,
       bpgspic.email                                                                     AS graveSitePersonInChargeEmail,
       bpgspic.address                                                                   AS graveSitePersonInChargeAddress,
       DATE_FORMAT(bpgspic.born_at, '%d.%m.%Y')                                          AS graveSitePersonInChargeBornAtFormatted,
       bpgspic.place_of_birth                                                            AS graveSitePersonInChargePlaceOfBirth,
       bpgspic.passport->>"$.series"                                                     AS graveSitePersonInChargePassportSeries,
       bpgspic.passport->>"$.number"                                                     AS graveSitePersonInChargePassportNumber,
       DATE_FORMAT(bpgspic.passport->>"$.issuedAt", '%d.%m.%Y')                          AS graveSitePersonInChargePassportIssuedAt,
       bpgspic.passport->>"$.issuedBy"                                                   AS graveSitePersonInChargePassportIssuedBy,
       IF(
          bpgspic.passport->>"$.divisionCode" <> 'null',
          bpgspic.passport->>"$.divisionCode",
          NULL
       )                                                                                 AS graveSitePersonInChargePassportDivisionCode,
       bpcnpic.id                                                                        AS columbariumNichePersonInChargeId,
       bpcnpic.full_name                                                                 AS columbariumNichePersonInChargeFullName,
       bpcnpic.phone                                                                     AS columbariumNichePersonInChargePhone,
       bpcnpic.phone_additional                                                          AS columbariumNichePersonInChargePhoneAdditional,
       bpcnpic.email                                                                     AS columbariumNichePersonInChargeEmail,
       bpcnpic.address                                                                   AS columbariumNichePersonInChargeAddress,
       DATE_FORMAT(bpcnpic.born_at, '%d.%m.%Y')                                          AS columbariumNichePersonInChargeBornAtFormatted,
       bpcnpic.place_of_birth                                                            AS columbariumNichePersonInChargePlaceOfBirth,
       bpcnpic.passport->>"$.series"                                                     AS columbariumNichePersonInChargePassportSeries,
       bpcnpic.passport->>"$.number"                                                     AS columbariumNichePersonInChargePassportNumber,
       DATE_FORMAT(bpcnpic.passport->>"$.issuedAt", '%d.%m.%Y')                          AS columbariumNichePersonInChargePassportIssuedAt,
       bpcnpic.passport->>"$.issuedBy"                                                   AS columbariumNichePersonInChargePassportIssuedBy,
       IF(
          bpcnpic.passport->>"$.divisionCode" <> 'null',
          bpcnpic.passport->>"$.divisionCode",
          NULL
       )                                                                                 AS columbariumNichePersonInChargePassportDivisionCode,
       bpmtpic.id                                                                        AS memorialTreePersonInChargeId,
       bpmtpic.full_name                                                                 AS memorialTreePersonInChargeFullName,
       bpmtpic.phone                                                                     AS memorialTreePersonInChargePhone,
       bpmtpic.phone_additional                                                          AS memorialTreePersonInChargePhoneAdditional,
       bpmtpic.email                                                                     AS memorialTreePersonInChargeEmail,
       bpmtpic.address                                                                   AS memorialTreePersonInChargeAddress,
       DATE_FORMAT(bpmtpic.born_at, '%d.%m.%Y')                                          AS memorialTreePersonInChargeBornAtFormatted,
       bpmtpic.place_of_birth                                                            AS memorialTreePersonInChargePlaceOfBirth,
       bpmtpic.passport->>"$.series"                                                     AS memorialTreePersonInChargePassportSeries,
       bpmtpic.passport->>"$.number"                                                     AS memorialTreePersonInChargePassportNumber,
       DATE_FORMAT(bpmtpic.passport->>"$.issuedAt", '%d.%m.%Y')                          AS memorialTreePersonInChargePassportIssuedAt,
       bpmtpic.passport->>"$.issuedBy"                                                   AS memorialTreePersonInChargePassportIssuedBy,
       IF(
          bpmtpic.passport->>"$.divisionCode" <> 'null',
          bpmtpic.passport->>"$.divisionCode",
          NULL
       )                                                                                 AS memorialTreePersonInChargePassportDivisionCode,
       fc.id                                                                             AS funeralCompanyId,
       b.burial_chain_id                                                                 AS burialChainId,
       b.burial_place_id->>"$.value"                                                     AS burialPlaceId,
       b.burial_place_id->>"$.type"                                                      AS burialPlaceType,
       bpgscb.id                                                                         AS burialPlaceGraveSiteCemeteryBlockId,
       bpgscb.name                                                                       AS burialPlaceGraveSiteCemeteryBlockName,
       bpgs.row_in_block                                                                 AS burialPlaceGraveSiteRowInBlock,
       bpgs.position_in_row                                                              AS burialPlaceGraveSitePositionInRow,
       bpgs.size                                                                         AS burialPlaceGraveSiteSize,
       bpgs.geo_position->>"$.coordinates.latitude"                                      AS burialPlaceGraveSiteGeoPositionLatitude,
       bpgs.geo_position->>"$.coordinates.longitude"                                     AS burialPlaceGraveSiteGeoPositionLongitude,
       IF(
          bpgs.geo_position->>"$.error" <> 'null',
          bpgs.geo_position->>"$.error",
          NULL
       )                                                                                 AS burialPlaceGraveSiteGeoPositionError,
       bpcnc.id                                                                          AS burialPlaceColumbariumNicheColumbariumId,
       bpcnc.name                                                                        AS burialPlaceColumbariumNicheColumbariumName,
       bpcn.row_in_columbarium                                                           AS burialPlaceColumbariumNicheRowInColumbarium,
       bpcn.niche_number                                                                 AS burialPlaceColumbariumNicheNumber,
       bpcn.geo_position->>"$.coordinates.latitude"                                      AS burialPlaceColumbariumNicheGeoPositionLatitude,
       bpcn.geo_position->>"$.coordinates.longitude"                                     AS burialPlaceColumbariumNicheGeoPositionLongitude,
       IF(
          bpcn.geo_position->>"$.error" <> 'null',
          bpcn.geo_position->>"$.error",
          NULL
       )                                                                                 AS burialPlaceColumbariumNicheGeoPositionError,
       bpmt.tree_number                                                                  AS burialPlaceMemorialTreeNumber,
       bpmt.geo_position->>"$.coordinates.latitude"                                      AS burialPlaceMemorialTreeGeoPositionLatitude,
       bpmt.geo_position->>"$.coordinates.longitude"                                     AS burialPlaceMemorialTreeGeoPositionLongitude,
       IF(
          bpmt.geo_position->>"$.error" <> 'null',
          bpmt.geo_position->>"$.error",
          NULL
       )                                                                                 AS burialPlaceMemorialTreeGeoPositionError,
       b.burial_container->>"$.type"                                                     AS burialContainerType,
       b.burial_container->>"$.value.size"                                               AS burialContainerCoffinSize,
       b.burial_container->>"$.value.shape"                                              AS burialContainerCoffinShape,
       b.burial_container->>"$.value.isNonStandard"                                      AS burialContainerCoffinIsNonStandard,
       DATE_FORMAT(b.buried_at, '%d.%m.%Y %H:%i')                                        AS buriedAtFormatted,
       b.created_at                                                                      AS createdAt,
       b.updated_at                                                                      AS updatedAt
FROM $this->tableName AS b
FIND_VIEW_BY_ID_SELECT_SQL;

        $sql = $this->appendFindViewByIdJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);

        return $this->appendWhereIdIsEqualSql($sql);
    }

    private function buildFindAllSql(?int $page, ?string $term, int $pageSize): string
    {
        $sql = <<<FIND_ALL_SELECT_SQL
SELECT b.id                                                                AS id,
       b.code                                                              AS code,
       dnp.full_name                                                       AS deceasedNaturalPersonFullName,
       DATE_FORMAT(dnp.born_at, '%d.%m.%Y')                                AS deceasedNaturalPersonBornAtFormatted,
       DATE_FORMAT(dnp.deceased_details->>"$.diedAt", '%d.%m.%Y')          AS deceasedNaturalPersonDeceasedDetailsDiedAtFormatted,
       IF(
          dnp.deceased_details->>"$.age" <> 'null',
          dnp.deceased_details->>"$.age",
          NULL
       )                                                                   AS deceasedNaturalPersonDeceasedDetailsAge,
       TIMESTAMPDIFF(YEAR, dnp.born_at, dnp.deceased_details->>"$.diedAt") AS deceasedNaturalPersonDeceasedDetailsAgeCalculated,
       DATE_FORMAT(b.buried_at, '%d.%m.%Y %H:%i')                          AS buriedAtFormatted,
       b.burial_place_id->>"$.type"                                        AS burialPlaceType,
       bpgscb.name                                                         AS burialPlaceGraveSiteCemeteryBlockName,
       bpgs.row_in_block                                                   AS burialPlaceGraveSiteRowInBlock,
       bpgs.position_in_row                                                AS burialPlaceGraveSitePositionInRow,
       bpcnc.name                                                          AS burialPlaceColumbariumNicheColumbariumName,
       bpcn.row_in_columbarium                                             AS burialPlaceColumbariumNicheRowInColumbarium,
       bpcn.niche_number                                                   AS burialPlaceColumbariumNicheNumber,
       bpmt.tree_number                                                    AS burialPlaceMemorialTreeNumber,
       b.customer_id->>"$.type"                                            AS customerType,
       cnp.full_name                                                       AS customerNaturalPersonFullName,
       cnp.address                                                         AS customerNaturalPersonAddress,
       cnp.phone                                                           AS customerNaturalPersonPhone,
       csp.name                                                            AS customerSoleProprietorName,
       csp.registration_address                                            AS customerSoleProprietorRegistrationAddress,
       csp.actual_location_address                                         AS customerSoleProprietorActualLocationAddress,
       csp.phone                                                           AS customerSoleProprietorPhone,
       cjp.name                                                            AS customerJuristicPersonName,
       cjp.legal_address                                                   AS customerJuristicPersonLegalAddress,
       cjp.postal_address                                                  AS customerJuristicPersonPostalAddress,
       cjp.phone                                                           AS customerJuristicPersonPhone
FROM $this->tableName AS b
FIND_ALL_SELECT_SQL;

        $sql = $this->appendFindAllJoinsSql($sql);
        $sql = $this->appendWhereRemovedAtIsNullSql($sql);
        $sql = $this->appendAndWhereLikeTermSql($sql, $term);
        $sql = $this->appendOrderByCodeSql($sql);

        return $this->appendLimitOffset($sql, $page, $pageSize);
    }

    private function appendFindViewByIdJoinsSql(string $sql): string
    {
        return $sql . <<<FIND_VIEW_BY_ID_JOINS_SQL
  LEFT JOIN natural_person    AS dnp     ON b.deceased_id                 = dnp.id
  LEFT JOIN natural_person    AS cnp     ON b.customer_id->>"$.value"     = cnp.id
  LEFT JOIN sole_proprietor   AS csp     ON b.customer_id->>"$.value"     = csp.id
  LEFT JOIN juristic_person   AS cjp     ON b.customer_id->>"$.value"     = cjp.id
  LEFT JOIN funeral_company   AS fc      ON b.funeral_company_id          = fc.id
  LEFT JOIN grave_site        AS bpgs    ON b.burial_place_id->>"$.value" = bpgs.id
  LEFT JOIN cemetery_block    AS bpgscb  ON bpgs.cemetery_block_id        = bpgscb.id
  LEFT JOIN natural_person    AS bpgspic ON bpgs.person_in_charge_id      = bpgspic.id
  LEFT JOIN columbarium_niche AS bpcn    ON b.burial_place_id->>"$.value" = bpcn.id
  LEFT JOIN columbarium       AS bpcnc   ON bpcn.columbarium_id           = bpcnc.id
  LEFT JOIN natural_person    AS bpcnpic ON bpcn.person_in_charge_id      = bpcnpic.id
  LEFT JOIN memorial_tree     AS bpmt    ON b.burial_place_id->>"$.value" = bpmt.id
  LEFT JOIN natural_person    AS bpmtpic ON bpmt.person_in_charge_id      = bpmtpic.id
FIND_VIEW_BY_ID_JOINS_SQL;
    }

    private function appendFindAllJoinsSql(string $sql): string
    {
        return $sql . <<<FIND_ALL_JOINS_SQL
  LEFT JOIN natural_person    AS dnp    ON b.deceased_id                 = dnp.id
  LEFT JOIN grave_site        AS bpgs   ON b.burial_place_id->>"$.value" = bpgs.id
  LEFT JOIN cemetery_block    AS bpgscb ON bpgs.cemetery_block_id        = bpgscb.id
  LEFT JOIN columbarium_niche AS bpcn   ON b.burial_place_id->>"$.value" = bpcn.id
  LEFT JOIN columbarium       AS bpcnc  ON bpcn.columbarium_id           = bpcnc.id
  LEFT JOIN memorial_tree     AS bpmt   ON b.burial_place_id->>"$.value" = bpmt.id
  LEFT JOIN natural_person    AS cnp    ON b.customer_id->>"$.value"     = cnp.id
  LEFT JOIN sole_proprietor   AS csp    ON b.customer_id->>"$.value"     = csp.id
  LEFT JOIN juristic_person   AS cjp    ON b.customer_id->>"$.value"     = cjp.id
FIND_ALL_JOINS_SQL;
    }

    private function appendWhereRemovedAtIsNullSql(string $sql): string
    {
        return $sql . ' WHERE b.removed_at IS NULL';
    }

    private function appendWhereIdIsEqualSql(string $sql): string
    {
        return $sql . ' AND b.id = :id';
    }

    private function appendAndWhereLikeTermSql(string $sql, ?string $term): string
    {
        if ($this->isTermNotEmpty($term)) {
            $sql .= <<<LIKE_TERM_SQL
  AND (b.code                                                                           LIKE :term
    OR dnp.full_name                                                                    LIKE :term
    OR DATE_FORMAT(dnp.born_at, '%d.%m.%Y')                                             LIKE :term
    OR DATE_FORMAT(dnp.deceased_details->>"$.diedAt", '%d.%m.%Y')                       LIKE :term
    OR dnp.deceased_details->>"$.age"                                                   LIKE :term
    OR TIMESTAMPDIFF(YEAR, dnp.born_at, dnp.deceased_details->>"$.diedAt")              LIKE :term
    OR DATE_FORMAT(b.buried_at, '%d.%m.%Y %H:%i')                                       LIKE :term
    OR bpgscb.name                                                                      LIKE :term
    OR bpgs.row_in_block                                                                LIKE :term
    OR bpgs.position_in_row                                                             LIKE :term
    OR bpcnc.name                                                                       LIKE :term
    OR bpcn.row_in_columbarium                                                          LIKE :term
    OR bpcn.niche_number                                                                LIKE :term
    OR bpmt.tree_number                                                                 LIKE :term
    OR cnp.full_name                                                                    LIKE :term
    OR cnp.address                                                                      LIKE :term
    OR cnp.phone                                                                        LIKE :term
    OR csp.name                                                                         LIKE :term
    OR csp.registration_address                                                         LIKE :term
    OR csp.actual_location_address                                                      LIKE :term
    OR csp.phone                                                                        LIKE :term
    OR cjp.name                                                                         LIKE :term
    OR cjp.legal_address                                                                LIKE :term
    OR cjp.postal_address                                                               LIKE :term
    OR cjp.phone                                                                        LIKE :term)
LIKE_TERM_SQL;
        }

        return $sql;
    }

    private function appendOrderByCodeSql(string $sql): string
    {
        return $sql . ' ORDER BY b.code';
    }

    private function appendLimitOffset(string $sql, ?int $page, int $pageSize): string
    {
        if ($page !== null) {
            $sql .= \sprintf(' LIMIT %d OFFSET %d', $pageSize, ($page - 1) * $pageSize);
        }

        return $sql;
    }

    private function hydrateView(array $viewData): BurialView
    {
        return new BurialView(
            $viewData['id'],
            $this->formatCode($viewData['code']),
            $viewData['type'],
            $viewData['deceasedNaturalPersonId'],
            $viewData['deceasedNaturalPersonFullName'],
            $viewData['deceasedNaturalPersonBornAtFormatted'],
            $viewData['deceasedNaturalPersonDeceasedDetailsDiedAtFormatted'],
            match ($viewData['deceasedNaturalPersonDeceasedDetailsAge']) {
                null    => $viewData['deceasedNaturalPersonDeceasedDetailsAgeCalculated'],
                default => (int) $viewData['deceasedNaturalPersonDeceasedDetailsAge'],
            },
            $viewData['deceasedNaturalPersonDeceasedDetailsCauseOfDeathId'],
            $viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateSeries'],
            $viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateNumber'],
            $viewData['deceasedNaturalPersonDeceasedDetailsDeathCertificateIssuedAt'],
            $viewData['deceasedNaturalPersonDeceasedDetailsCremationCertificateNumber'],
            $viewData['deceasedNaturalPersonDeceasedDetailsCremationCertificateIssuedAt'],
            $viewData['customerId'],
            $viewData['customerType'],
            $viewData['customerNaturalPersonFullName'],
            $viewData['customerNaturalPersonPhone'],
            $viewData['customerNaturalPersonPhoneAdditional'],
            $viewData['customerNaturalPersonEmail'],
            $viewData['customerNaturalPersonAddress'],
            $viewData['customerNaturalPersonBornAtFormatted'],
            $viewData['customerNaturalPersonPlaceOfBirth'],
            $viewData['customerNaturalPersonPassportSeries'],
            $viewData['customerNaturalPersonPassportNumber'],
            $viewData['customerNaturalPersonPassportIssuedAt'],
            $viewData['customerNaturalPersonPassportIssuedBy'],
            $viewData['customerNaturalPersonPassportDivisionCode'],
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
            $viewData['graveSitePersonInChargeId'],
            $viewData['graveSitePersonInChargeFullName'],
            $viewData['graveSitePersonInChargePhone'],
            $viewData['graveSitePersonInChargePhoneAdditional'],
            $viewData['graveSitePersonInChargeEmail'],
            $viewData['graveSitePersonInChargeAddress'],
            $viewData['graveSitePersonInChargeBornAtFormatted'],
            $viewData['graveSitePersonInChargePlaceOfBirth'],
            $viewData['graveSitePersonInChargePassportSeries'],
            $viewData['graveSitePersonInChargePassportNumber'],
            $viewData['graveSitePersonInChargePassportIssuedAt'],
            $viewData['graveSitePersonInChargePassportIssuedBy'],
            $viewData['graveSitePersonInChargePassportDivisionCode'],
            $viewData['columbariumNichePersonInChargeId'],
            $viewData['columbariumNichePersonInChargeFullName'],
            $viewData['columbariumNichePersonInChargePhone'],
            $viewData['columbariumNichePersonInChargePhoneAdditional'],
            $viewData['columbariumNichePersonInChargeEmail'],
            $viewData['columbariumNichePersonInChargeAddress'],
            $viewData['columbariumNichePersonInChargeBornAtFormatted'],
            $viewData['columbariumNichePersonInChargePlaceOfBirth'],
            $viewData['columbariumNichePersonInChargePassportSeries'],
            $viewData['columbariumNichePersonInChargePassportNumber'],
            $viewData['columbariumNichePersonInChargePassportIssuedAt'],
            $viewData['columbariumNichePersonInChargePassportIssuedBy'],
            $viewData['columbariumNichePersonInChargePassportDivisionCode'],
            $viewData['memorialTreePersonInChargeId'],
            $viewData['memorialTreePersonInChargeFullName'],
            $viewData['memorialTreePersonInChargePhone'],
            $viewData['memorialTreePersonInChargePhoneAdditional'],
            $viewData['memorialTreePersonInChargeEmail'],
            $viewData['memorialTreePersonInChargeAddress'],
            $viewData['memorialTreePersonInChargeBornAtFormatted'],
            $viewData['memorialTreePersonInChargePlaceOfBirth'],
            $viewData['memorialTreePersonInChargePassportSeries'],
            $viewData['memorialTreePersonInChargePassportNumber'],
            $viewData['memorialTreePersonInChargePassportIssuedAt'],
            $viewData['memorialTreePersonInChargePassportIssuedBy'],
            $viewData['memorialTreePersonInChargePassportDivisionCode'],
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
            $viewData['burialPlaceGraveSiteGeoPositionError'],
            $viewData['burialPlaceColumbariumNicheColumbariumId'],
            $viewData['burialPlaceColumbariumNicheColumbariumName'],
            $viewData['burialPlaceColumbariumNicheRowInColumbarium'],
            $viewData['burialPlaceColumbariumNicheNumber'],
            $viewData['burialPlaceColumbariumNicheGeoPositionLatitude'],
            $viewData['burialPlaceColumbariumNicheGeoPositionLongitude'],
            $viewData['burialPlaceColumbariumNicheGeoPositionError'],
            $viewData['burialPlaceMemorialTreeNumber'],
            $viewData['burialPlaceMemorialTreeGeoPositionLatitude'],
            $viewData['burialPlaceMemorialTreeGeoPositionLongitude'],
            $viewData['burialPlaceMemorialTreeGeoPositionError'],
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
            $viewData['buriedAtFormatted'],
            $viewData['createdAt'],
            $viewData['updatedAt'],
        );
    }

    private function hydrateList(
        array   $listData,
        ?int    $page,
        int     $pageSize,
        ?string $term,
        int     $totalCount,
        ?int    $totalPages,
    ): BurialList {
        $listItems = [];
        foreach ($listData as $listItemData) {
            $listItems[] = new BurialListItem(
                $listItemData['id'],
                $this->formatCode($listItemData['code']),
                $listItemData['deceasedNaturalPersonFullName'],
                $listItemData['deceasedNaturalPersonBornAtFormatted'],
                $listItemData['deceasedNaturalPersonDeceasedDetailsDiedAtFormatted'],
                match ($listItemData['deceasedNaturalPersonDeceasedDetailsAge']) {
                    null    => $listItemData['deceasedNaturalPersonDeceasedDetailsAgeCalculated'],
                    default => (int) $listItemData['deceasedNaturalPersonDeceasedDetailsAge'],
                },
                $listItemData['buriedAtFormatted'],
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

    private function formatCode(string $code): string
    {
        return \sprintf(BurialCode::CODE_FORMAT, $code);
    }
}
