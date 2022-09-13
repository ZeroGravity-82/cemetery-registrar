const $naturalPersonFormNew                       = $(`#modalNaturalPersonFormNew`);
const $naturalPersonFormNewTitle                  = $naturalPersonFormNew.find(`.modal-title`)
const $naturalPersonFormNewForm                   = $naturalPersonFormNew.find(`form`);
const $naturalPersonFormNewCemeteryBlockIdField   = $naturalPersonFormNew.find(`select[id=cemeteryBlockId]`);
const $naturalPersonFormNewRowInBlockField        = $naturalPersonFormNew.find(`input[id=rowInBlock]`);
const $naturalPersonFormNewPositionInRowField     = $naturalPersonFormNew.find(`input[id=positionInRow]`);
const $naturalPersonFormNewSizeField              = $naturalPersonFormNew.find(`input[id=size]`);
const $naturalPersonFormNewGeoPositionField       = $naturalPersonFormNew.find(`input[id=geoPosition]`);
const $naturalPersonFormNewGeoPositionErrorField  = $naturalPersonFormNew.find(`input[id=geoPositionError]`);
const $naturalPersonFormNewPersonInChargeField    = $naturalPersonFormNew.find(`select[id=personInCharge]`);
const $naturalPersonFormNewCsrfTokenField         = $naturalPersonFormNew.find(`input[id=token]`);
const $naturalPersonFormNewSaveAndCloseBtn        = $naturalPersonFormNew.find(`.js-save-and-close`);
const $naturalPersonFormNewCloseBtn               = $naturalPersonFormNew.find(`.js-close`);
const naturalPersonFormNewModalObject             = new bootstrap.Modal(`#modalNaturalPersonFormNew`, {});

function naturalPersonFormNew_show() {
  $naturalPersonFormNewTitle.html(`Физлица (создание)`);
  $naturalPersonFormNewCemeteryBlockIdField.val(null).change();
  $naturalPersonFormNewRowInBlockField.val(null);
  $naturalPersonFormNewPositionInRowField.val(null);
  $naturalPersonFormNewSizeField.val(null);
  $naturalPersonFormNewGeoPositionField.val(null);
  $naturalPersonFormNewGeoPositionErrorField.val(null);
  naturalPersonFormNew_hideAllValidationErrors();
  naturalPersonFormNewModalObject.show();
}

// Autofocus
$naturalPersonFormNew.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#cemeteryBlockId`).focus();
});

$naturalPersonFormNewSaveAndCloseBtn.on(`click`, () => {
  const url = $naturalPersonFormNewForm.data(`action-new`);
  naturalPersonFormNew_save(url, true);
});
$naturalPersonFormNewCloseBtn.on(`click`, () => {
  naturalPersonForm_close();
});
$naturalPersonFormNewGeoPositionField.on(`change`, () => {
  $naturalPersonFormNewGeoPositionErrorField.val(``);
});

function naturalPersonFormNew_save(url, isReloadRequired = false) {
  $spinner.show();
  const geoPositionLatitude  = $naturalPersonFormNewGeoPositionField.val().split(`,`)[0];
  const geoPositionLongitude = $naturalPersonFormNewGeoPositionField.val().split(`,`)[1];
  const data                 = {
    cemeteryBlockId: $naturalPersonFormNewCemeteryBlockIdField.val() !== ``
        ? $naturalPersonFormNewCemeteryBlockIdField.val()
        : null,
    rowInBlock: $naturalPersonFormNewRowInBlockField.val() !== ``
        ? parseInt($naturalPersonFormNewRowInBlockField.val())
        : null,
    positionInRow: $naturalPersonFormNewPositionInRowField.val() !== ``
        ? parseInt($naturalPersonFormNewPositionInRowField.val())
        : null,
    geoPositionLatitude: $naturalPersonFormNewGeoPositionField.val() !== ``
        ? geoPositionLatitude !== undefined ? geoPositionLatitude.trim() : null
        : null,
    geoPositionLongitude: $naturalPersonFormNewGeoPositionField.val() !== ``
        ? geoPositionLongitude !== undefined ? geoPositionLongitude.trim() : null
        : null,
    geoPositionError: $naturalPersonFormNewGeoPositionErrorField.val() !== ``
        ? $naturalPersonFormNewGeoPositionErrorField.val()
        : null,
    size: $naturalPersonFormNewSizeField.val() !== ``
        ? $naturalPersonFormNewSizeField.val()
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
          title: `Участок успешно создан.`,
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
