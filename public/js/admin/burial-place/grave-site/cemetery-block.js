const $table                 = $(`#cemeteryBlockList`);
const $modal                 = $(`#modalCemeteryBlock`);
const $modalTitle            = $modal.find(`.modal-title`)
const $modalForm             = $(`#modalCemeteryBlock form`);
const $modalNameField        = $modal.find(`input[id=name]`);
const $modalCsrfTokenField   = $modal.find(`input[id=token]`);
const $modalRemoveBtnWrapper = $modal.find(`.js-remove-wrapper`);
const $modalSaveBtn          = $modal.find(`.js-save`);
const $modalTimestamps       = $modal.find(`.timestamps`);
const modalObject            = new bootstrap.Modal(`#modalCemeteryBlock`, {});

let mode = null;
let id   = null;

// Create
$body.on(`click`, `.js-create-cemetery-block-btn`, () => {
  mode = `new`;
  id   = null;
  $modal.data(`id`, id);
  $modal.removeClass(`edit-form`);
  $modalRemoveBtnWrapper.removeClass(`d-none`).addClass(`d-none`);
  $modalSaveBtn.removeClass(`d-none`).addClass(`d-none`);
  $modalTimestamps.removeClass(`d-none`).addClass(`d-none`);
  $modalTitle.html(`Кварталы (создание)`);
  $modalNameField.val(null);
  hideAllValidationErrors();
  modalObject.show();
});

// Edit
$table.on(`click`, `td`, (e) => {
  $spinner.show();
  mode = `edit`;
  id   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getShowActionUrl(id),
  })
  .done((responseJson) => {
    const view = responseJson.data.view;
    $modal.data(`id`, id);
    $modal.removeClass(`edit-form`).addClass(`edit-form`);
    $modalRemoveBtnWrapper.removeClass(`d-none`);
    $modalSaveBtn.removeClass(`d-none`);
    $modalTimestamps.removeClass(`d-none`);
    $modalTitle.html(`<span>${view.name}</span> (Кварталы)`);
    $modalNameField.val(view.name);
    hideAllValidationErrors();
    modalObject.show();
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

$modal.on(`click`, `.js-save`, () => {
  save(getSaveActionUrl());
});
$modal.on(`click`, `.js-save-and-close`, () => {
  save(getSaveActionUrl(), true);
});
$modal.on(`click`, `.js-close`, () => {
  close();
});
$modal.on(`click`, `.js-remove`, () => {
  const name = $modalTitle.find(`span`).html();
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
      modalObject.hide();
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
    modalObject.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}
function close()
{
  modalObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function getSaveActionUrl()
{
  let url = null;

  if (mode === `new`) {
    url = getNewActionUrl();
  }
  if (mode === `edit`) {
    const id = $modal.data(`id`);
    url      = getEditActionUrl(id);
  }

  if (url === null) {
    throw `Режим сохранения не задан!`;
  }

  return url;
}
function getRemoveActionUrl()
{
  return $modalForm.data(`action-remove`).replace(`{id}`, id);
}
function getNewActionUrl()
{
  return $modalForm.data(`action-new`);
}
function getShowActionUrl(id)
{
  return $modalForm.data(`action-show`).replace(`{id}`, id);
}
function getEditActionUrl(id)
{
  return $modalForm.data(`action-edit`).replace(`{id}`, id);
}


// ------------------------------------------------- Validation errors -------------------------------------------------
// TODO refactor to extract to common file
function displayValidationErrors(data)
{
  for (const [fieldId, validationError] of Object.entries(data)) {
    const $field = $modalForm.find(`#${fieldId}`);
    if ($field.length === 0) {
      buildToast().fire({
        icon: `error`,
        title: validationError,
      });
      continue;
    }

    $field.removeClass(`is-invalid`).addClass(`is-invalid`);
    const ariaDescribedby  = $field.attr(`aria-describedby`);
    const $invalidFeedback = $modalForm.find(`#${ariaDescribedby}`);
    $invalidFeedback.html(validationError);
    $invalidFeedback.removeClass(`d-none`);
  }
}
function hideAllValidationErrors()
{
  $modalForm.find(`.is-invalid`).removeClass(`is-invalid`);
}
$modalForm.on(`change`, `.is-invalid`, (e) => {
  removeValidationError(e);
});
$modalForm.on(`input`, `.is-invalid`, (e) => {
  removeValidationError(e);
});
function removeValidationError(e)
{
  const $field = $(e.target);
  $field.removeClass(`is-invalid`);
}
