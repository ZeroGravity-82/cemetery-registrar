const $graveSiteFormNew                       = $(`#modalGraveSiteFormNew`);
const $graveSiteFormNewTitle                  = $graveSiteFormNew.find(`.modal-title`)
const $graveSiteFormNewForm                   = $graveSiteFormNew.find(`form`);
const $graveSiteFormNewCemeteryBlockIdField   = $graveSiteFormNew.find(`select[id=cemeteryBlockId]`);
const $graveSiteFormNewRowInBlockField        = $graveSiteFormNew.find(`input[id=rowInBlock]`);
const $graveSiteFormNewPositionInRowField     = $graveSiteFormNew.find(`input[id=positionInRow]`);
const $graveSiteFormNewSizeField              = $graveSiteFormNew.find(`input[id=size]`);
const $graveSiteFormNewGeoPositionField       = $graveSiteFormNew.find(`input[id=geoPosition]`);
const $graveSiteFormNewGeoPositionErrorField  = $graveSiteFormNew.find(`input[id=geoPositionError]`);
const $graveSiteFormNewPersonInChargeField    = $graveSiteFormNew.find(`select[id=personInCharge]`);
const $graveSiteFormNewCsrfTokenField         = $graveSiteFormNew.find(`input[id=token]`);
const $graveSiteFormNewSaveAndCloseBtn        = $graveSiteFormNew.find(`.js-save-and-close`);
const $graveSiteFormNewCloseBtn               = $graveSiteFormNew.find(`.js-close`);
const graveSiteFormNewModalObject             = new bootstrap.Modal(`#modalGraveSiteFormNew`, {});

function graveSiteFormNew_show() {
  $graveSiteFormNewTitle.html(`Участки (создание)`);
  $graveSiteFormNewCemeteryBlockIdField.val(null).change();
  $graveSiteFormNewRowInBlockField.val(null);
  $graveSiteFormNewPositionInRowField.val(null);
  $graveSiteFormNewSizeField.val(null);
  $graveSiteFormNewGeoPositionField.val(null);
  $graveSiteFormNewGeoPositionErrorField.val(null);
  graveSiteFormNew_hideAllValidationErrors();
  graveSiteFormNewModalObject.show();
}

// Autofocus
$graveSiteFormNew.on(`shown.bs.modal`, (e) => {
  $(e.target).find(`#cemeteryBlockId`).focus();
});

// Person in charge selector
$graveSiteFormNewPersonInChargeField.selectize({
  valueField: `id`,
  labelField: `fullName`,
  searchField: `fullName`,
  placeholder: `Введите ФИО ответственного...`,
  load: (query, callback) => {
    if (query.length < 3) return callback();
    $.ajax({
      dataType: `json`,
      method: `GET`,
      url: `http://localhost:8082/natural-person/list-alive?search=${query}`,
    })
    .done((res) => {
      callback(res.data.simpleList.items.slice(0, 10));
    })
    .fail(() => {
      callback();
    })
  },
  render: {
    option: (item, escape) =>
`<div>
   <span class="natural-person-name">${escape(item.fullName)}</span>&nbsp;
   <span class="text-secondary fs-6">
     ${item.bornAt ? `<span class="natural-person-born-at"><i class="bi-balloon"></i>${escape(item.bornAt)}</span>` : ``} 
   </span>
</div>`,
  },
});

$graveSiteFormNewSaveAndCloseBtn.on(`click`, () => {
  const url = $graveSiteFormNewForm.data(`action-new`);
  graveSiteFormNew_save(url, true);
});
$graveSiteFormNewCloseBtn.on(`click`, () => {
  graveSiteForm_close();
});
$graveSiteFormNewGeoPositionField.on(`change`, () => {
  $graveSiteFormNewGeoPositionErrorField.val(``);
});

function graveSiteFormNew_save(url, isReloadRequired = false) {
  $spinner.show();
  const geoPositionLatitude  = $graveSiteFormNewGeoPositionField.val().split(`,`)[0];
  const geoPositionLongitude = $graveSiteFormNewGeoPositionField.val().split(`,`)[1];
  const data                 = {
    cemeteryBlockId: $graveSiteFormNewCemeteryBlockIdField.val() !== ``
        ? $graveSiteFormNewCemeteryBlockIdField.val()
        : null,
    rowInBlock: $graveSiteFormNewRowInBlockField.val() !== ``
        ? parseInt($graveSiteFormNewRowInBlockField.val())
        : null,
    positionInRow: $graveSiteFormNewPositionInRowField.val() !== ``
        ? parseInt($graveSiteFormNewPositionInRowField.val())
        : null,
    geoPositionLatitude: $graveSiteFormNewGeoPositionField.val() !== ``
        ? geoPositionLatitude !== undefined ? geoPositionLatitude.trim() : null
        : null,
    geoPositionLongitude: $graveSiteFormNewGeoPositionField.val() !== ``
        ? geoPositionLongitude !== undefined ? geoPositionLongitude.trim() : null
        : null,
    geoPositionError: $graveSiteFormNewGeoPositionErrorField.val() !== ``
        ? $graveSiteFormNewGeoPositionErrorField.val()
        : null,
    size: $graveSiteFormNewSizeField.val() !== ``
        ? $graveSiteFormNewSizeField.val()
        : null,
    token: $graveSiteFormNewCsrfTokenField.val(),
  };
  $.ajax({
    dataType: `json`,
    method: `POST`,
    url: url,
    data: JSON.stringify(data),
    contentType: `application/json; charset=utf-8`,
  })
  .done(() => {
    buildToast().fire({
      icon: `success`,
      title: `Участок успешно создан.`,
    });
    if (isReloadRequired) {
      graveSiteFormNewModalObject.hide();
      location.reload();      // TODO refactor not to reload entire page
    }
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function graveSiteForm_close() {
  graveSiteFormNewModalObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function graveSiteFormNew_hideAllValidationErrors() {
  $graveSiteFormNewForm.find(`.is-invalid`).removeClass(`is-invalid`);
}

$graveSiteFormNewForm.on(`change`, `.is-invalid`, (e) => {
  graveSiteFormNew_removeValidationError(e);
});
$graveSiteFormNewForm.on(`input`, `.is-invalid`, (e) => {
  graveSiteFormNew_removeValidationError(e);
});
function graveSiteFormNew_removeValidationError(e) {
  $(e.target).removeClass(`is-invalid`);
}
