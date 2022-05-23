$('body').on('click', 'tr', function(e) {
    const $tr = $(e.target).closest('tr');
    const id = $tr.attr('data-id');
    const editBurialModal = new bootstrap.Modal('#editBurialModal', {});
    $.ajax({
        type: "GET",
        url: '/burial/edit-get/' + id,
        success: function (burialFormView) {
            const $editBurialModal = $('#editBurialModal');

            // Информация об умершем
            $editBurialModal.find('span#deceasedNameStub').text(burialFormView.deceasedNaturalPersonFullName);
            $editBurialModal.find('input#code').val(burialFormView.code);
            $editBurialModal.find('input#deceasedNaturalPersonFullName').val(burialFormView.deceasedNaturalPersonFullName);
            $editBurialModal.find('input#deceasedNaturalPersonBornAt').val(burialFormView.deceasedNaturalPersonBornAt);
            $editBurialModal.find('input#deceasedDiedAt').val(burialFormView.deceasedDiedAt);
            $editBurialModal.find('input#deceasedAge').val(burialFormView.deceasedAge);
            $editBurialModal.find('input#deceasedDeathCertificateId').val(burialFormView.deceasedDeathCertificateId);
            if (burialFormView.deceasedCauseOfDeath) {
                $editBurialModal.find("select#deceasedCauseOfDeath option[value='" + burialFormView.deceasedCauseOfDeath + "']").prop("selected", true)
            } else {
                $editBurialModal.find("select#deceasedCauseOfDeath").prop('selectedIndex',0);
            }

            // Заказчик
            $editBurialModal.find('input#customerNaturalPersonFullName').val(burialFormView.customerNaturalPersonFullName);
            $editBurialModal.find('input#customerNaturalPersonPhone').val(burialFormView.customerNaturalPersonPhone);
            $editBurialModal.find('input#customerNaturalPersonPhoneAdditional').val(burialFormView.customerNaturalPersonPhoneAdditional);
            $editBurialModal.find('input#customerNaturalPersonAddress').val(burialFormView.customerNaturalPersonAddress);
            $editBurialModal.find('input#customerNaturalPersonPassportSeries').val(burialFormView.customerNaturalPersonPassportSeries);
            $editBurialModal.find('input#customerNaturalPersonPassportNumber').val(burialFormView.customerNaturalPersonPassportNumber);
            $editBurialModal.find('input#customerNaturalPersonPassportIssuedAt').val(burialFormView.customerNaturalPersonPassportIssuedAt);
            $editBurialModal.find('input#customerNaturalPersonPassportIssuedBy').val(burialFormView.customerNaturalPersonPassportIssuedBy);
            $editBurialModal.find('input#customerNaturalPersonPassportDivisionCode').val(burialFormView.customerNaturalPersonPassportDivisionCode);

            // Захоронение
            if (burialFormView.funeralCompanyId) {
                $editBurialModal.find("select#funeralCompanyJuristicPersonName option[value='" + burialFormView.funeralCompanyId + "']").prop("selected", true)
            } else {
                $editBurialModal.find("select#funeralCompanyJuristicPersonName").prop('selectedIndex',0);
            }
            if (burialFormView.buriedAt) {
                let buriedAtArray = burialFormView.buriedAt.split(' ');
                $editBurialModal.find('input#buriedAt').val(buriedAtArray[0] + 'T' + buriedAtArray[1]);
            }
            if (burialFormView.burialPlaceGraveSiteCemeteryBlockId) {
                $editBurialModal.find("select#burialPlaceGraveSiteCemeteryBlockId option[value='" + burialFormView.burialPlaceGraveSiteCemeteryBlockId + "']").prop("selected", true)
            } else {
                $editBurialModal.find("select#burialPlaceGraveSiteCemeteryBlockId").prop('selectedIndex',0);
            }
            $editBurialModal.find('input#burialPlaceGraveSiteRowInBlock').val(burialFormView.burialPlaceGraveSiteRowInBlock);
            if (burialFormView.burialPlaceGraveSiteGeoPositionLatitude && burialFormView.burialPlaceGraveSiteGeoPositionLongitude) {
                $editBurialModal.find('input#burialPlaceGeoPosition').val(burialFormView.burialPlaceGraveSiteGeoPositionLatitude + ', ' + burialFormView.burialPlaceGraveSiteGeoPositionLongitude);
            }
            if (burialFormView.burialContainerCoffinShape) {
                $editBurialModal.find("select#burialContainerCoffinShape option[value='" + burialFormView.burialContainerCoffinShape + "']").prop("selected", true)
            } else {
                $editBurialModal.find("select#burialContainerCoffinShape").prop('selectedIndex',0);
            }
            $editBurialModal.find('input#burialContainerCoffinSize').val(burialFormView.burialContainerCoffinSize);
            if (burialFormView.burialContainerCoffinIsNonStandard) {
                $editBurialModal.find('input#burialContainerCoffinIsNonStandard').prop('checked', true);
            } else {
                $editBurialModal.find('input#burialContainerCoffinIsNonStandard').prop('checked', false);
            }
            editBurialModal.show();
        }
    });
});
