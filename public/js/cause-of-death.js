const $body                  = $(`body`);
const $spinner               = $(`#spinner-div`);
const $causeOfDeathTable     = $(`#causeOfDeathList`);
const $modalCauseOfDeath     = $(`#modalCauseOfDeath`);
const $modalTitle            = $modalCauseOfDeath.find(`.modal-title`)
const $modalCauseOfDeathForm = $(`#modalCauseOfDeath form`);
const $modalNameField        = $modalCauseOfDeath.find(`input[id=name]`);
const $modalCsrfTokenField   = $modalCauseOfDeath.find(`input[id=token]`);
const $modalRemoveBtnWrapper = $modalCauseOfDeath.find(`.js-remove-wrapper`);
const $modalTimestamps       = $modalCauseOfDeath.find(`.timestamps`);
const modalCauseOfDeath      = new bootstrap.Modal(`#modalCauseOfDeath`, {});

let mode = null;
let id   = null;

// Create
$body.on(`click`, `.js-create-cause-of-death-btn`, function() {
  mode = `new`;
  id   = null;
  $modalCauseOfDeath.data(`id`, id);
  $modalCauseOfDeath.removeClass(`edit-form`);
  $modalRemoveBtnWrapper.removeClass(`d-none`).addClass(`d-none`);
  $modalTimestamps.removeClass(`d-none`).addClass(`d-none`);
  $modalTitle.html(`Причины смерти (создание)`);
  $modalNameField.val(null);
  modalCauseOfDeath.show();
});

// Edit
$causeOfDeathTable.on(`click`, `td`, function(e) {
  $spinner.show();
  mode = `edit`;
  id   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getEditActionUrl(id),
  })
  .done(function (causeOfDeathView) {
    $modalCauseOfDeath.data(`id`, id);
    $modalCauseOfDeath.removeClass(`edit-form`).addClass(`edit-form`);
    $modalRemoveBtnWrapper.removeClass(`d-none`);
    $modalTimestamps.removeClass(`d-none`);
    $modalTitle.html(`<span id="causeOfDeathViewTitle">${causeOfDeathView.name}</span> (Причины смерти)`);
    $modalNameField.val(causeOfDeathView.name);
    modalCauseOfDeath.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
});

// Autofocus
$(document).ready(function () {
  $(`#modalCauseOfDeath`).on(`shown.bs.modal`, function () {
    $(this).find(`#name`).select();
  });
});

$modalCauseOfDeath.on(`click`, `.js-save`, function () {
  save(getSaveActionUrl());
});
$modalCauseOfDeath.on(`click`, `.js-save-and-close`, function () {
  save(getSaveActionUrl(), true);
});
$modalCauseOfDeath.on(`click`, `.js-close`, function () {
  close();
});
$modalCauseOfDeath.on(`click`, `.js-remove`, function () {
  const causeOfDeath = $(`#causeOfDeathViewTitle`).html();
  const isConfirmed  = confirm(`Удалить причину смерти "${causeOfDeath}"?`)
  if (isConfirmed) {
    remove(getRemoveActionUrl());
  }
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
  .done(function () {
    if (isReloadRequired) {
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
  .done(function () {
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}
function close()
{
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
    throw `The operation mode is not set!`;
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
function getEditActionUrl(id)
{
  return $modalCauseOfDeathForm.data(`action-edit`).replace(`{id}`, id);
}





// --------------------------- Common code section (not only for cause of death entity) --------------------------------
function onAjaxFailure(jqXHR)
{
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
function onAjaxAlways()
{
  $spinner.hide();
}
function processApplicationFailResponse(responseJson)
{
  // TODO refactor to get rid of dependency on sweetalert2
  const failType      = responseJson.data.failType;
  switch (failType) {
    case `VALIDATION_ERROR`:
      // TODO implement
      break;
    case `NOT_FOUND`:
    case `DOMAIN_EXCEPTION`:
      // notify(`warning`, `Запрос не выполнен!`, responseJson.data.message);
      buildToast().fire({
        icon: `warning`,
        title: responseJson.data.message,
      })
      break;
    default:
      throw `Неподдерживаемый тип отказа выполнения запроса прикладного сервиса: "${failType}".`;
  }
}
function processApplicationErrorResponse(responseJson)
{
  // notify(`error`, `Ошибка!`, responseJson.message);
  buildToast().fire({
    icon: `error`,
    title: responseJson.message,
  })
}
function buildToast()
{
  return Swal.mixin({
    toast: true,
    position: `top-end`,
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener(`mouseenter`, Swal.stopTimer)
      toast.addEventListener(`mouseleave`, Swal.resumeTimer)
    }
  })
}
// ----------------------- End of common code section (not only for cause of death entity) -----------------------------