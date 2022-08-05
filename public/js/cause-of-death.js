const $causeOfDeathTable     = $(`#causeOfDeathList`);
const $modalCauseOfDeath     = $(`#modalCauseOfDeath`);
const $modalTitle            = $modalCauseOfDeath.find(`.modal-title`)
const $modalCauseOfDeathForm = $(`#modalCauseOfDeath form`);
const $modalNameField        = $modalCauseOfDeath.find(`input[id=name]`);
const $modalCsrfTokenField   = $modalCauseOfDeath.find(`input[id=token]`);
const $modalRemoveBtnWrapper = $modalCauseOfDeath.find(`.js-remove-wrapper`);
const $modalSaveBtn          = $modalCauseOfDeath.find(`.js-save`);
const $modalTimestamps       = $modalCauseOfDeath.find(`.timestamps`);
const modalCauseOfDeath      = new bootstrap.Modal(`#modalCauseOfDeath`, {});

let mode = null;
let id   = null;

// Create
$body.on(`click`, `.js-create-cause-of-death-btn`, () => {
  mode = `new`;
  id   = null;
  $modalCauseOfDeath.data(`id`, id);
  $modalCauseOfDeath.removeClass(`edit-form`);
  $modalRemoveBtnWrapper.removeClass(`d-none`).addClass(`d-none`);
  $modalSaveBtn.removeClass(`d-none`).addClass(`d-none`);
  $modalTimestamps.removeClass(`d-none`).addClass(`d-none`);
  $modalTitle.html(`Причины смерти (создание)`);
  $modalNameField.val(null);
  hideAllValidationErrors();
  modalCauseOfDeath.show();
});

// Edit
$causeOfDeathTable.on(`click`, `td`, (e) => {
  $spinner.show();
  mode = `edit`;
  id   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getShowActionUrl(id),
  })
  .done((responseJson) => {
    const causeOfDeathView = responseJson.data.view;
    $modalCauseOfDeath.data(`id`, id);
    $modalCauseOfDeath.removeClass(`edit-form`).addClass(`edit-form`);
    $modalRemoveBtnWrapper.removeClass(`d-none`);
    $modalSaveBtn.removeClass(`d-none`);
    $modalTimestamps.removeClass(`d-none`);
    $modalTitle.html(`<span id="causeOfDeathViewTitle">${causeOfDeathView.name}</span> (Причины смерти)`);
    $modalNameField.val(causeOfDeathView.name);
    hideAllValidationErrors();
    modalCauseOfDeath.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
});

// Autofocus
$(document).ready(() => {
  $(`#modalCauseOfDeath`).on(`shown.bs.modal`, (e) => {
    const $modal = $(e.target);
    $modal.find(`#name`).select();
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
  const name = $(`#causeOfDeathViewTitle`).html();
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
      title: `Причина смерти успешно ${mode === `new` ? `создана` : `отредактирована`}.`,
    });
    if (isReloadRequired) {
      modalCauseOfDeath.hide();
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
      title: `Причина смерти успешно удалена.`,
    });
    modalCauseOfDeath.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}
function close()
{
  modalCauseOfDeath.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function getSaveActionUrl()
{
  let url = null;

  if (mode === `new`) {
    url = getNewActionUrl();
  }
  if (mode === `edit`) {
    const id = $modalCauseOfDeath.data(`id`);
    url      = getEditActionUrl(id);
  }

  if (url === null) {
    throw `Режим сохранения не задан!`;
  }

  return url;
}
function getRemoveActionUrl()
{
  return $modalCauseOfDeathForm.data(`action-remove`).replace(`{id}`, id);
}
function getNewActionUrl()
{
  return $modalCauseOfDeathForm.data(`action-new`);
}
function getShowActionUrl(id)
{
  return $modalCauseOfDeathForm.data(`action-show`).replace(`{id}`, id);
}
function getEditActionUrl(id)
{
  return $modalCauseOfDeathForm.data(`action-edit`).replace(`{id}`, id);
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
