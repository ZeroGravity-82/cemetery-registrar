const $naturalPersonFormNew                                  = $(`#modalNaturalPersonFormNew`);
const $naturalPersonFormNewTitle                             = $naturalPersonFormNew.find(`.modal-title`)
const $naturalPersonFormNewForm                              = $naturalPersonFormNew.find(`form`);
const $naturalPersonFormNewFullNameField                     = $naturalPersonFormNew.find(`input[id=fullName]`);
const $naturalPersonFormNewPhoneField                        = $naturalPersonFormNew.find(`input[id=phone]`);
const $naturalPersonFormNewPhoneAdditionalField              = $naturalPersonFormNew.find(`input[id=phoneAdditional]`);
const $naturalPersonFormNewAddressField                      = $naturalPersonFormNew.find(`input[id=address]`);
const $naturalPersonFormNewEmailField                        = $naturalPersonFormNew.find(`input[id=email]`);
const $naturalPersonFormNewBornAtField                       = $naturalPersonFormNew.find(`input[id=bornAt]`);
const $naturalPersonFormNewPlaceOfBirthField                 = $naturalPersonFormNew.find(`input[id=placeOfBirth]`);
const $naturalPersonFormNewPassportSeriesField               = $naturalPersonFormNew.find(`input[id=passportSeries]`);
const $naturalPersonFormNewPassportNumberField               = $naturalPersonFormNew.find(`input[id=passportNumber]`);
const $naturalPersonFormNewPassportIssuedAtField             = $naturalPersonFormNew.find(`input[id=passportIssuedAt]`);
const $naturalPersonFormNewPassportIssuedByField             = $naturalPersonFormNew.find(`input[id=passportIssuedBy]`);
const $naturalPersonFormNewPassportDivisionCodeField         = $naturalPersonFormNew.find(`input[id=passportDivisionCode]`);
const $naturalPersonFormNewDiedAtField                       = $naturalPersonFormNew.find(`input[id=diedAt]`);
const $naturalPersonFormNewAgeField                          = $naturalPersonFormNew.find(`input[id=age]`);
const $naturalPersonFormNewCauseOfDeathIdField               = $naturalPersonFormNew.find(`select[id=causeOfDeathId]`);
const $naturalPersonFormNewDeathCertificateSeriesField       = $naturalPersonFormNew.find(`input[id=deathCertificateSeries]`);
const $naturalPersonFormNewDeathCertificateNumberField       = $naturalPersonFormNew.find(`input[id=deathCertificateNumber]`);
const $naturalPersonFormNewDeathCertificateIssuedAtField     = $naturalPersonFormNew.find(`input[id=deathCertificateIssuedAt]`);
const $naturalPersonFormNewCremationCertificateNumberField   = $naturalPersonFormNew.find(`input[id=cremationCertificateNumber]`);
const $naturalPersonFormNewCremationCertificateIssuedAtField = $naturalPersonFormNew.find(`input[id=cremationCertificateIssuedAt]`);
const $naturalPersonFormNewCsrfTokenField                    = $naturalPersonFormNew.find(`input[id=token]`);
const $naturalPersonFormNewSaveAndCloseBtn                   = $naturalPersonFormNew.find(`.js-save-and-close`);
const $naturalPersonFormNewCloseBtn                          = $naturalPersonFormNew.find(`.js-close`);
const naturalPersonFormNewModalObject                        = new bootstrap.Modal(`#modalNaturalPersonFormNew`, {});

function naturalPersonFormNew_show() {
  $naturalPersonFormNewTitle.html(`Создание физлица`);
  $naturalPersonFormNewFullNameField.val(null);
  $naturalPersonFormNewPhoneField.val(null);
  $naturalPersonFormNewPhoneAdditionalField.val(null);
  $naturalPersonFormNewAddressField.val(null);
  $naturalPersonFormNewEmailField.val(null);
  $naturalPersonFormNewBornAtField.val(null);
  $naturalPersonFormNewPlaceOfBirthField.val(null);
  $naturalPersonFormNewPassportSeriesField.val(null);
  $naturalPersonFormNewPassportNumberField.val(null);
  $naturalPersonFormNewPassportIssuedAtField.val(null);
  $naturalPersonFormNewPassportIssuedByField.val(null);
  $naturalPersonFormNewPassportDivisionCodeField.val(null);
  $naturalPersonFormNewDiedAtField.val(null);
  $naturalPersonFormNewAgeField.val(null);
  $naturalPersonFormNewCauseOfDeathIdField.val(null).change();
  $naturalPersonFormNewDeathCertificateSeriesField.val(null);
  $naturalPersonFormNewDeathCertificateNumberField.val(null);
  $naturalPersonFormNewDeathCertificateIssuedAtField.val(null);
  $naturalPersonFormNewCremationCertificateNumberField.val(null);
  $naturalPersonFormNewCremationCertificateIssuedAtField.val(null);
  naturalPersonFormNew_hideAllValidationErrors();
  naturalPersonFormNewModalObject.show();
}

// Autofocus
$naturalPersonFormNew.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#fullName`).focus();
});

$naturalPersonFormNewSaveAndCloseBtn.on(`click`, () => {
  const url = $naturalPersonFormNewForm.data(`action-new`);
  naturalPersonFormNew_save(url, true);
});
$naturalPersonFormNewCloseBtn.on(`click`, () => {
  naturalPersonForm_close();
});

function naturalPersonFormNew_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    fullName: $naturalPersonFormNewFullNameField.val() !== ``
      ? $naturalPersonFormNewFullNameField.val()
      : null,
    phone: $naturalPersonFormNewPhoneField.val() !== ``
      ? $naturalPersonFormNewPhoneField.val()
      : null,
    phoneAdditional: $naturalPersonFormNewPhoneAdditionalField.val() !== ``
      ? $naturalPersonFormNewPhoneAdditionalField.val()
      : null,
    address: $naturalPersonFormNewAddressField.val() !== ``
      ? $naturalPersonFormNewAddressField.val()
      : null,
    email: $naturalPersonFormNewEmailField.val() !== ``
        ? $naturalPersonFormNewEmailField.val()
        : null,
    bornAt: $naturalPersonFormNewBornAtField.val() !== ``
      ? $naturalPersonFormNewBornAtField.val()
      : null,
    placeOfBirth: $naturalPersonFormNewPlaceOfBirthField.val() !== ``
      ? $naturalPersonFormNewPlaceOfBirthField.val()
      : null,
    passportSeries: $naturalPersonFormNewPassportSeriesField.val() !== ``
      ? $naturalPersonFormNewPassportSeriesField.val()
      : null,
    passportNumber: $naturalPersonFormNewPassportNumberField.val() !== ``
      ? $naturalPersonFormNewPassportNumberField.val()
      : null,
    passportIssuedAt: $naturalPersonFormNewPassportIssuedAtField.val() !== ``
      ? $naturalPersonFormNewPassportIssuedAtField.val()
      : null,
    passportIssuedBy: $naturalPersonFormNewPassportIssuedByField.val() !== ``
      ? $naturalPersonFormNewPassportIssuedByField.val()
      : null,
    passportDivisionCode: $naturalPersonFormNewPassportDivisionCodeField.val() !== ``
      ? $naturalPersonFormNewPassportDivisionCodeField.val()
      : null,
    diedAt: $naturalPersonFormNewDiedAtField.val() !== ``
      ? $naturalPersonFormNewDiedAtField.val()
      : null,
    age: $naturalPersonFormNewAgeField.val() !== ``
      ? parseInt($naturalPersonFormNewAgeField.val())
      : null,
    causeOfDeathId: $naturalPersonFormNewCauseOfDeathIdField.val() !== ``
      ? $naturalPersonFormNewCauseOfDeathIdField.val()
      : null,
    deathCertificateSeries: $naturalPersonFormNewDeathCertificateSeriesField.val() !== ``
      ? $naturalPersonFormNewDeathCertificateSeriesField.val()
      : null,
    deathCertificateNumber: $naturalPersonFormNewDeathCertificateNumberField.val() !== ``
      ? $naturalPersonFormNewDeathCertificateNumberField.val()
      : null,
    deathCertificateIssuedAt: $naturalPersonFormNewDeathCertificateIssuedAtField.val() !== ``
      ? $naturalPersonFormNewDeathCertificateIssuedAtField.val()
      : null,
    cremationCertificateNumber: $naturalPersonFormNewCremationCertificateNumberField.val() !== ``
      ? $naturalPersonFormNewCremationCertificateNumberField.val()
      : null,
    cremationCertificateIssuedAt: $naturalPersonFormNewCremationCertificateIssuedAtField.val() !== ``
      ? $naturalPersonFormNewCremationCertificateIssuedAtField.val()
      : null,
    token: $naturalPersonFormNewCsrfTokenField.val(),
  };
  $.ajax({
    dataType: `json`,
    method: `POST`,
    url: url,
    data: JSON.stringify(data),
    contentType: `application/json; charset=utf-8`,
  })
      .done(() => {
        buildToast().fire({
          icon: `success`,
          title: `Физлицо успешно создано.`,
        });
        if (isReloadRequired) {
          naturalPersonFormNewModalObject.hide();
          location.reload();      // TODO refactor not to reload entire page
        }
      })
      .fail(onAjaxFailure)
      .always(onAjaxAlways);
}

function naturalPersonForm_close() {
  naturalPersonFormNewModalObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function naturalPersonFormNew_hideAllValidationErrors() {
  $naturalPersonFormNewForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$naturalPersonFormNewForm.on(`change`, `.is-invalid`, (e) => {
  naturalPersonFormNew_removeValidationError(e);
});
$naturalPersonFormNewForm.on(`input`, `.is-invalid`, (e) => {
  naturalPersonFormNew_removeValidationError(e);
});
function naturalPersonFormNew_removeValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
