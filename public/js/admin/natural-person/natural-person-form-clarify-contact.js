const $naturalPersonFormClarifyContact                     = $(`#modalNaturalPersonFormClarifyContact`);
const $naturalPersonFormClarifyContactTitle                = $naturalPersonFormClarifyContact.find(`.modal-title`)
const $naturalPersonFormClarifyContactForm                 = $naturalPersonFormClarifyContact.find(`form`);
const $naturalPersonFormClarifyContactPhoneField           = $naturalPersonFormClarifyContact.find(`input[id=phone]`);
const $naturalPersonFormClarifyContactPhoneAdditionalField = $naturalPersonFormClarifyContact.find(`input[id=phoneAdditional]`);
const $naturalPersonFormClarifyContactAddressField         = $naturalPersonFormClarifyContact.find(`input[id=address]`);
const $naturalPersonFormClarifyContactEmailField           = $naturalPersonFormClarifyContact.find(`input[id=email]`);
const $naturalPersonFormClarifyContactCsrfTokenField       = $naturalPersonFormClarifyContact.find(`input[id=token]`);
const $naturalPersonFormClarifyContactSaveAndCloseBtn      = $naturalPersonFormClarifyContact.find(`.js-save-and-close`);
const $naturalPersonFormClarifyContactCloseBtn             = $naturalPersonFormClarifyContact.find(`.js-close`);
const naturalPersonFormClarifyContactModalObject           = new bootstrap.Modal(`#modalNaturalPersonFormClarifyContact`, {});

let persistedCallbackClarifyContact;
let persistedArgsClarifyContact;

function naturalPersonFormClarifyContact_show(view, callback, args) {
  persistedCallbackClarifyContact = callback;
  persistedArgsClarifyContact     = args;
  currentNaturalPersonId          = view.id;
  $naturalPersonFormClarifyContactTitle.html(`Уточнение контактных данных - <span>${view.fullName}</span>`);
  $naturalPersonFormClarifyContactPhoneField.val(view.phone);
  $naturalPersonFormClarifyContactPhoneAdditionalField.val(view.phoneAdditional);
  $naturalPersonFormClarifyContactAddressField.val(view.address);
  $naturalPersonFormClarifyContactEmailField.val(view.email);
  naturalPersonFormClarifyContact_hideAllValidationErrors();
  naturalPersonFormClarifyContactModalObject.show();
}

// Autofocus
$naturalPersonFormClarifyContact.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#phone`).focus();
});

$naturalPersonFormClarifyContactSaveAndCloseBtn.on(`click`, () => {
  const url = $naturalPersonFormClarifyContactForm.data(`action-clarify-contact`).replace(`{id}`, currentNaturalPersonId);
  naturalPersonFormClarifyContact_save(url, false);
});
$naturalPersonFormClarifyContactCloseBtn.on(`click`, () => {
  naturalPersonFormClarifyContact_close();
});

function naturalPersonFormClarifyContact_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    phone: $naturalPersonFormClarifyContactPhoneField.val() !== ``
      ? $naturalPersonFormClarifyContactPhoneField.val()
      : null,
    phoneAdditional: $naturalPersonFormClarifyContactPhoneAdditionalField.val() !== ``
      ? $naturalPersonFormClarifyContactPhoneAdditionalField.val()
      : null,
    address: $naturalPersonFormClarifyContactAddressField.val() !== ``
      ? $naturalPersonFormClarifyContactAddressField.val()
      : null,
    email: $naturalPersonFormClarifyContactEmailField.val() !== ``
      ? $naturalPersonFormClarifyContactEmailField.val()
      : null,
    token: $naturalPersonFormClarifyContactCsrfTokenField.val(),
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
      title: `Контактные данные успешно уточнены.`,
    });
    naturalPersonFormClarifyContact_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function naturalPersonFormClarifyContact_close() {
  naturalPersonFormClarifyContactModalObject.hide();
  window[persistedCallbackClarifyContact](...persistedArgsClarifyContact);
}

function naturalPersonFormClarifyContact_hideAllValidationErrors() {
  $naturalPersonFormClarifyContactForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$naturalPersonFormClarifyContactForm.on(`input`, `.is-invalid`, () => {
  naturalPersonFormClarifyContact_hideAllValidationErrors();
});
