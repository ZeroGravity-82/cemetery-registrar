const $cemeteryBlockTable     = $(`#cemeteryBlockList`);
const $modalCemeteryBlock     = $(`#modalCemeteryBlock`);
const $modalTitle             = $modalCemeteryBlock.find(`.modal-title`)
const $modalCemeteryBlockForm = $(`#modalCemeteryBlock form`);
const $modalNameField         = $modalCemeteryBlock.find(`input[id=name]`);
const $modalCsrfTokenField    = $modalCemeteryBlock.find(`input[id=token]`);
const $modalRemoveBtnWrapper  = $modalCemeteryBlock.find(`.js-remove-wrapper`);
const $modalSaveBtn           = $modalCemeteryBlock.find(`.js-save`);
const $modalTimestamps        = $modalCemeteryBlock.find(`.timestamps`);
const modalCemeteryBlock      = new bootstrap.Modal(`#modalCemeteryBlock`, {});

let mode = null;
let id   = null;

// Create
$body.on(`click`, `.js-create-cemetery-block-btn`, () => {
  mode = `new`;
  id   = null;
  $modalCemeteryBlock.data(`id`, id);
  $modalCemeteryBlock.removeClass(`edit-form`);
  $modalRemoveBtnWrapper.removeClass(`d-none`).addClass(`d-none`);
  $modalSaveBtn.removeClass(`d-none`).addClass(`d-none`);
  $modalTimestamps.removeClass(`d-none`).addClass(`d-none`);
  $modalTitle.html(`Кварталы (создание)`);
  $modalNameField.val(null);
  hideAllValidationErrors();
  modalCemeteryBlock.show();
});

// Edit
$cemeteryBlockTable.on(`click`, `td`, (e) => {
  $spinner.show();
  mode = `edit`;
  id   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getShowActionUrl(id),
  })
  .done((responseJson) => {
    const cemeteryBlockView = responseJson.data.view;
    $modalCemeteryBlock.data(`id`, id);
    $modalCemeteryBlock.removeClass(`edit-form`).addClass(`edit-form`);
    $modalRemoveBtnWrapper.removeClass(`d-none`);
    $modalSaveBtn.removeClass(`d-none`);
    $modalTimestamps.removeClass(`d-none`);
    $modalTitle.html(`<span id="cemeteryBlockViewTitle">${cemeteryBlockView.name}</span> (Кварталы)`);
    $modalNameField.val(cemeteryBlockView.name);
    hideAllValidationErrors();
    modalCemeteryBlock.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
});

// Autofocus
$(document).ready(() => {
  $(`#modalCemeteryBlock`).on(`shown.bs.modal`, (e) => {
    const $modal = $(e.target);
    $modal.find(`#name`).select();
  });
});

$modalCemeteryBlock.on(`click`, `.js-save`, () => {
  save(getSaveActionUrl());
});
$modalCemeteryBlock.on(`click`, `.js-save-and-close`, () => {
  save(getSaveActionUrl(), true);
});
$modalCemeteryBlock.on(`click`, `.js-close`, () => {
  close();
});
$modalCemeteryBlock.on(`click`, `.js-remove`, () => {
  const name = $(`#cemeteryBlockViewTitle`).html();
  Swal.fire({
    title: `Удалить квартал "${name}"?`,
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
  const method = mode === `new` ? `POST` : `PUT`;
  const data   = {
    name: $modalNameField.val(),
    token: $modalCsrfTokenField.val(),
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
      title: `Квартал успешно ${mode === `new` ? `создан` : `отредактирован`}.`,
    });
    if (isReloadRequired) {
      modalCemeteryBlock.hide();
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
    token: $modalCsrfTokenField.val(),
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
    modalCemeteryBlock.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}
function close()
{
  modalCemeteryBlock.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function getSaveActionUrl()
{
  let url = null;

  if (mode === `new`) {
    url = getNewActionUrl();
  }
  if (mode === `edit`) {
    const id = $modalCemeteryBlock.data(`id`);
    url      = getEditActionUrl(id);
  }

  if (url === null) {
    throw `Режим сохранения не задан!`;
  }

  return url;
}
function getRemoveActionUrl()
{
  return $modalCemeteryBlockForm.data(`action-remove`).replace(`{id}`, id);
}
function getNewActionUrl()
{
  return $modalCemeteryBlockForm.data(`action-new`);
}
function getShowActionUrl(id)
{
  return $modalCemeteryBlockForm.data(`action-show`).replace(`{id}`, id);
}
function getEditActionUrl(id)
{
  return $modalCemeteryBlockForm.data(`action-edit`).replace(`{id}`, id);
}


// ------------------------------------------------- Validation errors -------------------------------------------------
// TODO refactor to extract to common file
function displayValidationErrors(data)
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
function hideAllValidationErrors()
{
  $modalCemeteryBlockForm.find(`.is-invalid`).removeClass(`is-invalid`);
}
$modalCemeteryBlockForm.on(`change`, `.is-invalid`, (e) => {
  removeValidationError(e);
});
$modalCemeteryBlockForm.on(`input`, `.is-invalid`, (e) => {
  removeValidationError(e);
});
function removeValidationError(e)
{
  const $field = $(e.target);
  $field.removeClass(`is-invalid`);
}
