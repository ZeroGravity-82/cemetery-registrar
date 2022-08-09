const $tableGraveSite                      = $(`#graveSiteList`);
const $modalGraveSite                      = $(`#modalGraveSite`);
const $modalGraveSiteTitle                 = $modalGraveSite.find(`.modal-title`)
const $modalGraveSiteForm                  = $modalGraveSite.find(`form`);
const $modalGraveSiteCemeteryBlockIdField  = $modalGraveSite.find(`select[id=cemeteryBlockId]`);
const $modalGraveSiteRowInBlockField       = $modalGraveSite.find(`input[id=rowInBlock]`);
const $modalGraveSitePositionInRowField    = $modalGraveSite.find(`input[id=positionInRow]`);
const $modalGraveSiteSizeField             = $modalGraveSite.find(`input[id=size]`);
const $modalGraveSiteGeoPositionField      = $modalGraveSite.find(`input[id=geoPosition]`);
const $modalGraveSiteGeoPositionErrorField = $modalGraveSite.find(`input[id=geoPositionError]`);
const $modalGraveSiteCsrfTokenField        = $modalGraveSite.find(`input[id=token]`);
const $modalGraveSiteRemoveBtnWrapper      = $modalGraveSite.find(`.js-remove-wrapper`);
const $modalGraveSiteSaveBtn               = $modalGraveSite.find(`.js-save`);
const $modalGraveSiteTimestamps            = $modalGraveSite.find(`.timestamps`);
const modalGraveSiteObject                 = new bootstrap.Modal(`#modalGraveSite`, {});

let modeGraveSite = null;
let idGraveSite   = null;

// Create
$body.on(`click`, `.js-create-grave-site-btn`, () => {
  modeGraveSite = `new`;
  idGraveSite   = null;
  $modalGraveSite.data(`id`, idGraveSite);
  $modalGraveSite.removeClass(`edit-form`);
  $modalGraveSiteRemoveBtnWrapper.removeClass(`d-none`).addClass(`d-none`);
  $modalGraveSiteSaveBtn.removeClass(`d-none`).addClass(`d-none`);
  $modalGraveSiteTimestamps.removeClass(`d-none`).addClass(`d-none`);
  $modalGraveSiteTitle.html(`Участки (создание)`);
  $modalGraveSiteCemeteryBlockIdField.val(null).change();
  $modalGraveSiteRowInBlockField.val(null);
  $modalGraveSitePositionInRowField.val(null);
  $modalGraveSiteSizeField.val(null);
  $modalGraveSiteGeoPositionField.val(null);
  $modalGraveSiteGeoPositionErrorField.val(null);
  hideAllValidationErrorsGraveSite();
  modalGraveSiteObject.show();
});

// Edit
$tableGraveSite.on(`click`, `td`, (e) => {
  $spinner.show();
  modeGraveSite = `edit`;
  idGraveSite   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getShowActionUrlGraveSite(idGraveSite),
  })
  .done((responseJson) => {
    const view = responseJson.data.view;
    $modalGraveSite.data(`id`, idGraveSite);
    $modalGraveSite.removeClass(`edit-form`).addClass(`edit-form`);
    $modalGraveSiteRemoveBtnWrapper.removeClass(`d-none`);
    $modalGraveSiteSaveBtn.removeClass(`d-none`);
    $modalGraveSiteTimestamps.removeClass(`d-none`);
    let modalGraveSiteTitle = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      modalGraveSiteTitle += `, место ${view.positionInRow}`;
    }
    $modalGraveSiteTitle.html(`<span>${modalGraveSiteTitle}</span> (Участки)`);
    $modalGraveSiteCemeteryBlockIdField.val(view.cemeteryBlockId).change();
    $modalGraveSiteRowInBlockField.val(view.rowInBlock);
    $modalGraveSitePositionInRowField.val(view.positionInRow);
    $modalGraveSiteGeoPositionField.val(
        view.geoPositionLatitude !== null || view.geoPositionLongitude !== null
          ? [view.geoPositionLatitude, view.geoPositionLongitude].join(`, `)
          : ``
    );
    $modalGraveSiteGeoPositionErrorField.val(view.geoPositionError);
    $modalGraveSiteSizeField.val(view.size);
    hideAllValidationErrorsGraveSite();
    modalGraveSiteObject.show();
  })
  .fail(onAjaxFailureGraveSite)
  .always(onAjaxAlways);
});
$modalGraveSiteGeoPositionField.on(`change`, () => {
  $modalGraveSiteGeoPositionErrorField.val(``);
});

// Autofocus
$(document).ready(() => {
  $(`#modalGraveSite`).on(`shown.bs.modal`, (e) => {
    const $modalGraveSite = $(e.target);
    $modalGraveSite.find(`#cemeteryBlockId`).focus();
  });
});

$modalGraveSite.on(`click`, `.js-save`, () => {
  saveGraveSite(getSaveActionUrlGraveSite());
});
$modalGraveSite.on(`click`, `.js-save-and-close`, () => {
  saveGraveSite(getSaveActionUrlGraveSite(), true);
});
$modalGraveSite.on(`click`, `.js-close`, () => {
  closeGraveSite();
});
$modalGraveSite.on(`click`, `.js-remove`, () => {
  const name = $modalGraveSiteTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить участок "${name}"?`,
    icon: `warning`,
    iconColor: `red`,
    showCancelButton: true,
    focusCancel: true,
    confirmButtonText: `Да, удалить`,
    confirmButtonColor: `red`,
    cancelButtonText: `Нет`,
  })
  .then((result) => {
    if (result.isConfirmed) {
      removeGraveSite(getRemoveActionUrlGraveSite());
    }
  });
});

function saveGraveSite(url, isReloadRequired = false)
{
  $spinner.show();
  const method               = modeGraveSite === `new` ? `POST` : `PUT`;
  const geoPositionLatitude  = $modalGraveSiteGeoPositionField.val().split(`,`)[0];
  const geoPositionLongitude = $modalGraveSiteGeoPositionField.val().split(`,`)[1];
  console.log();
  const data                 = {
    cemeteryBlockId: $modalGraveSiteCemeteryBlockIdField.val() !== ``
        ? $modalGraveSiteCemeteryBlockIdField.val()
        : null,
    rowInBlock: $modalGraveSiteRowInBlockField.val() !== ``
        ? parseInt($modalGraveSiteRowInBlockField.val())
        : null,
    positionInRow: $modalGraveSitePositionInRowField.val() !== ``
        ? parseInt($modalGraveSitePositionInRowField.val())
        : null,
    geoPositionLatitude: $modalGraveSiteGeoPositionField.val() !== ``
        ? geoPositionLatitude !== undefined ? geoPositionLatitude.trim() : null
        : null,
    geoPositionLongitude: $modalGraveSiteGeoPositionField.val() !== ``
        ? geoPositionLongitude !== undefined ? geoPositionLongitude.trim() : null
        : null,
    geoPositionError: $modalGraveSiteGeoPositionErrorField.val() !== ``
        ? $modalGraveSiteGeoPositionErrorField.val()
        : null,
    size: $modalGraveSiteSizeField.val() !== ``
        ? $modalGraveSiteSizeField.val()
        : null,
    token: $modalGraveSiteCsrfTokenField.val(),
  };
  $.ajax({
    dataType: `json`,
    method: method,
    url: url,
    data: JSON.stringify(data),
    contentType: `application/json; charset=utf-8`,
  })
  .done(() => {
    buildToast().fire({
      icon: `success`,
      title: `Участок успешно ${modeGraveSite === `new` ? `создан` : `отредактирован`}.`,
    });
    if (isReloadRequired) {
      modalGraveSiteObject.hide();
      location.reload();      // TODO refactor not to reload entire page
    }
  })
  .fail(onAjaxFailureGraveSite)
  .always(onAjaxAlways);
}
function removeGraveSite(url)
{
  $spinner.show();
  const data = {
    token: $modalGraveSiteCsrfTokenField.val(),
  };
  $.ajax({
    dataType: `json`,
    method: `DELETE`,
    url: url,
    data: JSON.stringify(data),
  })
  .done(() => {
      buildToast().fire({
      icon: `success`,
      title: `Участок успешно удалён.`,
    });
    modalGraveSiteObject.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailureGraveSite)
  .always(onAjaxAlways);
}
function closeGraveSite()
{
  modalGraveSiteObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function getSaveActionUrlGraveSite()
{
  let url = null;

  if (modeGraveSite === `new`) {
    url = getNewActionUrlGraveSite();
  }
  if (modeGraveSite === `edit`) {
    const idGraveSite = $modalGraveSite.data(`id`);
    url               = getEditActionUrlGraveSite(idGraveSite);
  }

  if (url === null) {
    throw `Режим сохранения не задан!`;
  }

  return url;
}
function getRemoveActionUrlGraveSite()
{
  return $modalGraveSiteForm.data(`action-remove`).replace(`{id}`, idGraveSite);
}
function getNewActionUrlGraveSite()
{
  return $modalGraveSiteForm.data(`action-new`);
}
function getShowActionUrlGraveSite(idGraveSite)
{
  return $modalGraveSiteForm.data(`action-show`).replace(`{id}`, idGraveSite);
}
function getEditActionUrlGraveSite(idGraveSite)
{
  return $modalGraveSiteForm.data(`action-edit`).replace(`{id}`, idGraveSite);
}


// ------------------------------------------------- Validation errors -------------------------------------------------
// TODO refactor to extract to common file
function displayValidationErrorsGraveSite(data)
{
  for (const [fieldId, validationError] of Object.entries(data)) {
    const $field = $modalGraveSiteForm.find(`#${fieldId}`);
    if ($field.length === 0) {
      buildToast().fire({
        icon: `error`,
        title: validationError,
      });
      continue;
    }

    $field.removeClass(`is-invalid`).addClass(`is-invalid`);
    const ariaDescribedby  = $field.attr(`aria-describedby`);
    const $invalidFeedback = $modalGraveSiteForm.find(`#${ariaDescribedby}`);
    $invalidFeedback.html(validationError);
    $invalidFeedback.removeClass(`d-none`);
  }
}
function hideAllValidationErrorsGraveSite()
{
  $modalGraveSiteForm.find(`.is-invalid`).removeClass(`is-invalid`);
}
$modalGraveSiteForm.on(`change`, `.is-invalid`, (e) => {
  removeValidationErrorGraveSite(e);
});
$modalGraveSiteForm.on(`input`, `.is-invalid`, (e) => {
  removeValidationErrorGraveSite(e);
});
function removeValidationErrorGraveSite(e)
{
  const $field = $(e.target);
  $field.removeClass(`is-invalid`);
}


// ----------------------------------------- Application services ------------------------------------------------------
function onAjaxFailureGraveSite(jqXHR) {
  if (jqXHR.responseText === undefined) {
    buildToast().fire({
      icon: `error`,
      title: `Сервер не отвечает.`,
    });
    return;
  }
  const responseJson = JSON.parse(jqXHR.responseText);
  switch (responseJson.status) {
    case `fail`:
      processApplicationFailResponseGraveSite(responseJson);
      break;
    case `error`:
      processApplicationErrorResponseGraveSite(responseJson);
      break;
    default:
      throw `Неподдерживаемый статус ответа прикладного сервиса: "${responseJson.status}".`;
  }
}

function processApplicationFailResponseGraveSite(responseJson) {
  const failType = responseJson.data.failType;
  switch (failType) {
    case `VALIDATION_ERROR`:
      delete responseJson.data.failType;
      displayValidationErrorsGraveSite(responseJson.data)
      break;
    case `NOT_FOUND`:
    case `DOMAIN_EXCEPTION`:
      buildToast().fire({
        icon: `warning`,
        title: responseJson.data.message,
      })
      break;
    default:
      throw `Неподдерживаемый тип отказа выполнения запроса прикладного сервиса: "${failType}".`;
  }
}

function processApplicationErrorResponseGraveSite(responseJson) {
  buildToast().fire({
    icon: `error`,
    title: responseJson.message,
  })
}
