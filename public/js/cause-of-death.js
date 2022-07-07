const $body                  = $(`body`);
const $modalCauseOfDeath     = $(`#modalCauseOfDeath`);
const $modalTitle            = $modalCauseOfDeath.find(`.modal-title`)
const $modalCauseOfDeathForm = $(`#modalCauseOfDeath form`);
const $modalNameField        = $modalCauseOfDeath.find(`input[id=name]`);
const $modalTimestamps       = $modalCauseOfDeath.find(`.timestamps`);
const modalCauseOfDeath      = new bootstrap.Modal(`#modalCauseOfDeath`, {});

let mode = null;
let id   = null;

// New
$body.on(`click`, `.js-create-cause-of-death-btn`, function() {
  mode = `new`;
  id   = null;
  $modalCauseOfDeath.data(`id`, id);
  $modalCauseOfDeath.removeClass(`edit-form`);
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
    type: `GET`,
    url: getEditActionUrl(id),
    success: function (causeOfDeathView) {
      $modalCauseOfDeath.data(`id`, id);
      $modalCauseOfDeath.removeClass(`edit-form`).addClass(`edit-form`);
      $modalTimestamps.removeClass(`d-none`);
      $modalTitle.html(`${causeOfDeathView.name} (Причины смерти)`);
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
  save(getActionUrl());
});
$modalCauseOfDeath.on(`click`, `.js-save-and-close`, function () {
  save(getActionUrl());
  close();
});
$modalCauseOfDeath.on(`click`, `.js-close`, function () {
  close();
});

function save(url)
{
  $.ajax({
    type: `POST`,
    url: url,
    data: $modalCauseOfDeathForm.serialize(),
    success: function () {
    },
  });
}
function close()
{
  modalCauseOfDeath.hide();
}

function getActionUrl()
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

function getNewActionUrl()
{
  return $modalCauseOfDeathForm.data(`action-new`);
}

function getEditActionUrl(id)
{
  return $modalCauseOfDeathForm.data(`action-edit`).replace(`{id}`, id);
}
