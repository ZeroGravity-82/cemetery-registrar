const $graveSiteFormReplacePersonInCharge                           = $(`#modalGraveSiteFormReplacePersonInCharge`);
const $graveSiteFormReplacePersonInChargeTitle                      = $graveSiteFormReplacePersonInCharge.find(`.modal-title`)
const $graveSiteFormReplacePersonInChargeForm                       = $graveSiteFormReplacePersonInCharge.find(`form`);
const $graveSiteFormReplacePersonInChargePersonInChargeCurrentField = $graveSiteFormReplacePersonInCharge.find(`p.js-person-in-charge-current`);
const $graveSiteFormReplacePersonInChargePersonInChargeNewField     = $graveSiteFormReplacePersonInCharge.find(`select[id=personInChargeNew]`);
const $graveSiteFormReplacePersonInChargeCsrfTokenField             = $graveSiteFormReplacePersonInCharge.find(`input[id=token]`);
const $graveSiteFormReplacePersonInChargeSaveAndCloseBtn            = $graveSiteFormReplacePersonInCharge.find(`.js-save-and-close`);
const $graveSiteFormReplacePersonInChargeCloseBtn                   = $graveSiteFormReplacePersonInCharge.find(`.js-close`);
const graveSiteFormReplacePersonInChargeModalObject                 = new bootstrap.Modal(`#modalGraveSiteFormReplacePersonInCharge`, {});

let persistedCallbackReplacePersonInCharge;
let persistedArgsReplacePersonInCharge;

function graveSiteFormReplacePersonInCharge_show(view, callback, args) {
  persistedCallbackReplacePersonInCharge = callback;
  persistedArgsReplacePersonInCharge     = args;
  currentGraveSiteId                     = view.id;
  let graveSiteCardTitle = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      graveSiteCardTitle += `, место ${view.positionInRow}`;
    }
  $graveSiteFormReplacePersonInChargeTitle.html(`Замена ответственного за участок - <span>${graveSiteCardTitle}</span>`);
  graveSiteFormReplacePersonInCharge_hideAllValidationErrors();
  graveSiteFormReplacePersonInChargeModalObject.show();
}

// Autofocus
$graveSiteFormReplacePersonInCharge.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#personInChargeNew`).focus();
});

$graveSiteFormReplacePersonInChargeSaveAndCloseBtn.on(`click`, () => {
  const url = $graveSiteFormReplacePersonInChargeForm.data(`action-replace-person-in-charge`).replace(`{id}`, currentGraveSiteId);
  graveSiteFormReplacePersonInCharge_save(url, false);
});
$graveSiteFormReplacePersonInChargeCloseBtn.on(`click`, () => {
  graveSiteFormReplacePersonInCharge_close();
});

function graveSiteFormReplacePersonInCharge_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    cemeteryBlockId: $graveSiteFormReplacePersonInChargeCemeteryBlockIdField.val() !== ``
        ? $graveSiteFormReplacePersonInChargeCemeteryBlockIdField.val()
        : null,
    rowInBlock: $graveSiteFormReplacePersonInChargeRowInBlockField.val() !== ``
        ? parseInt($graveSiteFormReplacePersonInChargeRowInBlockField.val())
        : null,
    positionInRow: $graveSiteFormReplacePersonInChargePositionInRowField.val() !== ``
        ? parseInt($graveSiteFormReplacePersonInChargePositionInRowField.val())
        : null,
    token: $graveSiteFormReplacePersonInChargeCsrfTokenField.val(),
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
      title: `Ответственный за участок успешно заменён.`,
    });
    graveSiteFormReplacePersonInCharge_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function graveSiteFormReplacePersonInCharge_close() {
  graveSiteFormReplacePersonInChargeModalObject.hide();
  window[persistedCallbackReplacePersonInCharge](...persistedArgsReplacePersonInCharge);
}

function graveSiteFormReplacePersonInCharge_hideAllValidationErrors() {
  $graveSiteFormReplacePersonInChargeForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$graveSiteFormReplacePersonInChargeForm.on(`input`, `.is-invalid`, (e) => {
  graveSiteFormReplacePersonInCharge_hideValidationError(e);
});
function graveSiteFormReplacePersonInCharge_hideValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
