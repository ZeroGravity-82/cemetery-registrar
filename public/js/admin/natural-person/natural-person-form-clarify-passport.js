const $naturalPersonFormClarifyPassport                     = $(`#modalNaturalPersonFormClarifyPassport`);
const $naturalPersonFormClarifyPassportTitle                = $naturalPersonFormClarifyPassport.find(`.modal-title`)
const $naturalPersonFormClarifyPassportForm                 = $naturalPersonFormClarifyPassport.find(`form`);
const $naturalPersonFormClarifyPassportSeriesField          = $naturalPersonFormClarifyPassport.find(`input[id=passportSeries]`);
const $naturalPersonFormClarifyPassportNumberField          = $naturalPersonFormClarifyPassport.find(`input[id=passportNumber]`);
const $naturalPersonFormClarifyPassportIssuedAtField        = $naturalPersonFormClarifyPassport.find(`input[id=passportIssuedAt]`);
const $naturalPersonFormClarifyPassportIssuedByField        = $naturalPersonFormClarifyPassport.find(`input[id=passportIssuedBy]`);
const $naturalPersonFormClarifyPassportDivisionCodeField    = $naturalPersonFormClarifyPassport.find(`input[id=passportDivisionCode]`);
const $naturalPersonFormClarifyPassportCsrfTokenField       = $naturalPersonFormClarifyPassport.find(`input[id=token]`);
const $naturalPersonFormClarifyPassportSaveAndCloseBtn      = $naturalPersonFormClarifyPassport.find(`.js-save-and-close`);
const $naturalPersonFormClarifyPassportCloseBtn             = $naturalPersonFormClarifyPassport.find(`.js-close`);
const naturalPersonFormClarifyPassportModalObject           = new bootstrap.Modal(`#modalNaturalPersonFormClarifyPassport`, {});

let persistedCallbackClarifyPassport;
let persistedArgsClarifyPassport;

function naturalPersonFormClarifyPassport_show(view, callback, args) {
  persistedCallbackClarifyPassport = callback;
  persistedArgsClarifyPassport     = args;
  currentNaturalPersonId           = view.id;
  $naturalPersonFormClarifyPassportTitle.html(`Уточнение паспортных данных - <span>${view.fullName}</span>`);
  $naturalPersonFormClarifyPassportSeriesField.val(view.passportSeries);
  $naturalPersonFormClarifyPassportNumberField.val(view.passportNumber);
  $naturalPersonFormClarifyPassportIssuedAtField.val(
      view.passportIssuedAt !== null
        ? view.passportIssuedAt.split(`.`).reverse().join(`-`)
        : null
  );
  $naturalPersonFormClarifyPassportIssuedByField.val(view.passportIssuedBy);
  $naturalPersonFormClarifyPassportDivisionCodeField.val(view.passportDivisionCode);
  naturalPersonFormClarifyPassport_hideAllValidationErrors();
  naturalPersonFormClarifyPassportModalObject.show();
}

// Autofocus
$naturalPersonFormClarifyPassport.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#passportSeries`).focus();
});

$naturalPersonFormClarifyPassportSaveAndCloseBtn.on(`click`, () => {
  const url = $naturalPersonFormClarifyPassportForm.data(`action-clarify-passport`).replace(`{id}`, currentNaturalPersonId);
  naturalPersonFormClarifyPassport_save(url, false);
});
$naturalPersonFormClarifyPassportCloseBtn.on(`click`, () => {
  naturalPersonFormClarifyPassport_close();
});

function naturalPersonFormClarifyPassport_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    passportSeries: $naturalPersonFormClarifyPassportSeriesField.val() !== ``
        ? $naturalPersonFormClarifyPassportSeriesField.val()
        : null,
    passportNumber: $naturalPersonFormClarifyPassportNumberField.val() !== ``
        ? $naturalPersonFormClarifyPassportNumberField.val()
        : null,
    passportIssuedAt: $naturalPersonFormClarifyPassportIssuedAtField.val() !== ``
        ? $naturalPersonFormClarifyPassportIssuedAtField.val()
        : null,
    passportIssuedBy: $naturalPersonFormClarifyPassportIssuedByField.val() !== ``
        ? $naturalPersonFormClarifyPassportIssuedByField.val()
        : null,
    passportDivisionCode: $naturalPersonFormClarifyPassportDivisionCodeField.val() !== ``
        ? $naturalPersonFormClarifyPassportDivisionCodeField.val()
        : null,
    token: $naturalPersonFormClarifyPassportCsrfTokenField.val(),
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
      title: `Паспортные данные успешно уточнены.`,
    });
    naturalPersonFormClarifyPassport_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function naturalPersonFormClarifyPassport_close() {
  naturalPersonFormClarifyPassportModalObject.hide();
  window[persistedCallbackClarifyPassport](...persistedArgsClarifyPassport);
}

function naturalPersonFormClarifyPassport_hideAllValidationErrors() {
  $naturalPersonFormClarifyPassportForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$naturalPersonFormClarifyPassportForm.on(`change`, `.is-invalid`, (e) => {
  naturalPersonFormClarifyFullName_hideValidationError(e);
});
$naturalPersonFormClarifyPassportForm.on(`input`, `.is-invalid`, (e) => {
  naturalPersonFormClarifyFullName_hideValidationError(e);
});
