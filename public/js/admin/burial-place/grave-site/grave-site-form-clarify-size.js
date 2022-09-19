const $graveSiteFormClarifySize                = $(`#modalGraveSiteFormClarifySize`);
const $graveSiteFormClarifySizeTitle           = $graveSiteFormClarifySize.find(`.modal-title`)
const $graveSiteFormClarifySizeForm            = $graveSiteFormClarifySize.find(`form`);
const $graveSiteFormClarifySizeSizeField       = $graveSiteFormClarifySize.find(`input[id=size]`);
const $graveSiteFormClarifySizeCsrfTokenField  = $graveSiteFormClarifySize.find(`input[id=token]`);
const $graveSiteFormClarifySizeSaveAndCloseBtn = $graveSiteFormClarifySize.find(`.js-save-and-close`);
const $graveSiteFormClarifySizeCloseBtn        = $graveSiteFormClarifySize.find(`.js-close`);
const graveSiteFormClarifySizeModalObject      = new bootstrap.Modal(`#modalGraveSiteFormClarifySize`, {});

let persistedCallbackClarifySize;
let persistedArgsClarifySize;

function graveSiteFormClarifySize_show(view, callback, args) {
  persistedCallbackClarifySize = callback;
  persistedArgsClarifySize     = args;
  currentGraveSiteId           = view.id;
  let graveSiteCardTitle = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      graveSiteCardTitle += `, место ${view.positionInRow}`;
    }
  $graveSiteFormClarifySizeTitle.html(`Уточнение размера участка - <span>${graveSiteCardTitle}</span>`);
  $graveSiteFormClarifySizeSizeField.val(view.size);
  graveSiteFormClarifySize_hideAllValidationErrors();
  graveSiteFormClarifySizeModalObject.show();
}

// Autofocus
$graveSiteFormClarifySize.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#size`).focus();
});

$graveSiteFormClarifySizeSaveAndCloseBtn.on(`click`, () => {
  const url = $graveSiteFormClarifySizeForm.data(`action-clarify-size`).replace(`{id}`, currentGraveSiteId);
  graveSiteFormClarifySize_save(url, false);
});
$graveSiteFormClarifySizeCloseBtn.on(`click`, () => {
  graveSiteFormClarifySize_close();
});

function graveSiteFormClarifySize_save(url, isReloadRequired = false) {
  $spinner.show();
  const data = {
    size: $graveSiteFormClarifySizeSizeField.val() !== ``
        ? $graveSiteFormClarifySizeSizeField.val()
        : null,
    token: $graveSiteFormClarifySizeCsrfTokenField.val(),
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
      title: `Размер участка успешно уточнён.`,
    });
    graveSiteFormClarifySize_close();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function graveSiteFormClarifySize_close() {
  graveSiteFormClarifySizeModalObject.hide();
  window[persistedCallbackClarifySize](...persistedArgsClarifySize);
}

function graveSiteFormClarifySize_hideAllValidationErrors() {
  $graveSiteFormClarifySizeForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$graveSiteFormClarifySizeForm.on(`change`, `.is-invalid`, (e) => {
  graveSiteFormClarifySize_removeValidationError(e);
});
$graveSiteFormClarifySizeForm.on(`input`, `.is-invalid`, (e) => {
  graveSiteFormClarifySize_removeValidationError(e);
});
function graveSiteFormClarifySize_removeValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
