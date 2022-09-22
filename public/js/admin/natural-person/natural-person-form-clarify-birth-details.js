const $naturalPersonFormClarifyBirthDetails                  = $(`#modalNaturalPersonFormClarifyBirthDetails`);
const $naturalPersonFormClarifyBirthDetailsTitle             = $naturalPersonFormClarifyBirthDetails.find(`.modal-title`)
const $naturalPersonFormClarifyBirthDetailsForm              = $naturalPersonFormClarifyBirthDetails.find(`form`);
const $naturalPersonFormClarifyBirthDetailsBornAtField       = $naturalPersonFormClarifyBirthDetails.find(`input[id=bornAt]`);
const $naturalPersonFormClarifyBirthDetailsPlaceOfBirthField = $naturalPersonFormClarifyBirthDetails.find(`input[id=placeOfBirth]`);
const $naturalPersonFormClarifyBirthDetailsDiedAtField       = $naturalPersonFormClarifyBirthDetails.find(`input[id=diedAt]`);
const $naturalPersonFormClarifyBirthDetailsAgeField          = $naturalPersonFormClarifyBirthDetails.find(`input[id=age]`);
const $naturalPersonFormClarifyBirthDetailsCsrfTokenField    = $naturalPersonFormClarifyBirthDetails.find(`input[id=token]`);
const $naturalPersonFormClarifyBirthDetailsSaveAndCloseBtn   = $naturalPersonFormClarifyBirthDetails.find(`.js-save-and-close`);
const $naturalPersonFormClarifyBirthDetailsCloseBtn          = $naturalPersonFormClarifyBirthDetails.find(`.js-close`);
const naturalPersonFormClarifyBirthDetailsModalObject        = new bootstrap.Modal(`#modalNaturalPersonFormClarifyBirthDetails`, {});

let persistedCallbackClarifyBirthDetails;
let persistedArgsClarifyBirthDetails;

function naturalPersonFormClarifyBirthDetails_show(view, callback, args) {
  persistedCallbackClarifyBirthDetails = callback;
  persistedArgsClarifyBirthDetails     = args;
  currentNaturalPersonId               = view.id;
  $naturalPersonFormClarifyBirthDetailsTitle.html(`Уточнение даты и места рождения - <span>${view.fullName}</span>`);
  $naturalPersonFormClarifyBirthDetailsBornAtField.val(
    view.bornAt !== null
      ? view.bornAt.split(`.`).reverse().join(`-`)
      : null
  );
  $naturalPersonFormClarifyBirthDetailsPlaceOfBirthField.val(view.placeOfBirth);
  $naturalPersonFormClarifyBirthDetailsDiedAtField.val(
    view.diedAt !== null
      ? view.diedAt.split(`.`).reverse().join(`-`)
      : null
  );
  $naturalPersonFormClarifyBirthDetailsAgeField.val(view.age);
  naturalPersonFormClarifyBirthDetails_hideAllValidationErrors();
  naturalPersonFormClarifyBirthDetailsModalObject.show();
}

// Autofocus
$naturalPersonFormClarifyBirthDetails.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#bornAt`).focus();
});

$naturalPersonFormClarifyBirthDetailsSaveAndCloseBtn.on(`click`, () => {
  const url = $naturalPersonFormClarifyBirthDetailsForm.data(`action-clarify-birth-details`).replace(`{id}`, currentNaturalPersonId);
  naturalPersonFormClarifyBirthDetails_save(url, false);
});
$naturalPersonFormClarifyBirthDetailsCloseBtn.on(`click`, () => {
  naturalPersonFormClarifyBirthDetails_close();
});

function naturalPersonFormClarifyBirthDetails_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    bornAt: $naturalPersonFormClarifyBirthDetailsBornAtField.val() !== ``
      ? $naturalPersonFormClarifyBirthDetailsBornAtField.val()
      : null,
    placeOfBirth: $naturalPersonFormClarifyBirthDetailsPlaceOfBirthField.val() !== ``
      ? $naturalPersonFormClarifyBirthDetailsPlaceOfBirthField.val()
      : null,
    diedAt: $naturalPersonFormClarifyBirthDetailsDiedAtField.val() !== ``
      ? $naturalPersonFormClarifyBirthDetailsDiedAtField.val()
      : null,
    age: $naturalPersonFormClarifyBirthDetailsAgeField.val() !== ``
      ? parseInt($naturalPersonFormClarifyBirthDetailsAgeField.val())
      : null,
    token: $naturalPersonFormClarifyBirthDetailsCsrfTokenField.val(),
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
      title: `Дата и место рождения успешно уточнены.`,
    });
    naturalPersonFormClarifyBirthDetails_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function naturalPersonFormClarifyBirthDetails_close() {
  naturalPersonFormClarifyBirthDetailsModalObject.hide();
  window[persistedCallbackClarifyBirthDetails](...persistedArgsClarifyBirthDetails);
}

function naturalPersonFormClarifyBirthDetails_hideAllValidationErrors() {
  $naturalPersonFormClarifyBirthDetailsForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$naturalPersonFormClarifyBirthDetailsForm.on(`change`, `.is-invalid`, (e) => {
  naturalPersonFormClarifyBirthDetails_hideAllValidationErrors();
});
$naturalPersonFormClarifyBirthDetailsForm.on(`input`, `.is-invalid`, (e) => {
  naturalPersonFormClarifyBirthDetails_hideAllValidationErrors();
});
