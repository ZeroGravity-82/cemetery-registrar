const $naturalPersonFormClarifyDeceasedDetails                                  = $(`#modalNaturalPersonFormClarifyDeceasedDetails`);
const $naturalPersonFormClarifyDeceasedDetailsTitle                             = $naturalPersonFormClarifyDeceasedDetails.find(`.modal-title`)
const $naturalPersonFormClarifyDeceasedDetailsForm                              = $naturalPersonFormClarifyDeceasedDetails.find(`form`);
const $naturalPersonFormClarifyDeceasedDetailsDiedAtField                       = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=diedAt]`);
const $naturalPersonFormClarifyDeceasedDetailsAgeField                          = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=age]`);
const $naturalPersonFormClarifyDeceasedDetailsCauseOfDeathIdField               = $naturalPersonFormClarifyDeceasedDetails.find(`select[id=causeOfDeathId]`);
const $naturalPersonFormClarifyDeceasedDetailsDeathCertificateSeriesField       = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=deathCertificateSeries]`);
const $naturalPersonFormClarifyDeceasedDetailsDeathCertificateNumberField       = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=deathCertificateNumber]`);
const $naturalPersonFormClarifyDeceasedDetailsDeathCertificateIssuedAtField     = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=deathCertificateIssuedAt]`);
const $naturalPersonFormClarifyDeceasedDetailsCremationCertificateNumberField   = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=cremationCertificateNumber]`);
const $naturalPersonFormClarifyDeceasedDetailsCremationCertificateIssuedAtField = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=cremationCertificateIssuedAt]`);
const $naturalPersonFormClarifyDeceasedDetailsBornAtField                       = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=bornAt]`);
const $naturalPersonFormClarifyDeceasedDetailsCsrfTokenField                    = $naturalPersonFormClarifyDeceasedDetails.find(`input[id=token]`);
const $naturalPersonFormClarifyDeceasedDetailsSaveAndCloseBtn                   = $naturalPersonFormClarifyDeceasedDetails.find(`.js-save-and-close`);
const $naturalPersonFormClarifyDeceasedDetailsCloseBtn                          = $naturalPersonFormClarifyDeceasedDetails.find(`.js-close`);
const naturalPersonFormClarifyDeceasedDetailsModalObject                        = new bootstrap.Modal(`#modalNaturalPersonFormClarifyDeceasedDetails`, {});

let persistedCallbackClarifyDeceasedDetails;
let persistedArgsClarifyDeceasedDetails;
let isFirstEnteringDeceasedDataMode;

function naturalPersonFormClarifyDeceasedDetails_show(view, callback, args) {
  persistedCallbackClarifyDeceasedDetails = callback;
  persistedArgsClarifyDeceasedDetails     = args;
  currentNaturalPersonId                  = view.id;
  isFirstEnteringDeceasedDataMode         = view.diedAt === null;
  $naturalPersonFormClarifyDeceasedDetailsTitle.html(
      `${isFirstEnteringDeceasedDataMode ? `Внесение` : `Уточнение`} данных о смерти - <span>${view.fullName}</span>`
  );
  $naturalPersonFormClarifyDeceasedDetailsDiedAtField.val(
    view.diedAt !== null
      ? view.diedAt.split(`.`).reverse().join(`-`)
      : null
  );
  $naturalPersonFormClarifyDeceasedDetailsAgeField.val(view.age);
  $naturalPersonFormClarifyDeceasedDetailsCauseOfDeathIdField.val(view.causeOfDeathId);
  $naturalPersonFormClarifyDeceasedDetailsDeathCertificateSeriesField.val(view.deathCertificateSeries);
  $naturalPersonFormClarifyDeceasedDetailsDeathCertificateNumberField.val(view.deathCertificateNumber);
  $naturalPersonFormClarifyDeceasedDetailsDeathCertificateIssuedAtField.val(
    view.deathCertificateIssuedAt !== null
      ? view.deathCertificateIssuedAt.split(`.`).reverse().join(`-`)
      : null
  );
  $naturalPersonFormClarifyDeceasedDetailsCremationCertificateNumberField.val(view.cremationCertificateNumber);
  $naturalPersonFormClarifyDeceasedDetailsCremationCertificateIssuedAtField.val(
    view.cremationCertificateIssuedAt !== null
      ? view.cremationCertificateIssuedAt.split(`.`).reverse().join(`-`)
      : null
  );
  $naturalPersonFormClarifyDeceasedDetailsBornAtField.val(
    view.bornAt !== null
      ? view.bornAt.split(`.`).reverse().join(`-`)
      : null
  );
  naturalPersonFormClarifyDeceasedDetails_hideAllValidationErrors();
  naturalPersonFormClarifyDeceasedDetailsModalObject.show();
}

// Autofocus
$naturalPersonFormClarifyDeceasedDetails.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#diedAt`).focus();
});

$naturalPersonFormClarifyDeceasedDetailsSaveAndCloseBtn.on(`click`, () => {
  const url = $naturalPersonFormClarifyDeceasedDetailsForm.data(`action-clarify-deceased-details`).replace(`{id}`, currentNaturalPersonId);
  naturalPersonFormClarifyDeceasedDetails_save(url, false);
});
$naturalPersonFormClarifyDeceasedDetailsCloseBtn.on(`click`, () => {
  naturalPersonFormClarifyDeceasedDetails_close();
});

function naturalPersonFormClarifyDeceasedDetails_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    diedAt: $naturalPersonFormClarifyDeceasedDetailsDiedAtField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsDiedAtField.val()
      : null,
    age: $naturalPersonFormClarifyDeceasedDetailsAgeField.val() !== ``
      ? parseInt($naturalPersonFormClarifyDeceasedDetailsAgeField.val())
      : null,
    causeOfDeathId: $naturalPersonFormClarifyDeceasedDetailsCauseOfDeathIdField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsCauseOfDeathIdField.val()
      : null,
    deathCertificateSeries: $naturalPersonFormClarifyDeceasedDetailsDeathCertificateSeriesField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsDeathCertificateSeriesField.val()
      : null,
    deathCertificateNumber: $naturalPersonFormClarifyDeceasedDetailsDeathCertificateNumberField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsDeathCertificateNumberField.val()
      : null,
    deathCertificateIssuedAt: $naturalPersonFormClarifyDeceasedDetailsDeathCertificateIssuedAtField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsDeathCertificateIssuedAtField.val()
      : null,
    cremationCertificateNumber: $naturalPersonFormClarifyDeceasedDetailsCremationCertificateNumberField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsCremationCertificateNumberField.val()
      : null,
    cremationCertificateIssuedAt: $naturalPersonFormClarifyDeceasedDetailsCremationCertificateIssuedAtField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsCremationCertificateIssuedAtField.val()
      : null,
    bornAt: $naturalPersonFormClarifyDeceasedDetailsBornAtField.val() !== ``
      ? $naturalPersonFormClarifyDeceasedDetailsBornAtField.val()
      : null,
    token: $naturalPersonFormClarifyDeceasedDetailsCsrfTokenField.val(),
  };
  $.ajax({
    dataType: `json`,
    method: `PATCH`,
    url: url,
    data: JSON.stringify(data),
    contentType: `application/json; charset=utf-8`,
  })
  .done(() => {
    buildToast().fire({
      icon: `success`,
      title: `Данные о смерти успешно ${isFirstEnteringDeceasedDataMode ? `внесены` : `уточнены`}.`,
    });
    naturalPersonFormClarifyDeceasedDetails_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function naturalPersonFormClarifyDeceasedDetails_close() {
  naturalPersonFormClarifyDeceasedDetailsModalObject.hide();
  window[persistedCallbackClarifyDeceasedDetails](...persistedArgsClarifyDeceasedDetails);
}

function naturalPersonFormClarifyDeceasedDetails_hideAllValidationErrors() {
  $naturalPersonFormClarifyDeceasedDetailsForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$naturalPersonFormClarifyDeceasedDetailsForm.on(`input`, `.is-invalid`, (e) => {
  naturalPersonFormClarifyDeceasedDetails_hideValidationError(e);

  // Hide validation errors of inter-related input fields
  switch ($(e.target).attr(`id`)) {
    case `diedAt`:
    case `age`:
      $naturalPersonFormClarifyDeceasedDetailsDiedAtField.removeClass(`is-invalid`);
      $naturalPersonFormClarifyDeceasedDetailsAgeField.removeClass(`is-invalid`);
  }
});
function naturalPersonFormClarifyDeceasedDetails_hideValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
