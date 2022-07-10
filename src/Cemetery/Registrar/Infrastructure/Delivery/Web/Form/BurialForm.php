<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('deceasedNaturalPersonId')
            ->add('deceasedNaturalPersonFullName')
            ->add('deceasedNaturalPersonBornAt')
            ->add('deceasedDiedAt')
            ->add('deceasedAge')
            ->add('deceasedDeathCertificateId')
            ->add('deceasedCauseOfDeathId')
            ->add('customerId')
            ->add('customerType')
            ->add('customerNaturalPersonFullName')
            ->add('customerNaturalPersonPhone')
            ->add('customerNaturalPersonPhoneAdditional')
            ->add('customerNaturalPersonEmail')
            ->add('customerNaturalPersonAddress')
            ->add('customerNaturalPersonBornAt')
            ->add('customerNaturalPersonPlaceOfBirth')
            ->add('customerNaturalPersonPassportSeries')
            ->add('customerNaturalPersonPassportNumber')
            ->add('customerNaturalPersonPassportIssuedAt')
            ->add('customerNaturalPersonPassportIssuedBy')
            ->add('customerNaturalPersonPassportDivisionCode')
            ->add('customerSoleProprietorName')
            ->add('customerSoleProprietorInn')
            ->add('customerSoleProprietorOgrnip')
            ->add('customerSoleProprietorOkpo')
            ->add('customerSoleProprietorOkved')
            ->add('customerSoleProprietorRegistrationAddress')
            ->add('customerSoleProprietorActualLocationAddress')
            ->add('customerSoleProprietorBankDetailsBankName')
            ->add('customerSoleProprietorBankDetailsBik')
            ->add('customerSoleProprietorBankDetailsCorrespondentAccount')
            ->add('customerSoleProprietorBankDetailsCurrentAccount')
            ->add('customerSoleProprietorPhone')
            ->add('customerSoleProprietorPhoneAdditional')
            ->add('customerSoleProprietorFax')
            ->add('customerSoleProprietorEmail')
            ->add('customerSoleProprietorWebsite')
            ->add('customerJuristicPersonName')
            ->add('customerJuristicPersonInn')
            ->add('customerJuristicPersonKpp')
            ->add('customerJuristicPersonOgrn')
            ->add('customerJuristicPersonOkpo')
            ->add('customerJuristicPersonOkved')
            ->add('customerJuristicPersonLegalAddress')
            ->add('customerJuristicPersonPostalAddress')
            ->add('customerJuristicPersonBankDetailsBankName')
            ->add('customerJuristicPersonBankDetailsBik')
            ->add('customerJuristicPersonBankDetailsCorrespondentAccount')
            ->add('customerJuristicPersonBankDetailsCurrentAccount')
            ->add('customerJuristicPersonPhone')
            ->add('customerJuristicPersonPhoneAdditional')
            ->add('customerJuristicPersonFax')
            ->add('customerJuristicPersonGeneralDirector')
            ->add('customerJuristicPersonEmail')
            ->add('customerJuristicPersonWebsite')
            ->add('personInChargeId')
            ->add('personInChargeFullName')
            ->add('personInChargePhone')
            ->add('personInChargePhoneAdditional')
            ->add('personInChargeEmail')
            ->add('personInChargeAddress')
            ->add('personInChargeBornAt')
            ->add('personInChargePlaceOfBirth')
            ->add('personInChargePassportSeries')
            ->add('personInChargePassportNumber')
            ->add('personInChargePassportIssuedAt')
            ->add('personInChargePassportIssuedBy')
            ->add('personInChargePassportDivisionCode')
            ->add('funeralCompanyId')
            ->add('burialChainId')
            ->add('burialPlaceId')
            ->add('burialPlaceType')
            ->add('burialPlaceGraveSiteCemeteryBlockId')
            ->add('burialPlaceGraveSiteRowInBlock')
            ->add('burialPlaceGraveSitePositionInRow')
            ->add('burialPlaceGraveSiteSize')
            ->add('burialPlaceColumbariumNicheColumbariumId')
            ->add('burialPlaceColumbariumNicheRowInColumbarium')
            ->add('burialPlaceColumbariumNicheNicheNumber')
            ->add('burialPlaceMemorialTreeNumber')
            ->add('burialPlaceGeoPositionLatitude')
            ->add('burialPlaceGeoPositionLongitude')
            ->add('burialPlaceGeoPositionError',                           TextType::class)
            ->add('burialContainerType',                                   TextType::class)
            ->add('burialContainerCoffinSize',                             IntegerType::class)
            ->add('burialContainerCoffinShape',                            TextType::class)
            ->add('burialContainerCoffinIsNonStandard')
            ->add('buriedAt',                                              DateTimeType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
