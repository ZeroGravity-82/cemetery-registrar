const $graveSiteFormClarifyLocation                     = $(`#modalGraveSiteFormClarifyLocation`);
const $graveSiteFormClarifyLocationTitle                = $graveSiteFormClarifyLocation.find(`.modal-title`)
const $graveSiteFormClarifyLocationForm                 = $graveSiteFormClarifyLocation.find(`form`);
const $graveSiteFormClarifyLocationCemeteryBlockIdField = $graveSiteFormClarifyLocation.find(`select[id=cemeteryBlockId]`);
const $graveSiteFormClarifyLocationRowInBlockField      = $graveSiteFormClarifyLocation.find(`input[id=rowInBlock]`);
const $graveSiteFormClarifyLocationPositionInRowField   = $graveSiteFormClarifyLocation.find(`input[id=positionInRow]`);
const $graveSiteFormClarifyLocationCsrfTokenField       = $graveSiteFormClarifyLocation.find(`input[id=token]`);
const $graveSiteFormClarifyLocationSaveAndCloseBtn      = $graveSiteFormClarifyLocation.find(`.js-save-and-close`);
const $graveSiteFormClarifyLocationCloseBtn             = $graveSiteFormClarifyLocation.find(`.js-close`);
const graveSiteFormClarifyLocationModalObject           = new bootstrap.Modal(`#modalGraveSiteFormClarifyLocation`, {});

let persistedCallbackClarifyLocation;
let persistedArgsClarifyLocation;

function graveSiteFormClarifyLocation_show(view, callback, args) {
  persistedCallbackClarifyLocation = callback;
  persistedArgsClarifyLocation     = args;
  currentGraveSiteId               = view.id;
  let graveSiteCardTitle = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      graveSiteCardTitle += `, место ${view.positionInRow}`;
    }
  $graveSiteFormClarifyLocationTitle.html(`Уточнение расположения участка - <span>${graveSiteCardTitle}</span>`);
  $graveSiteFormClarifyLocationCemeteryBlockIdField.val(view.cemeteryBlockId);
  $graveSiteFormClarifyLocationRowInBlockField.val(view.rowInBlock);
  $graveSiteFormClarifyLocationPositionInRowField.val(view.positionInRow);
  graveSiteFormClarifyLocation_hideAllValidationErrors();
  graveSiteFormClarifyLocationModalObject.show();
}

// Autofocus
$graveSiteFormClarifyLocation.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#cemeteryBlockId`).focus();
});

$graveSiteFormClarifyLocationSaveAndCloseBtn.on(`click`, () => {
  const url = $graveSiteFormClarifyLocationForm.data(`action-clarify-location`).replace(`{id}`, currentGraveSiteId);
  graveSiteFormClarifyLocation_save(url, false);
});
$graveSiteFormClarifyLocationCloseBtn.on(`click`, () => {
  graveSiteFormClarifyLocation_close();
});

function graveSiteFormClarifyLocation_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    cemeteryBlockId: $graveSiteFormClarifyLocationCemeteryBlockIdField.val() !== ``
        ? $graveSiteFormClarifyLocationCemeteryBlockIdField.val()
        : null,
    rowInBlock: $graveSiteFormClarifyLocationRowInBlockField.val() !== ``
        ? parseInt($graveSiteFormClarifyLocationRowInBlockField.val())
        : null,
    positionInRow: $graveSiteFormClarifyLocationPositionInRowField.val() !== ``
        ? parseInt($graveSiteFormClarifyLocationPositionInRowField.val())
        : null,
    token: $graveSiteFormClarifyLocationCsrfTokenField.val(),
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
      title: `Расположение участка успешно уточнено.`,
    });
    graveSiteFormClarifyLocation_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function graveSiteFormClarifyLocation_close() {
  graveSiteFormClarifyLocationModalObject.hide();
  window[persistedCallbackClarifyLocation](...persistedArgsClarifyLocation);
}

function graveSiteFormClarifyLocation_hideAllValidationErrors() {
  $graveSiteFormClarifyLocationForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$graveSiteFormClarifyLocationForm.on(`change`, `.is-invalid`, (e) => {
  graveSiteFormClarifyLocation_removeValidationError(e);
});
$graveSiteFormClarifyLocationForm.on(`input`, `.is-invalid`, (e) => {
  graveSiteFormClarifyLocation_removeValidationError(e);
});
function graveSiteFormClarifyLocation_removeValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
