const $tableCemeteryBlock                 = $(`#cemeteryBlockList`);
const $modalCemeteryBlock                 = $(`#modalCemeteryBlock`);
const $modalCemeteryBlockTitle            = $modalCemeteryBlock.find(`.modal-title`)
const $modalCemeteryBlockForm             = $modalCemeteryBlock.find(`form`);
const $modalCemeteryBlockNameField        = $modalCemeteryBlock.find(`input[id=name]`);
const $modalCemeteryBlockCsrfTokenField   = $modalCemeteryBlock.find(`input[id=token]`);
const $modalCemeteryBlockRemoveBtnWrapper = $modalCemeteryBlock.find(`.js-remove-wrapper`);
const $modalCemeteryBlockSaveBtn          = $modalCemeteryBlock.find(`.js-save`);
const $modalCemeteryBlockTimestamps       = $modalCemeteryBlock.find(`.timestamps`);
const modalCemeteryBlockObject            = new bootstrap.Modal(`#modalCemeteryBlock`, {});

let modeCemeteryBlock = null;
let idCemeteryBlock   = null;

// Create
$body.on(`click`, `.js-create-cemetery-block-btn`, () => {
  modeCemeteryBlock = `new`;
  idCemeteryBlock   = null;
  $modalCemeteryBlock.data(`id`, idCemeteryBlock);
  $modalCemeteryBlock.removeClass(`edit-form`);
  $modalCemeteryBlockRemoveBtnWrapper.removeClass(`d-none`).addClass(`d-none`);
  $modalCemeteryBlockSaveBtn.removeClass(`d-none`).addClass(`d-none`);
  $modalCemeteryBlockTimestamps.removeClass(`d-none`).addClass(`d-none`);
  $modalCemeteryBlockTitle.html(`Кварталы (создание)`);
  $modalCemeteryBlockNameField.val(null);
  hideAllValidationErrorsCemeteryBlock();
  modalCemeteryBlockObject.show();
});

// Edit
$tableCemeteryBlock.on(`click`, `td`, (e) => {
  $spinner.show();
  modeCemeteryBlock = `edit`;
  idCemeteryBlock   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getShowActionUrlCemeteryBlock(idCemeteryBlock),
  })
  .done((responseJson) => {
    const view = responseJson.data.view;
    $modalCemeteryBlock.data(`id`, idCemeteryBlock);
    $modalCemeteryBlock.removeClass(`edit-form`).addClass(`edit-form`);
    $modalCemeteryBlockRemoveBtnWrapper.removeClass(`d-none`);
    $modalCemeteryBlockSaveBtn.removeClass(`d-none`);
    $modalCemeteryBlockTimestamps.removeClass(`d-none`);
    $modalCemeteryBlockTitle.html(`<span>${view.name}</span> (Кварталы)`);
    $modalCemeteryBlockNameField.val(view.name);
    hideAllValidationErrorsCemeteryBlock();
    modalCemeteryBlockObject.show();
  })
  .fail(onAjaxFailureCemeteryBlock)
  .always(onAjaxAlways);
});

// Autofocus
$(document).ready(() => {
  $(`#modalCemeteryBlock`).on(`shown.bs.modal`, (e) => {
    const $modalCemeteryBlock = $(e.target);
    $modalCemeteryBlock.find(`#name`).select();
  });
});

$modalCemeteryBlock.on(`click`, `.js-save`, () => {
  saveCemeteryBlock(getSaveActionUrlCemeteryBlock());
});
$modalCemeteryBlock.on(`click`, `.js-save-and-close`, () => {
  saveCemeteryBlock(getSaveActionUrlCemeteryBlock(), true);
});
$modalCemeteryBlock.on(`click`, `.js-close`, () => {
  closeCemeteryBlock();
});
$modalCemeteryBlock.on(`click`, `.js-remove`, () => {
  const name = $modalCemeteryBlockTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить квартал<br>"${name}"?`,
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
      removeCemeteryBlock(getRemoveActionUrlCemeteryBlock());
    }
  });
});

function saveCemeteryBlock(url, isReloadRequired = false)
{
  $spinner.show();
  const method = modeCemeteryBlock === `new` ? `POST` : `PUT`;
  const data   = {
    name: $modalCemeteryBlockNameField.val(),
    token: $modalCemeteryBlockCsrfTokenField.val(),
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
      title: `Квартал успешно ${modeCemeteryBlock === `new` ? `создан` : `отредактирован`}.`,
    });
    if (isReloadRequired) {
      modalCemeteryBlockObject.hide();
      location.reload();      // TODO refactor not to reload entire page
    }
  })
  .fail(onAjaxFailureCemeteryBlock)
  .always(onAjaxAlways);
}
function removeCemeteryBlock(url)
{
  $spinner.show();
  const data = {
    token: $modalCemeteryBlockCsrfTokenField.val(),
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
      title: `Квартал успешно удалён.`,
    });
    modalCemeteryBlockObject.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailureCemeteryBlock)
  .always(onAjaxAlways);
}
function closeCemeteryBlock()
{
  modalCemeteryBlockObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function getSaveActionUrlCemeteryBlock()
{
  let url = null;

  if (modeCemeteryBlock === `new`) {
    url = getNewActionUrlCemeteryBlock();
  }
  if (modeCemeteryBlock === `edit`) {
    const idCemeteryBlock = $modalCemeteryBlock.data(`id`);
    url                   = getEditActionUrlCemeteryBlock(idCemeteryBlock);
  }

  if (url === null) {
    throw `Режим сохранения не задан!`;
  }

  return url;
}
function getRemoveActionUrlCemeteryBlock()
{
  return $modalCemeteryBlockForm.data(`action-remove`).replace(`{id}`, idCemeteryBlock);
}
function getNewActionUrlCemeteryBlock()
{
  return $modalCemeteryBlockForm.data(`action-new`);
}
function getShowActionUrlCemeteryBlock(idCemeteryBlock)
{
  return $modalCemeteryBlockForm.data(`action-show`).replace(`{id}`, idCemeteryBlock);
}
function getEditActionUrlCemeteryBlock(idCemeteryBlock)
{
  return $modalCemeteryBlockForm.data(`action-edit`).replace(`{id}`, idCemeteryBlock);
}


// ------------------------------------------------- Validation errors -------------------------------------------------
// TODO refactor to extract to common file
function displayValidationErrorsCemeteryBlock(data)
{
  for (const [fieldId, validationError] of Object.entries(data)) {
    const $field = $modalCemeteryBlockForm.find(`#${fieldId}`);
    if ($field.length === 0) {
      buildToast().fire({
        icon: `error`,
        title: validationError,
      });
      continue;
    }

    $field.removeClass(`is-invalid`).addClass(`is-invalid`);
    const ariaDescribedby  = $field.attr(`aria-describedby`);
    const $invalidFeedback = $modalCemeteryBlockForm.find(`#${ariaDescribedby}`);
    $invalidFeedback.html(validationError);
    $invalidFeedback.removeClass(`d-none`);
  }
}
function hideAllValidationErrorsCemeteryBlock()
{
  $modalCemeteryBlockForm.find(`.is-invalid`).removeClass(`is-invalid`);
}
$modalCemeteryBlockForm.on(`change`, `.is-invalid`, (e) => {
  removeValidationErrorCemeteryBlock(e);
});
$modalCemeteryBlockForm.on(`input`, `.is-invalid`, (e) => {
  removeValidationErrorCemeteryBlock(e);
});
function removeValidationErrorCemeteryBlock(e)
{
  const $field = $(e.target);
  $field.removeClass(`is-invalid`);
}


// ----------------------------------------- Application services ------------------------------------------------------
function onAjaxFailureCemeteryBlock(jqXHR) {
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
      processApplicationFailResponseCemeteryBlock(responseJson);
      break;
    case `error`:
      processApplicationErrorResponseCemeteryBlock(responseJson);
      break;
    default:
      throw `Неподдерживаемый статус ответа прикладного сервиса: "${responseJson.status}".`;
  }
}

function processApplicationFailResponseCemeteryBlock(responseJson) {
  const failType = responseJson.data.failType;
  switch (failType) {
    case `VALIDATION_ERROR`:
      delete responseJson.data.failType;
      displayValidationErrorsCemeteryBlock(responseJson.data)
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

function processApplicationErrorResponseCemeteryBlock(responseJson) {
  buildToast().fire({
    icon: `error`,
    title: responseJson.message,
  })
}
