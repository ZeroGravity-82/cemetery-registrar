const $tableCauseOfDeath                 = $(`#causeOfDeathList`);
const $modalCauseOfDeath                 = $(`#modalCauseOfDeath`);
const $modalCauseOfDeathTitle            = $modalCauseOfDeath.find(`.modal-title`)
const $modalCauseOfDeathForm             = $modalCauseOfDeath.find(`form`);
const $modalCauseOfDeathNameField        = $modalCauseOfDeath.find(`input[id=name]`);
const $modalCauseOfDeathCsrfTokenField   = $modalCauseOfDeath.find(`input[id=token]`);
const $modalCauseOfDeathRemoveBtnWrapper = $modalCauseOfDeath.find(`.js-remove-wrapper`);
const $modalCauseOfDeathSaveBtn          = $modalCauseOfDeath.find(`.js-save`);
const $modalCauseOfDeathTimestamps       = $modalCauseOfDeath.find(`.timestamps`);
const modalCauseOfDeathObject            = new bootstrap.Modal(`#modalCauseOfDeath`, {});

let modeCauseOfDeath = null;
let idCauseOfDeath   = null;

// Create
$body.on(`click`, `.js-create-cause-of-death-btn`, () => {
  modeCauseOfDeath = `new`;
  idCauseOfDeath   = null;
  $modalCauseOfDeath.data(`id`, idCauseOfDeath);
  $modalCauseOfDeath.removeClass(`edit-form`);
  $modalCauseOfDeathRemoveBtnWrapper.removeClass(`d-none`).addClass(`d-none`);
  $modalCauseOfDeathSaveBtn.removeClass(`d-none`).addClass(`d-none`);
  $modalCauseOfDeathTimestamps.removeClass(`d-none`).addClass(`d-none`);
  $modalCauseOfDeathTitle.html(`Причины смерти (создание)`);
  $modalCauseOfDeathNameField.val(null);
  hideAllValidationErrors();
  modalCauseOfDeathObject.show();
});

// Edit
$tableCauseOfDeath.on(`click`, `td`, (e) => {
  $spinner.show();
  modeCauseOfDeath = `edit`;
  idCauseOfDeath   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getShowActionUrl(idCauseOfDeath),
  })
  .done((responseJson) => {
    const view = responseJson.data.view;
    $modalCauseOfDeath.data(`id`, idCauseOfDeath);
    $modalCauseOfDeath.removeClass(`edit-form`).addClass(`edit-form`);
    $modalCauseOfDeathRemoveBtnWrapper.removeClass(`d-none`);
    $modalCauseOfDeathSaveBtn.removeClass(`d-none`);
    $modalCauseOfDeathTimestamps.removeClass(`d-none`);
    $modalCauseOfDeathTitle.html(`<span>${view.name}</span> (Причины смерти)`);
    $modalCauseOfDeathNameField.val(view.name);
    hideAllValidationErrors();
    modalCauseOfDeathObject.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
});

// Autofocus
$(document).ready(() => {
  $(`#modalCauseOfDeath`).on(`shown.bs.modal`, (e) => {
    const $modalCauseOfDeath = $(e.target);
    $modalCauseOfDeath.find(`#name`).select();
  });
});

$modalCauseOfDeath.on(`click`, `.js-save`, () => {
  save(getSaveActionUrl());
});
$modalCauseOfDeath.on(`click`, `.js-save-and-close`, () => {
  save(getSaveActionUrl(), true);
});
$modalCauseOfDeath.on(`click`, `.js-close`, () => {
  close();
});
$modalCauseOfDeath.on(`click`, `.js-remove`, () => {
  const name = $modalCauseOfDeathTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить причину смерти "${name}"?`,
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
      remove(getRemoveActionUrl());
    }
  });
});

function save(url, isReloadRequired = false)
{
  $spinner.show();
  const method = modeCauseOfDeath === `new` ? `POST` : `PUT`;
  const data   = {
    name: $modalCauseOfDeathNameField.val(),
    token: $modalCauseOfDeathCsrfTokenField.val(),
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
      title: `Причина смерти успешно ${modeCauseOfDeath === `new` ? `создана` : `отредактирована`}.`,
    });
    if (isReloadRequired) {
      modalCauseOfDeathObject.hide();
      location.reload();      // TODO refactor not to reload entire page
    }
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}
function remove(url)
{
  $spinner.show();
  const data = {
    token: $modalCauseOfDeathCsrfTokenField.val(),
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
      title: `Причина смерти успешно удалена.`,
    });
    modalCauseOfDeathObject.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}
function close()
{
  modalCauseOfDeathObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function getSaveActionUrl()
{
  let url = null;

  if (modeCauseOfDeath === `new`) {
    url = getNewActionUrl();
  }
  if (modeCauseOfDeath === `edit`) {
    const idCauseOfDeath = $modalCauseOfDeath.data(`id`);
    url      = getEditActionUrl(idCauseOfDeath);
  }

  if (url === null) {
    throw `Режим сохранения не задан!`;
  }

  return url;
}
function getRemoveActionUrl()
{
  return $modalCauseOfDeathForm.data(`action-remove`).replace(`{id}`, idCauseOfDeath);
}
function getNewActionUrl()
{
  return $modalCauseOfDeathForm.data(`action-new`);
}
function getShowActionUrl(idCauseOfDeath)
{
  return $modalCauseOfDeathForm.data(`action-show`).replace(`{id}`, idCauseOfDeath);
}
function getEditActionUrl(idCauseOfDeath)
{
  return $modalCauseOfDeathForm.data(`action-edit`).replace(`{id}`, idCauseOfDeath);
}


// ------------------------------------------------- Validation errors -------------------------------------------------
// TODO refactor to extract to common file
function displayValidationErrors(data)
{
  for (const [fieldId, validationError] of Object.entries(data)) {
    const $field = $modalCauseOfDeathForm.find(`#${fieldId}`);
    if ($field.length === 0) {
      buildToast().fire({
        icon: `error`,
        title: validationError,
      });
      continue;
    }

    $field.removeClass(`is-invalid`).addClass(`is-invalid`);
    const ariaDescribedby  = $field.attr(`aria-describedby`);
    const $invalidFeedback = $modalCauseOfDeathForm.find(`#${ariaDescribedby}`);
    $invalidFeedback.html(validationError);
    $invalidFeedback.removeClass(`d-none`);
  }
}
function hideAllValidationErrors()
{
  $modalCauseOfDeathForm.find(`.is-invalid`).removeClass(`is-invalid`);
}
$modalCauseOfDeathForm.on(`change`, `.is-invalid`, (e) => {
  removeValidationError(e);
});
$modalCauseOfDeathForm.on(`input`, `.is-invalid`, (e) => {
  removeValidationError(e);
});
function removeValidationError(e)
{
  const $field = $(e.target);
  $field.removeClass(`is-invalid`);
}


// ----------------------------------------- Application services ------------------------------------------------------
function onAjaxFailure(jqXHR) {
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
      processApplicationFailResponse(responseJson);
      break;
    case `error`:
      processApplicationErrorResponse(responseJson);
      break;
    default:
      throw `Неподдерживаемый статус ответа прикладного сервиса: "${responseJson.status}".`;
  }
}

function processApplicationFailResponse(responseJson) {
  const failType = responseJson.data.failType;
  switch (failType) {
    case `VALIDATION_ERROR`:
      delete responseJson.data.failType;
      displayValidationErrors(responseJson.data)
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

function processApplicationErrorResponse(responseJson) {
  buildToast().fire({
    icon: `error`,
    title: responseJson.message,
  })
}
