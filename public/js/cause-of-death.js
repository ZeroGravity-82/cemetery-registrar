const $body                  = $(`body`);
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
$body.on(`click`, `tr`, function(e) {
  mode = `edit`;
  id   = $(e.target).closest(`tr`).attr(`data-id`);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: getEditActionUrl(id),
    success: function (causeOfDeathView) {
      $modalCauseOfDeath.data(`id`, id);
      $modalCauseOfDeath.removeClass(`edit-form`).addClass(`edit-form`);
      $modalRemoveBtnWrapper.removeClass(`d-none`);
      $modalTimestamps.removeClass(`d-none`);
      $modalTitle.html(`<span id="causeOfDeathViewTitle">${causeOfDeathView.name}</span> (Причины смерти)`);
      $modalNameField.val(causeOfDeathView.name);
      modalCauseOfDeath.show();
    }
  });
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
    close();
  }
});

function save(url, isReloadRequired = false)
{
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
    success: function () {
      if (isReloadRequired) {
        location.reload();
      }
    },
  });
}
function remove(url)
{
  const data = {
    token: $modalCsrfTokenField.val(),
  };
  $.ajax({
    dataType: `json`,
    method: `DELETE`,
    url: url,
    data: JSON.stringify(data),
    success: function () {
    },
  });
}
function close()
{
  location.reload();  // TODO refactor to not reload page
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
