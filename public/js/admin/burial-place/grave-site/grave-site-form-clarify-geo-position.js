const $graveSiteFormClarifyGeoPosition                 = $(`#modalGraveSiteFormClarifyGeoPosition`);
const $graveSiteFormClarifyGeoPositionTitle            = $graveSiteFormClarifyGeoPosition.find(`.modal-title`)
const $graveSiteFormClarifyGeoPositionForm             = $graveSiteFormClarifyGeoPosition.find(`form`);
const $graveSiteFormClarifyGeoPositionGeoPositionField = $graveSiteFormClarifyGeoPosition.find(`input[id=geoPosition]`);
const $graveSiteFormClarifyGeoPositionCsrfTokenField   = $graveSiteFormClarifyGeoPosition.find(`input[id=token]`);
const $graveSiteFormClarifyGeoPositionSaveAndCloseBtn  = $graveSiteFormClarifyGeoPosition.find(`.js-save-and-close`);
const $graveSiteFormClarifyGeoPositionCloseBtn         = $graveSiteFormClarifyGeoPosition.find(`.js-close`);
const graveSiteFormClarifyGeoPositionModalObject       = new bootstrap.Modal(`#modalGraveSiteFormClarifyGeoPosition`, {});

let persistedCallbackClarifyGeoPosition;
let persistedArgsClarifyGeoPosition;

function graveSiteFormClarifyGeoPosition_show(view, callback, args) {
  persistedCallbackClarifyGeoPosition = callback;
  persistedArgsClarifyGeoPosition     = args;
  currentGraveSiteId           = view.id;
  let graveSiteCardTitle = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      graveSiteCardTitle += `, место ${view.positionInRow}`;
    }
  $graveSiteFormClarifyGeoPositionTitle.html(`<span>${graveSiteCardTitle}</span> (Уточнение размера участка)`);
  const geoPosition = view.geoPositionLatitude !== null && view.geoPositionLongitude !== null
    ? `${view.geoPositionLatitude}, ${view.geoPositionLongitude}`
    : null;
  $graveSiteFormClarifyGeoPositionGeoPositionField.val(geoPosition).change();
  graveSiteFormClarifyGeoPosition_hideAllValidationErrors();
  graveSiteFormClarifyGeoPositionModalObject.show();
}

// Autofocus
$graveSiteFormClarifyGeoPosition.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#geoPosition`).focus();
});

$graveSiteFormClarifyGeoPositionSaveAndCloseBtn.on(`click`, () => {
  const url = $graveSiteFormClarifyGeoPositionForm.data(`action-clarify-geo-position`).replace(`{id}`, currentGraveSiteId);
  graveSiteFormClarifyGeoPosition_save(url, false);
});
$graveSiteFormClarifyGeoPositionCloseBtn.on(`click`, () => {
  graveSiteFormClarifyGeoPosition_close();
});

function graveSiteFormClarifyGeoPosition_save(url, isReloadRequired = false) {
  $spinner.show();
  const geoPositionLatitude  = $graveSiteFormClarifyGeoPositionGeoPositionField.val().split(`,`)[0];
  const geoPositionLongitude = $graveSiteFormClarifyGeoPositionGeoPositionField.val().split(`,`)[1];
  const data = {
    geoPositionLatitude: $graveSiteFormClarifyGeoPositionGeoPositionField.val() !== ``
        ? geoPositionLatitude !== undefined ? geoPositionLatitude.trim() : null
        : null,
    geoPositionLongitude: $graveSiteFormClarifyGeoPositionGeoPositionField.val() !== ``
        ? geoPositionLongitude !== undefined ? geoPositionLongitude.trim() : null
        : null,
    geoPositionError: null,
    token: $graveSiteFormClarifyGeoPositionCsrfTokenField.val(),
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
      title: `Геопозиция участка успешно уточнена.`,
    });
    graveSiteFormClarifyGeoPosition_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function graveSiteFormClarifyGeoPosition_close() {
  graveSiteFormClarifyGeoPositionModalObject.hide();
  window[persistedCallbackClarifyGeoPosition](...persistedArgsClarifyGeoPosition);
}

function graveSiteFormClarifyGeoPosition_hideAllValidationErrors() {
  $graveSiteFormClarifyGeoPositionForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$graveSiteFormClarifyGeoPositionForm.on(`change`, `.is-invalid`, (e) => {
  graveSiteFormClarifyGeoPosition_removeValidationError(e);
});
$graveSiteFormClarifyGeoPositionForm.on(`input`, `.is-invalid`, (e) => {
  graveSiteFormClarifyGeoPosition_removeValidationError(e);
});
function graveSiteFormClarifyGeoPosition_removeValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
