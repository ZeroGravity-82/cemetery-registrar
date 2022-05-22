<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Application\Burial\Doctrine\Dbal;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Cemetery\Registrar\Application\Burial\BurialFormView;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTree;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Infrastructure\Application\Fetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DoctrineDbalBurialFetcher extends Fetcher implements BurialFetcher
{
    /**
     * {@inheritdoc}
     */
    public function getFormViewById(string $id): BurialFormView
    {
        $burialFormViewData = $this->initializeBurialViewData($id);
        $this->fillBurialPlaceOwnerPassportData($burialFormViewData);
        $this->fillBurialContainerData($burialFormViewData);
        $this->fillCustomerData($burialFormViewData);
        $this->fillFuneralCompanyData($burialFormViewData);
        $this->fillBurialPlaceData($burialFormViewData);

        return $this->hydrateBurialView($burialFormViewData);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $page, ?string $term = null, int $pageSize = self::DEFAULT_PAGE_SIZE): array
    {
//        $result = $this->connection->createQueryBuilder()
//            ->select(
//                'b.id              AS id',
//                'b.code            AS code',
//                'dnp.full_name     AS deceasedNaturalPersonFullName',
//                'dnp.born_at       AS deceasedNaturalPersonBornAt',
//                'd.died_at         AS deceasedDiedAt',
//                'd.age             AS deceasedAge',
//                'b.buried_at       AS buriedAt',
//                'b.burial_place_id AS burialPlaceIdJson',
//                'b.customer_id     AS customerIdJson',
//            )
//            ->from('burial', 'b')
//            ->leftJoin('b', 'deceased', 'd', 'b.deceased_id = d.id')
//            ->leftJoin('d', 'natural_person', 'dnp', 'd.natural_person_id = dnp.id')
//            ->andWhere()
//            ->setFirstResult()
//            ->setMaxResults()
//            ->executeQuery();
        return [];
    }

    /**
     * Initializes an array with as many burial view data as possible that can be queried with a single SQL query.
     * The rest of the data can be obtained using separate SQL queries only after decoding JSON fields.
     *
     * @param string $id
     *
     * @return array
     *
     * @throws \RuntimeException when the burial is not found by ID
     */
    private function initializeBurialViewData(string $id): array
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'b.id                                         AS id',
                'b.code                                       AS code',
                'b.type                                       AS type',
                'd.id                                         AS deceasedId',
                'dnp.id                                       AS deceasedNaturalPersonId',
                'dnp.full_name                                AS deceasedNaturalPersonFullName',
                'dnp.born_at                                  AS deceasedNaturalPersonBornAt',
                'd.died_at                                    AS deceasedDiedAt',
                'd.age                                        AS deceasedAge',
                'd.death_certificate_id                       AS deceasedDeathCertificateId',
                'd.cause_of_death                             AS deceasedCauseOfDeath',
                'b.customer_id->>"$.value"                    AS customerId',
                'b.customer_id->>"$.type"                     AS customerType',
                'cnp.full_name                                AS customerNaturalPersonFullName',
                'cnp.phone                                    AS customerNaturalPersonPhone',
                'cnp.phone_additional                         AS customerNaturalPersonPhoneAdditional',
                'cnp.email                                    AS customerNaturalPersonEmail',
                'cnp.address                                  AS customerNaturalPersonAddress',
                'cnp.born_at                                  AS customerNaturalPersonBornAt',
                'cnp.place_of_birth                           AS customerNaturalPersonPlaceOfBirth',
                'cnp.passport->>"$.series"                    AS customerNaturalPersonPassportSeries',
                'cnp.passport->>"$.number"                    AS customerNaturalPersonPassportNumber',
                'cnp.passport->>"$.issuedAt"                  AS customerNaturalPersonPassportIssuedAt',
                'cnp.passport->>"$.issuedBy"                  AS customerNaturalPersonPassportIssuedBy',
                'cnp.passport->>"$.divisionCode"              AS customerNaturalPersonPassportDivisionCode',
                'csp.name                                     AS customerSoleProprietorName',
                'csp.inn                                      AS customerSoleProprietorInn',
                'csp.ogrnip                                   AS customerSoleProprietorOgrnip',
                'csp.okpo                                     AS customerSoleProprietorOkpo',
                'csp.okved                                    AS customerSoleProprietorOkved',
                'csp.registration_address                     AS customerSoleProprietorRegistrationAddress',
                'csp.actual_location_address                  AS customerSoleProprietorActualLocationAddress',
                'csp.bank_details->>"$.bankName"              AS customerSoleProprietorBankDetailsBankName',
                'csp.bank_details->>"$.bik"                   AS customerSoleProprietorBankDetailsBik',
                'csp.bank_details->>"$.correspondentAccount"  AS customerSoleProprietorBankDetailsCorrespondentAccount',
                'csp.bank_details->>"$.currentAccount"        AS customerSoleProprietorBankDetailsCurrentAccount',
                'csp.phone                                    AS customerSoleProprietorPhone',
                'csp.phone_additional                         AS customerSoleProprietorPhoneAdditional',
                'csp.fax                                      AS customerSoleProprietorFax',
                'csp.email                                    AS customerSoleProprietorEmail',
                'csp.website                                  AS customerSoleProprietorWebsite',
                'cjp.name                                     AS customerJuristicPersonName',
                'cjp.inn                                      AS customerJuristicPersonInn',
                'cjp.kpp                                      AS customerJuristicPersonKpp',
                'cjp.ogrn                                     AS customerJuristicPersonOgrn',
                'cjp.okpo                                     AS customerJuristicPersonOkpo',
                'cjp.okved                                    AS customerJuristicPersonOkved',
                'cjp.legal_address                            AS customerJuristicPersonLegalAddress',
                'cjp.postal_address                           AS customerJuristicPersonPostalAddress',
                'cjp.bank_details->>"$.bankName"              AS customerSoleProprietorBankDetailsBankName',
                'cjp.bank_details->>"$.bik"                   AS customerSoleProprietorBankDetailsBik',
                'cjp.bank_details->>"$.correspondentAccount"  AS customerSoleProprietorBankDetailsCorrespondentAccount',
                'cjp.bank_details->>"$.currentAccount"        AS customerSoleProprietorBankDetailsCurrentAccount',
                'cjp.phone                                    AS customerJuristicPersonPhone',
                'cjp.phone_additional                         AS customerJuristicPersonPhoneAdditional',
                'cjp.fax                                      AS customerJuristicPersonFax',
                'cjp.general_director                         AS customerJuristicPersonGeneralDirector',
                'cjp.email                                    AS customerJuristicPersonEmail',
                'cjp.website                                  AS customerJuristicPersonWebsite',
                'bponp.id                                     AS burialPlaceOwnerId',
                'bponp.full_name                              AS burialPlaceOwnerFullName',
                'bponp.phone                                  AS burialPlaceOwnerPhone',
                'bponp.phone_additional                       AS burialPlaceOwnerPhoneAdditional',
                'bponp.email                                  AS burialPlaceOwnerEmail',
                'bponp.address                                AS burialPlaceOwnerAddress',
                'bponp.born_at                                AS burialPlaceOwnerBornAt',
                'bponp.place_of_birth                         AS burialPlaceOwnerPlaceOfBirth',
                'bponp.passport->>"$.series"                  AS burialPlaceOwnerPassportSeries',
                'bponp.passport->>"$.number"                  AS burialPlaceOwnerPassportNumber',
                'bponp.passport->>"$.issuedAt"                AS burialPlaceOwnerPassportIssuedAt',
                'bponp.passport->>"$.issuedBy"                AS burialPlaceOwnerPassportIssuedBy',
                'bponp.passport->>"$.divisionCode"            AS burialPlaceOwnerPassportDivisionCode',
                'b.funeral_company_id->>"$.value"             AS funeralCompanyId',
                'b.funeral_company_id->>"$.type"              AS funeralCompanyType',
                'fcsp.name                                    AS funeralCompanySoleProprietorName',
                'fcsp.inn                                     AS funeralCompanySoleProprietorInn',
                'fcsp.ogrnip                                  AS funeralCompanySoleProprietorOgrnip',
                'fcsp.okpo                                    AS funeralCompanySoleProprietorOkpo',
                'fcsp.okved                                   AS funeralCompanySoleProprietorOkved',
                'fcsp.registration_address                    AS funeralCompanySoleProprietorRegistrationAddress',
                'fcsp.actual_location_address                 AS funeralCompanySoleProprietorActualLocationAddress',
                'fcsp.bank_details->>"$.bankName"             AS funeralCompanySoleProprietorBankDetailsBankName',
                'fcsp.bank_details->>"$.bik"                  AS funeralCompanySoleProprietorBankDetailsBik',
                'fcsp.bank_details->>"$.correspondentAccount" AS funeralCompanySoleProprietorBankDetailsCorrespondentAccount',
                'fcsp.bank_details->>"$.currentAccount"       AS funeralCompanySoleProprietorBankDetailsCurrentAccount',
                'fcsp.phone                                   AS funeralCompanySoleProprietorPhone',
                'fcsp.phone_additional                        AS funeralCompanySoleProprietorPhoneAdditional',
                'fcsp.fax                                     AS funeralCompanySoleProprietorFax',
                'fcsp.email                                   AS funeralCompanySoleProprietorEmail',
                'fcsp.website                                 AS funeralCompanySoleProprietorWebsite',
                'fcjp.name                                    AS funeralCompanyJuristicPersonName',
                'fcjp.inn                                     AS funeralCompanyJuristicPersonInn',
                'fcjp.kpp                                     AS funeralCompanyJuristicPersonKpp',
                'fcjp.ogrn                                    AS funeralCompanyJuristicPersonOgrn',
                'fcjp.okpo                                    AS funeralCompanyJuristicPersonOkpo',
                'fcjp.okved                                   AS funeralCompanyJuristicPersonOkved',
                'fcjp.legal_address                           AS funeralCompanyJuristicPersonLegalAddress',
                'fcjp.postal_address                          AS funeralCompanyJuristicPersonPostalAddress',
                'fcjp.bank_details->>"$.bankName"             AS funeralCompanyJuristicPersonBankDetailsBankName',
                'fcjp.bank_details->>"$.bik"                  AS funeralCompanyJuristicPersonBankDetailsBik',
                'fcjp.bank_details->>"$.correspondentAccount" AS funeralCompanyJuristicPersonBankDetailsCorrespondentAccount',
                'fcjp.bank_details->>"$.currentAccount"       AS funeralCompanyJuristicPersonBankDetailsCurrentAccount',
                'fcjp.phone                                   AS funeralCompanyJuristicPersonPhone',
                'fcjp.phone_additional                        AS funeralCompanyJuristicPersonPhoneAdditional',
                'fcjp.fax                                     AS funeralCompanyJuristicPersonFax',
                'fcjp.general_director                        AS funeralCompanyJuristicPersonGeneralDirector',
                'fcjp.email                                   AS funeralCompanyJuristicPersonEmail',
                'fcjp.website                                 AS funeralCompanyJuristicPersonWebsite',
                'b.burial_chain_id                            AS burialChainId',
                'b.burial_place_id->>"$.value"                AS burialPlaceId',
                'b.burial_place_id->>"$.type"                 AS burialPlaceType',




                'cb.id              AS burialPlaceGraveSiteCemeteryBlockId',
                'cb.name            AS burialPlaceGraveSiteCemeteryBlockName',
                'gs.row_in_block    AS burialPlaceGraveSiteRowInBlock',
                'gs.position_in_row AS burialPlaceGraveSitePositionInRow',
                'gs.size            AS burialPlaceGraveSiteSize',
                'gs.geo_position    AS burialPlaceGraveSiteGeoPositionJson',




                'cl.id                       AS burialPlaceColumbariumNicheColumbariumId',
                'cl.name                     AS burialPlaceColumbariumNicheColumbariumName',
                'cn.row_in_columbarium       AS burialPlaceColumbariumNicheRowInColumbarium',
                'cn.columbarium_niche_number AS burialPlaceColumbariumNicheNicheNumber',
                'cn.geo_position             AS burialPlaceColumbariumNicheGeoPositionJson',


                'mt.tree_number  AS burialPlaceMemorialTreeNumber',
                'mt.geo_position AS burialPlaceMemorialTreeGeoPositionJson',







                'b.burial_container      AS burialContainerJson',
                'b.buried_at             AS buriedAt',
                'b.updated_at            AS updatedAt',
            )
            ->from('burial', 'b')
            ->leftJoin('b',    'deceased',          'd',      'b.deceased_id = d.id')
            ->leftJoin('d',    'natural_person',    'dnp',    'd.natural_person_id = dnp.id')
            ->leftJoin('b',    'natural_person',    'cnp',    'b.customer_id->>"$.value" = cnp.id')
            ->leftJoin('b',    'sole_proprietor',   'csp',    'b.customer_id->>"$.value" = csp.id')
            ->leftJoin('b',    'juristic_person',   'cjp',    'b.customer_id->>"$.value" = cjp.id')
            ->leftJoin('b',    'natural_person',    'bponp',  'b.burial_place_owner_id = bponp.id')
            ->leftJoin('b',    'sole_proprietor',   'fcsp',   'b.funeral_company_id->>"$.value" = fcsp.id')
            ->leftJoin('b',    'juristic_person',   'fcjp',   'b.funeral_company_id->>"$.value" = fcjp.id')
            ->leftJoin('b',    'grave_site',        'bpgs',   'b.burial_place_id->>"$.value" = bpgs.id')
            ->leftJoin('bpgs', 'cemetery_block',    'bpgscb', 'bpgs.cemetery_block_id = bpgscb.id')
            ->leftJoin('b',    'columbarium_niche', 'bpcn',   'b.burial_place_id->>"$.value" = bpcn.id')
            ->leftJoin('bpcn', 'columbarium',       'bpcnc',  'bpcn.columbarium_id = bpcnc.id')
            ->leftJoin('b',    'memorial_tree',     'bpmt',   'b.burial_place_id->>"$.value" = bpmt.id')
            ->andWhere('b.id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $burialFormViewData = $result->fetchAssociative();
        if ($burialFormViewData === false) {
            throw new \RuntimeException(\sprintf('Захоронение с ID "%s" не найдено.', $id));
        }

        return $burialFormViewData;
    }

    /**
     * @param array $burialFormViewData
     */
    private function fillBurialPlaceOwnerPassportData(array &$burialFormViewData): void
    {
        $burialFormViewData['burialPlaceOwnerPassportSeries']       = null;
        $burialFormViewData['burialPlaceOwnerPassportNumber']       = null;
        $burialFormViewData['burialPlaceOwnerPassportIssuedAt']     = null;
        $burialFormViewData['burialPlaceOwnerPassportIssuedBy']     = null;
        $burialFormViewData['burialPlaceOwnerPassportDivisionCode'] = null;
        $this->fillPassportDataFromJson(
            $burialFormViewData['burialPlaceOwnerPassportJson'] ?? null,
            $burialFormViewData,
            'burialPlaceOwner'
        );
        unset($burialFormViewData['burialPlaceOwnerPassportJson']);
    }

    /**
     * @param array $burialFormViewData
     */
    private function fillBurialContainerData(array &$burialFormViewData): void
    {
        $burialFormViewData['burialContainerCoffinSize']          = null;
        $burialFormViewData['burialContainerCoffinShape']         = null;
        $burialFormViewData['burialContainerCoffinIsNonStandard'] = null;
        $burialContainerData = isset($burialFormViewData['burialContainerJson'])
            ? \json_decode($burialFormViewData['burialContainerJson'], true)
            : null;
        $burialFormViewData['burialContainerType'] = $burialContainerData['type'] ?? null;
        switch ($burialFormViewData['burialContainerType']) {
            case Coffin::CLASS_SHORTCUT:
                $burialFormViewData['burialContainerCoffinSize'] = $burialContainerData['value']['size'];
                $burialFormViewData['burialContainerCoffinShape'] = $burialContainerData['value']['shape'];
                $burialFormViewData['burialContainerCoffinIsNonStandard'] = $burialContainerData['value']['isNonStandard'];
                break;
            case Urn::CLASS_SHORTCUT:
                break;
        }
        unset($burialFormViewData['burialContainerJson']);
    }

    /**
     * @param array $burialFormViewData
     */
    private function fillCustomerData(array &$burialFormViewData): void
    {
        $burialFormViewData['customerNaturalPersonFullName']                         = null;
        $burialFormViewData['customerNaturalPersonPhone']                            = null;
        $burialFormViewData['customerNaturalPersonPhoneAdditional']                  = null;
        $burialFormViewData['customerNaturalPersonEmail']                            = null;
        $burialFormViewData['customerNaturalPersonAddress']                          = null;
        $burialFormViewData['customerNaturalPersonBornAt']                           = null;
        $burialFormViewData['customerNaturalPersonPlaceOfBirth']                     = null;
        $burialFormViewData['customerNaturalPersonPassportSeries']                   = null;
        $burialFormViewData['customerNaturalPersonPassportNumber']                   = null;
        $burialFormViewData['customerNaturalPersonPassportIssuedAt']                 = null;
        $burialFormViewData['customerNaturalPersonPassportIssuedBy']                 = null;
        $burialFormViewData['customerNaturalPersonPassportDivisionCode']             = null;
        $burialFormViewData['customerSoleProprietorName']                            = null;
        $burialFormViewData['customerSoleProprietorInn']                             = null;
        $burialFormViewData['customerSoleProprietorOgrnip']                          = null;
        $burialFormViewData['customerSoleProprietorOkpo']                            = null;
        $burialFormViewData['customerSoleProprietorOkved']                           = null;
        $burialFormViewData['customerSoleProprietorRegistrationAddress']             = null;
        $burialFormViewData['customerSoleProprietorActualLocationAddress']           = null;
        $burialFormViewData['customerSoleProprietorBankDetailsBankName']             = null;
        $burialFormViewData['customerSoleProprietorBankDetailsBik']                  = null;
        $burialFormViewData['customerSoleProprietorBankDetailsCorrespondentAccount'] = null;
        $burialFormViewData['customerSoleProprietorBankDetailsCurrentAccount']       = null;
        $burialFormViewData['customerSoleProprietorPhone']                           = null;
        $burialFormViewData['customerSoleProprietorPhoneAdditional']                 = null;
        $burialFormViewData['customerSoleProprietorFax']                             = null;
        $burialFormViewData['customerSoleProprietorEmail']                           = null;
        $burialFormViewData['customerSoleProprietorWebsite']                         = null;
        $burialFormViewData['customerJuristicPersonName']                            = null;
        $burialFormViewData['customerJuristicPersonInn']                             = null;
        $burialFormViewData['customerJuristicPersonKpp']                             = null;
        $burialFormViewData['customerJuristicPersonOgrn']                            = null;
        $burialFormViewData['customerJuristicPersonOkpo']                            = null;
        $burialFormViewData['customerJuristicPersonOkved']                           = null;
        $burialFormViewData['customerJuristicPersonLegalAddress']                    = null;
        $burialFormViewData['customerJuristicPersonPostalAddress']                   = null;
        $burialFormViewData['customerJuristicPersonBankDetailsBankName']             = null;
        $burialFormViewData['customerJuristicPersonBankDetailsBik']                  = null;
        $burialFormViewData['customerJuristicPersonBankDetailsCorrespondentAccount'] = null;
        $burialFormViewData['customerJuristicPersonBankDetailsCurrentAccount']       = null;
        $burialFormViewData['customerJuristicPersonPhone']                           = null;
        $burialFormViewData['customerJuristicPersonPhoneAdditional']                 = null;
        $burialFormViewData['customerJuristicPersonFax']                             = null;
        $burialFormViewData['customerJuristicPersonGeneralDirector']                 = null;
        $burialFormViewData['customerJuristicPersonEmail']                           = null;
        $burialFormViewData['customerJuristicPersonWebsite']                         = null;
        $customerIdData = isset($burialFormViewData['customerIdJson'])
            ? \json_decode($burialFormViewData['customerIdJson'], true)
            : null;
        $burialFormViewData['customerId']   = $customerIdData['value'] ?? null;
        $burialFormViewData['customerType'] = $customerIdData['type']  ?? null;
        if ($burialFormViewData['customerId'] !== null && $burialFormViewData['customerType'] !== null) {
            $customerData = [];
            switch ($burialFormViewData['customerType']) {
                case NaturalPerson::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'np.full_name        AS customerNaturalPersonFullName',
                            'np.phone            AS customerNaturalPersonPhone',
                            'np.phone_additional AS customerNaturalPersonPhoneAdditional',
                            'np.email            AS customerNaturalPersonEmail',
                            'np.address          AS customerNaturalPersonAddress',
                            'np.born_at          AS customerNaturalPersonBornAt',
                            'np.place_of_birth   AS customerNaturalPersonPlaceOfBirth',
                            'np.passport         AS customerNaturalPersonPassportJson',
                        )
                        ->from('natural_person', 'np')
                        ->andWhere('np.id = :id')
                        ->setParameter('id', $burialFormViewData['customerId'])
                        ->executeQuery();
                    $customerData = $result->fetchAssociative();
                    $customerNaturalPersonPassportJson = $customerData['customerNaturalPersonPassportJson'] ?? null;
                    unset($customerData['customerNaturalPersonPassportJson']);
                    $this->fillPassportDataFromJson(
                        $customerNaturalPersonPassportJson,
                        $burialFormViewData,
                        'customerNaturalPerson'
                    );
                    break;
                case SoleProprietor::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'sp.name                    AS customerSoleProprietorName',
                            'sp.inn                     AS customerSoleProprietorInn',
                            'sp.ogrnip                  AS customerSoleProprietorOgrnip',
                            'sp.okpo                    AS customerSoleProprietorOkpo',
                            'sp.okved                   AS customerSoleProprietorOkved',
                            'sp.registration_address    AS customerSoleProprietorRegistrationAddress',
                            'sp.actual_location_address AS customerSoleProprietorActualLocationAddress',
                            'sp.bank_details            AS customerSoleProprietorBankDetailsJson',
                            'sp.phone                   AS customerSoleProprietorPhone',
                            'sp.phone_additional        AS customerSoleProprietorPhoneAdditional',
                            'sp.fax                     AS customerSoleProprietorFax',
                            'sp.email                   AS customerSoleProprietorEmail',
                            'sp.website                 AS customerSoleProprietorWebsite',
                        )
                        ->from('sole_proprietor', 'sp')
                        ->andWhere('sp.id = :id')
                        ->setParameter('id', $burialFormViewData['customerId'])
                        ->executeQuery();
                    $customerData = $result->fetchAssociative();
                    $customerSoleProprietorBankDetailsJson = $customerData['customerSoleProprietorBankDetailsJson'] ?? null;
                    unset($customerData['customerSoleProprietorBankDetailsJson']);
                    $this->fillBankDetailsDataFromJson(
                        $customerSoleProprietorBankDetailsJson,
                        $burialFormViewData,
                        'customerSoleProprietor'
                    );
                    break;
                case JuristicPerson::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'jp.name                    AS customerJuristicPersonName',
                            'jp.inn                     AS customerJuristicPersonInn',
                            'jp.kpp                     AS customerJuristicPersonKpp',
                            'jp.ogrn                    AS customerJuristicPersonOgrn',
                            'jp.okpo                    AS customerJuristicPersonOkpo',
                            'jp.okved                   AS customerJuristicPersonOkved',
                            'jp.legal_address           AS customerJuristicPersonLegalAddress',
                            'jp.postal_address          AS customerJuristicPersonPostalAddress',
                            'jp.bank_details            AS customerJuristicPersonBankDetailsJson',
                            'jp.phone                   AS customerJuristicPersonPhone',
                            'jp.phone_additional        AS customerJuristicPersonPhoneAdditional',
                            'jp.fax                     AS customerJuristicPersonFax',
                            'jp.general_director        AS customerJuristicPersonGeneralDirector',
                            'jp.email                   AS customerJuristicPersonEmail',
                            'jp.website                 AS customerJuristicPersonWebsite',
                        )
                        ->from('juristic_person', 'jp')
                        ->andWhere('jp.id = :id')
                        ->setParameter('id', $burialFormViewData['customerId'])
                        ->executeQuery();
                    $customerData = $result->fetchAssociative();
                    $customerJuristicPersonBankDetailsJson = $customerData['customerJuristicPersonBankDetailsJson'] ?? null;
                    unset($customerData['customerJuristicPersonBankDetailsJson']);
                    $this->fillBankDetailsDataFromJson(
                        $customerJuristicPersonBankDetailsJson,
                        $burialFormViewData,
                        'customerJuristicPerson'
                    );
                    break;
            }
            $burialFormViewData = \array_merge($burialFormViewData, $customerData);
        }
        unset($burialFormViewData['customerIdJson']);
    }

    /**
     * @param array $burialFormViewData
     */
    private function fillFuneralCompanyData(array &$burialFormViewData): void
    {
        $burialFormViewData['funeralCompanySoleProprietorName']                            = null;
        $burialFormViewData['funeralCompanySoleProprietorInn']                             = null;
        $burialFormViewData['funeralCompanySoleProprietorOgrnip']                          = null;
        $burialFormViewData['funeralCompanySoleProprietorOkpo']                            = null;
        $burialFormViewData['funeralCompanySoleProprietorOkved']                           = null;
        $burialFormViewData['funeralCompanySoleProprietorRegistrationAddress']             = null;
        $burialFormViewData['funeralCompanySoleProprietorActualLocationAddress']           = null;
        $burialFormViewData['funeralCompanySoleProprietorBankDetailsBankName']             = null;
        $burialFormViewData['funeralCompanySoleProprietorBankDetailsBik']                  = null;
        $burialFormViewData['funeralCompanySoleProprietorBankDetailsCorrespondentAccount'] = null;
        $burialFormViewData['funeralCompanySoleProprietorBankDetailsCurrentAccount']       = null;
        $burialFormViewData['funeralCompanySoleProprietorPhone']                           = null;
        $burialFormViewData['funeralCompanySoleProprietorPhoneAdditional']                 = null;
        $burialFormViewData['funeralCompanySoleProprietorFax']                             = null;
        $burialFormViewData['funeralCompanySoleProprietorEmail']                           = null;
        $burialFormViewData['funeralCompanySoleProprietorWebsite']                         = null;
        $burialFormViewData['funeralCompanyJuristicPersonName']                            = null;
        $burialFormViewData['funeralCompanyJuristicPersonInn']                             = null;
        $burialFormViewData['funeralCompanyJuristicPersonKpp']                             = null;
        $burialFormViewData['funeralCompanyJuristicPersonOgrn']                            = null;
        $burialFormViewData['funeralCompanyJuristicPersonOkpo']                            = null;
        $burialFormViewData['funeralCompanyJuristicPersonOkved']                           = null;
        $burialFormViewData['funeralCompanyJuristicPersonLegalAddress']                    = null;
        $burialFormViewData['funeralCompanyJuristicPersonPostalAddress']                   = null;
        $burialFormViewData['funeralCompanyJuristicPersonBankDetailsBankName']             = null;
        $burialFormViewData['funeralCompanyJuristicPersonBankDetailsBik']                  = null;
        $burialFormViewData['funeralCompanyJuristicPersonBankDetailsCorrespondentAccount'] = null;
        $burialFormViewData['funeralCompanyJuristicPersonBankDetailsCurrentAccount']       = null;
        $burialFormViewData['funeralCompanyJuristicPersonPhone']                           = null;
        $burialFormViewData['funeralCompanyJuristicPersonPhoneAdditional']                 = null;
        $burialFormViewData['funeralCompanyJuristicPersonFax']                             = null;
        $burialFormViewData['funeralCompanyJuristicPersonGeneralDirector']                 = null;
        $burialFormViewData['funeralCompanyJuristicPersonEmail']                           = null;
        $burialFormViewData['funeralCompanyJuristicPersonWebsite']                         = null;
        $funeralCompanyIdData = isset($burialFormViewData['funeralCompanyIdJson'])
            ? \json_decode($burialFormViewData['funeralCompanyIdJson'], true)
            : null;
        $burialFormViewData['funeralCompanyId']   = $funeralCompanyIdData['value'] ?? null;
        $burialFormViewData['funeralCompanyType'] = $funeralCompanyIdData['type']  ?? null;
        if ($burialFormViewData['funeralCompanyId'] !== null && $burialFormViewData['funeralCompanyType'] !== null) {
            $funeralCompanyData = [];
            switch ($burialFormViewData['funeralCompanyType']) {
                case SoleProprietor::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'sp.name                    AS funeralCompanySoleProprietorName',
                            'sp.inn                     AS funeralCompanySoleProprietorInn',
                            'sp.ogrnip                  AS funeralCompanySoleProprietorOgrnip',
                            'sp.okpo                    AS funeralCompanySoleProprietorOkpo',
                            'sp.okved                   AS funeralCompanySoleProprietorOkved',
                            'sp.registration_address    AS funeralCompanySoleProprietorRegistrationAddress',
                            'sp.actual_location_address AS funeralCompanySoleProprietorActualLocationAddress',
                            'sp.bank_details            AS funeralCompanySoleProprietorBankDetailsJson',
                            'sp.phone                   AS funeralCompanySoleProprietorPhone',
                            'sp.phone_additional        AS funeralCompanySoleProprietorPhoneAdditional',
                            'sp.fax                     AS funeralCompanySoleProprietorFax',
                            'sp.email                   AS funeralCompanySoleProprietorEmail',
                            'sp.website                 AS funeralCompanySoleProprietorWebsite',
                        )
                        ->from('sole_proprietor', 'sp')
                        ->andWhere('sp.id = :id')
                        ->setParameter('id', $burialFormViewData['funeralCompanyId'])
                        ->executeQuery();
                    $funeralCompanyData = $result->fetchAssociative();
                    $funeralCompanySoleProprietorBankDetailsJson = $funeralCompanyData['funeralCompanySoleProprietorBankDetailsJson'] ?? null;
                    unset($funeralCompanyData['funeralCompanySoleProprietorBankDetailsJson']);
                    $this->fillBankDetailsDataFromJson(
                        $funeralCompanySoleProprietorBankDetailsJson,
                        $burialFormViewData,
                        'funeralCompanySoleProprietor'
                    );
                    break;
                case JuristicPerson::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'jp.name             AS funeralCompanyJuristicPersonName',
                            'jp.inn              AS funeralCompanyJuristicPersonInn',
                            'jp.kpp              AS funeralCompanyJuristicPersonKpp',
                            'jp.ogrn             AS funeralCompanyJuristicPersonOgrn',
                            'jp.okpo             AS funeralCompanyJuristicPersonOkpo',
                            'jp.okved            AS funeralCompanyJuristicPersonOkved',
                            'jp.legal_address    AS funeralCompanyJuristicPersonLegalAddress',
                            'jp.postal_address   AS funeralCompanyJuristicPersonPostalAddress',
                            'jp.bank_details     AS funeralCompanyJuristicPersonBankDetailsJson',
                            'jp.phone            AS funeralCompanyJuristicPersonPhone',
                            'jp.phone_additional AS funeralCompanyJuristicPersonPhoneAdditional',
                            'jp.fax              AS funeralCompanyJuristicPersonFax',
                            'jp.general_director AS funeralCompanyJuristicPersonGeneralDirector',
                            'jp.email            AS funeralCompanyJuristicPersonEmail',
                            'jp.website          AS funeralCompanyJuristicPersonWebsite',
                        )
                        ->from('juristic_person', 'jp')
                        ->andWhere('jp.id = :id')
                        ->setParameter('id', $burialFormViewData['funeralCompanyId'])
                        ->executeQuery();
                    $funeralCompanyData = $result->fetchAssociative();
                    $funeralCompanyJuristicPersonBankDetailsJson = $funeralCompanyData['funeralCompanyJuristicPersonBankDetailsJson'] ?? null;
                    unset($funeralCompanyData['funeralCompanyJuristicPersonBankDetailsJson']);
                    $this->fillBankDetailsDataFromJson(
                        $funeralCompanyJuristicPersonBankDetailsJson,
                        $burialFormViewData,
                        'funeralCompanyJuristicPerson'
                    );
                    break;
            }
            $burialFormViewData = \array_merge($burialFormViewData, $funeralCompanyData);
        }
        unset($burialFormViewData['funeralCompanyIdJson']);
    }

    /**
     * @param array $burialFormViewData
     */
    private function fillBurialPlaceData(array &$burialFormViewData): void
    {
        $burialFormViewData['burialPlaceGraveSiteCemeteryBlockId']         = null;
        $burialFormViewData['burialPlaceGraveSiteCemeteryBlockName']       = null;
        $burialFormViewData['burialPlaceGraveSiteRowInBlock']              = null;
        $burialFormViewData['burialPlaceGraveSitePositionInRow']           = null;
        $burialFormViewData['burialPlaceGraveSiteSize']                    = null;
        $burialFormViewData['burialPlaceColumbariumNicheColumbariumId']    = null;
        $burialFormViewData['burialPlaceColumbariumNicheColumbariumName']  = null;
        $burialFormViewData['burialPlaceColumbariumNicheRowInColumbarium'] = null;
        $burialFormViewData['burialPlaceColumbariumNicheNicheNumber']      = null;
        $burialFormViewData['burialPlaceMemorialTreeNumber']               = null;
        $burialFormViewData['burialPlaceGeoPositionLatitude']              = null;
        $burialFormViewData['burialPlaceGeoPositionLongitude']             = null;
        $burialFormViewData['burialPlaceGeoPositionError']                 = null;
        $burialPlaceIdData = isset($burialFormViewData['burialPlaceIdJson'])
            ? \json_decode($burialFormViewData['burialPlaceIdJson'], true)
            : null;
        $burialFormViewData['burialPlaceId']   = $burialPlaceIdData['value'] ?? null;
        $burialFormViewData['burialPlaceType'] = $burialPlaceIdData['type']  ?? null;
        if ($burialFormViewData['burialPlaceId'] !== null && $burialFormViewData['burialPlaceType'] !== null) {
            $burialPlaceData = [];
            switch ($burialFormViewData['burialPlaceType']) {
                case GraveSite::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'cb.id              AS burialPlaceGraveSiteCemeteryBlockId',
                            'cb.name            AS burialPlaceGraveSiteCemeteryBlockName',
                            'gs.row_in_block    AS burialPlaceGraveSiteRowInBlock',
                            'gs.position_in_row AS burialPlaceGraveSitePositionInRow',
                            'gs.size            AS burialPlaceGraveSiteSize',
                            'gs.geo_position    AS burialPlaceGraveSiteGeoPositionJson',
                        )
                        ->from('grave_site', 'gs')
                        ->leftJoin('gs', 'cemetery_block', 'cb', 'gs.cemetery_block_id = cb.id')
                        ->andWhere('gs.id = :id')
                        ->setParameter('id', $burialFormViewData['burialPlaceId'])
                        ->executeQuery();
                    $burialPlaceData                     = $result->fetchAssociative();
                    $burialPlaceGraveSiteGeoPositionJson = $burialPlaceData['burialPlaceGraveSiteGeoPositionJson'] ?? null;
                    unset($burialPlaceData['burialPlaceGraveSiteGeoPositionJson']);
                    $this->fillGeoPositionDataFromJson(
                        $burialPlaceGraveSiteGeoPositionJson,
                        $burialFormViewData,
                        'burialPlace'
                    );
                    break;
                case ColumbariumNiche::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'cl.id                       AS burialPlaceColumbariumNicheColumbariumId',
                            'cl.name                     AS burialPlaceColumbariumNicheColumbariumName',
                            'cn.row_in_columbarium       AS burialPlaceColumbariumNicheRowInColumbarium',
                            'cn.columbarium_niche_number AS burialPlaceColumbariumNicheNicheNumber',
                            'cn.geo_position             AS burialPlaceColumbariumNicheGeoPositionJson',
                        )
                        ->from('columbarium_niche', 'cn')
                        ->leftJoin('cn', 'columbarium', 'cl', 'cn.columbarium_id = cl.id')
                        ->andWhere('cn.id = :id')
                        ->setParameter('id', $burialFormViewData['burialPlaceId'])
                        ->executeQuery();
                    $burialPlaceData                            = $result->fetchAssociative();
                    $burialPlaceColumbariumNicheGeoPositionJson = $burialPlaceData['burialPlaceColumbariumNicheGeoPositionJson'] ?? null;
                    unset($burialPlaceData['burialPlaceColumbariumNicheGeoPositionJson']);
                    $this->fillGeoPositionDataFromJson(
                        $burialPlaceColumbariumNicheGeoPositionJson,
                        $burialFormViewData,
                        'burialPlace'
                    );
                    break;
                case MemorialTree::CLASS_SHORTCUT:
                    $result = $this->connection->createQueryBuilder()
                        ->select(
                            'mt.tree_number  AS burialPlaceMemorialTreeNumber',
                            'mt.geo_position AS burialPlaceMemorialTreeGeoPositionJson',
                        )
                        ->from('memorial_tree', 'mt')
                        ->andWhere('mt.id = :id')
                        ->setParameter('id', $burialFormViewData['burialPlaceId'])
                        ->executeQuery();
                    $burialPlaceData                        = $result->fetchAssociative();
                    $burialPlaceMemorialTreeGeoPositionJson = $burialPlaceData['burialPlaceMemorialTreeGeoPositionJson'] ?? null;
                    unset($burialPlaceData['burialPlaceMemorialTreeGeoPositionJson']);
                    $this->fillGeoPositionDataFromJson(
                        $burialPlaceMemorialTreeGeoPositionJson,
                        $burialFormViewData,
                        'burialPlace'
                    );
                    break;
            }
            $burialFormViewData = \array_merge($burialFormViewData, $burialPlaceData);
        }
        unset($burialFormViewData['burialPlaceIdJson']);
    }

    /**
     * @param string|null $passportJson
     * @param array       $burialFormViewData
     * @param string      $keyPrefix
     */
    private function fillPassportDataFromJson(
        ?string $passportJson,
        array   &$burialFormViewData,
        string  $keyPrefix,
    ): void {
        if ($passportJson === null) {
            return;
        }
        $passportData = \json_decode($passportJson, true);
        $burialFormViewData[$keyPrefix . 'PassportSeries']       = $passportData['series']       ?? null;
        $burialFormViewData[$keyPrefix . 'PassportNumber']       = $passportData['number']       ?? null;
        $burialFormViewData[$keyPrefix . 'PassportIssuedAt']     = $passportData['issuedAt']     ?? null;
        $burialFormViewData[$keyPrefix . 'PassportIssuedBy']     = $passportData['issuedBy']     ?? null;
        $burialFormViewData[$keyPrefix . 'PassportDivisionCode'] = $passportData['divisionCode'] ?? null;
    }

    /**
     * @param string|null $bankDetailsJson
     * @param array       $burialFormViewData
     * @param string      $keyPrefix
     */
    private function fillBankDetailsDataFromJson(
        ?string $bankDetailsJson,
        array   &$burialFormViewData,
        string  $keyPrefix,
    ): void {
        if ($bankDetailsJson === null) {
            return;
        }
        $bankDetailsData = \json_decode($bankDetailsJson, true);
        $burialFormViewData[$keyPrefix . 'BankDetailsBankName']             = $bankDetailsData['bankName']             ?? null;
        $burialFormViewData[$keyPrefix . 'BankDetailsBik']                  = $bankDetailsData['bik']                  ?? null;
        $burialFormViewData[$keyPrefix . 'BankDetailsCorrespondentAccount'] = $bankDetailsData['correspondentAccount'] ?? null;
        $burialFormViewData[$keyPrefix . 'BankDetailsCurrentAccount']       = $bankDetailsData['currentAccount']       ?? null;
    }

    /**
     * @param string|null $geoPositionJson
     * @param array       $burialFormViewData
     * @param string      $keyPrefix
     */
    private function fillGeoPositionDataFromJson(
        ?string $geoPositionJson,
        array   &$burialFormViewData,
        string  $keyPrefix,
    ): void {
        if ($geoPositionJson === null) {
            return;
        }
        $geoPositionData = \json_decode($geoPositionJson, true);
        $burialFormViewData[$keyPrefix . 'GeoPositionLatitude'] = isset($geoPositionData['coordinates']['latitude'])
            ? (string) $geoPositionData['coordinates']['latitude']
            : null;
        $burialFormViewData[$keyPrefix . 'GeoPositionLongitude'] = isset($geoPositionData['coordinates']['longitude'])
            ? (string) $geoPositionData['coordinates']['longitude']
            : null;
        $burialFormViewData[$keyPrefix . 'GeoPositionError'] = isset($geoPositionData['error'])
            ? (string) $geoPositionData['error']
            : null;
    }

    /**
     * @param array $burialFormViewData
     *
     * @return BurialFormView
     */
    private function hydrateBurialView(array $burialFormViewData): BurialFormView
    {
        return new BurialFormView(
            $burialFormViewData['id'],
            $burialFormViewData['code'],
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
            $burialFormViewData['customerNaturalPersonPassportDivisionCode'],
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
            $burialFormViewData['burialPlaceOwnerPassportDivisionCode'],
            $burialFormViewData['funeralCompanyId'],
            $burialFormViewData['funeralCompanyType'],
            $burialFormViewData['funeralCompanySoleProprietorName'],
            $burialFormViewData['funeralCompanySoleProprietorInn'],
            $burialFormViewData['funeralCompanySoleProprietorOgrnip'],
            $burialFormViewData['funeralCompanySoleProprietorOkpo'],
            $burialFormViewData['funeralCompanySoleProprietorOkved'],
            $burialFormViewData['funeralCompanySoleProprietorRegistrationAddress'],
            $burialFormViewData['funeralCompanySoleProprietorActualLocationAddress'],
            $burialFormViewData['funeralCompanySoleProprietorBankDetailsBankName'],
            $burialFormViewData['funeralCompanySoleProprietorBankDetailsBik'],
            $burialFormViewData['funeralCompanySoleProprietorBankDetailsCorrespondentAccount'],
            $burialFormViewData['funeralCompanySoleProprietorBankDetailsCurrentAccount'],
            $burialFormViewData['funeralCompanySoleProprietorPhone'],
            $burialFormViewData['funeralCompanySoleProprietorPhoneAdditional'],
            $burialFormViewData['funeralCompanySoleProprietorFax'],
            $burialFormViewData['funeralCompanySoleProprietorEmail'],
            $burialFormViewData['funeralCompanySoleProprietorWebsite'],
            $burialFormViewData['funeralCompanyJuristicPersonName'],
            $burialFormViewData['funeralCompanyJuristicPersonInn'],
            $burialFormViewData['funeralCompanyJuristicPersonKpp'],
            $burialFormViewData['funeralCompanyJuristicPersonOgrn'],
            $burialFormViewData['funeralCompanyJuristicPersonOkpo'],
            $burialFormViewData['funeralCompanyJuristicPersonOkved'],
            $burialFormViewData['funeralCompanyJuristicPersonLegalAddress'],
            $burialFormViewData['funeralCompanyJuristicPersonPostalAddress'],
            $burialFormViewData['funeralCompanyJuristicPersonBankDetailsBankName'],
            $burialFormViewData['funeralCompanyJuristicPersonBankDetailsBik'],
            $burialFormViewData['funeralCompanyJuristicPersonBankDetailsCorrespondentAccount'],
            $burialFormViewData['funeralCompanyJuristicPersonBankDetailsCurrentAccount'],
            $burialFormViewData['funeralCompanyJuristicPersonPhone'],
            $burialFormViewData['funeralCompanyJuristicPersonPhoneAdditional'],
            $burialFormViewData['funeralCompanyJuristicPersonFax'],
            $burialFormViewData['funeralCompanyJuristicPersonGeneralDirector'],
            $burialFormViewData['funeralCompanyJuristicPersonEmail'],
            $burialFormViewData['funeralCompanyJuristicPersonWebsite'],
            $burialFormViewData['burialChainId'],
            $burialFormViewData['burialPlaceId'],
            $burialFormViewData['burialPlaceType'],
            $burialFormViewData['burialPlaceGraveSiteCemeteryBlockId'],
            $burialFormViewData['burialPlaceGraveSiteCemeteryBlockName'],
            $burialFormViewData['burialPlaceGraveSiteRowInBlock'],
            $burialFormViewData['burialPlaceGraveSitePositionInRow'],
            $burialFormViewData['burialPlaceGraveSiteSize'],
            $burialFormViewData['burialPlaceColumbariumNicheColumbariumId'],
            $burialFormViewData['burialPlaceColumbariumNicheColumbariumName'],
            $burialFormViewData['burialPlaceColumbariumNicheRowInColumbarium'],
            $burialFormViewData['burialPlaceColumbariumNicheNicheNumber'],
            $burialFormViewData['burialPlaceMemorialTreeNumber'],
            $burialFormViewData['burialPlaceGeoPositionLatitude'],
            $burialFormViewData['burialPlaceGeoPositionLongitude'],
            $burialFormViewData['burialPlaceGeoPositionError'],
            $burialFormViewData['burialContainerType'],
            $burialFormViewData['burialContainerCoffinSize'],
            $burialFormViewData['burialContainerCoffinShape'],
            $burialFormViewData['burialContainerCoffinIsNonStandard'],
            $burialFormViewData['buriedAt'],
            $burialFormViewData['updatedAt'],
        );
    }
}
