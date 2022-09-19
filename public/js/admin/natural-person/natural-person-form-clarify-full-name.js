const $naturalPersonFormClarifyFullName                = $(`#modalNaturalPersonFormClarifyFullName`);
const $naturalPersonFormClarifyFullNameTitle           = $naturalPersonFormClarifyFullName.find(`.modal-title`)
const $naturalPersonFormClarifyFullNameForm            = $naturalPersonFormClarifyFullName.find(`form`);
const $naturalPersonFormClarifyFullNameFullNameField   = $naturalPersonFormClarifyFullName.find(`input[id=fullName]`);
const $naturalPersonFormClarifyFullNameCsrfTokenField  = $naturalPersonFormClarifyFullName.find(`input[id=token]`);
const $naturalPersonFormClarifyFullNameSaveAndCloseBtn = $naturalPersonFormClarifyFullName.find(`.js-save-and-close`);
const $naturalPersonFormClarifyFullNameCloseBtn        = $naturalPersonFormClarifyFullName.find(`.js-close`);
const naturalPersonFormClarifyFullNameModalObject      = new bootstrap.Modal(`#modalNaturalPersonFormClarifyFullName`, {});

let persistedCallbackClarifyFullName;
let persistedArgsClarifyFullName;

function naturalPersonFormClarifyFullName_show(view, callback, args) {
  persistedCallbackClarifyFullName = callback;
  persistedArgsClarifyFullName     = args;
  currentNaturalPersonId           = view.id;
  $naturalPersonFormClarifyFullNameTitle.html(`Уточнение ФИО - <span>${view.fullName}</span>`);
  $naturalPersonFormClarifyFullNameFullNameField.val(view.fullName);
  naturalPersonFormClarifyFullName_hideAllValidationErrors();
  naturalPersonFormClarifyFullNameModalObject.show();
}

// Autofocus
$naturalPersonFormClarifyFullName.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#fullName`).focus();
});

$naturalPersonFormClarifyFullNameSaveAndCloseBtn.on(`click`, () => {
  const url = $naturalPersonFormClarifyFullNameForm.data(`action-clarify-full-name`).replace(`{id}`, currentNaturalPersonId);
  naturalPersonFormClarifyFullName_save(url, false);
});
$naturalPersonFormClarifyFullNameCloseBtn.on(`click`, () => {
  naturalPersonFormClarifyFullName_close();
});

function naturalPersonFormClarifyFullName_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    fullName: $naturalPersonFormClarifyFullNameFullNameField.val() !== ``
        ? $naturalPersonFormClarifyFullNameFullNameField.val()
        : null,
    token: $naturalPersonFormClarifyFullNameCsrfTokenField.val(),
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
      title: `Значение ФИО успешно уточнено.`,
    });
    naturalPersonFormClarifyFullName_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function naturalPersonFormClarifyFullName_close() {
  naturalPersonFormClarifyFullNameModalObject.hide();
  window[persistedCallbackClarifyFullName](...persistedArgsClarifyFullName);
}

function naturalPersonFormClarifyFullName_hideAllValidationErrors() {
  $naturalPersonFormClarifyFullNameForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$naturalPersonFormClarifyFullNameForm.on(`change`, `.is-invalid`, (e) => {
  naturalPersonFormClarifyFullName_removeValidationError(e);
});
$naturalPersonFormClarifyFullNameForm.on(`input`, `.is-invalid`, (e) => {
  naturalPersonFormClarifyFullName_removeValidationError(e);
});
function naturalPersonFormClarifyFullName_removeValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
