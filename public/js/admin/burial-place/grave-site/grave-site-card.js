const $graveSiteCard                          = $(`#modalGraveSiteCard`);
const $graveSiteFormClarifyLocation           = $(`#modalGraveSiteFormClarifyLocation`);
const $graveSiteCardTitle                     = $graveSiteCard.find(`.modal-title`)
const $graveSiteCardCard                      = $graveSiteCard.find(`div.card`);
const $graveSiteCardLocationField             = $graveSiteCard.find(`.js-location`);
const $graveSiteCardSizeField                 = $graveSiteCard.find(`.js-size`);
const $graveSiteCardGeoPositionField          = $graveSiteCard.find(`.js-geo-position`);
const $graveSiteCardPersonInChargeField       = $graveSiteCard.find(`.js-person-in-charge`);
const $graveSiteCardCsrfTokenField            = $graveSiteCard.find(`input[id=token]`);
const $graveSiteCardCloseBtn                  = $graveSiteCard.find(`.js-close`);
const graveSiteCardModalObject                = new bootstrap.Modal(`#modalGraveSiteCard`, {});
const graveSiteFormClarifyLocationModalObject = new bootstrap.Modal(`#modalGraveSiteFormClarifyLocation`, {});

let graveSiteId = null;

function graveSiteCard_show(id) {
  $spinner.show();
  graveSiteId = id;
  const url   = $graveSiteCardCard.data(`action-show`).replace(`{id}`, graveSiteId);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: url,
  })
  .done((responseJson) => {
    const view = responseJson.data.view;
    $graveSiteCardCard.data(`id`, graveSiteId);
    let graveSiteCardTitle = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      graveSiteCardTitle += `, место ${view.positionInRow}`;
    }
    $graveSiteCardTitle.html(`<span>${graveSiteCardTitle}</span> (Участки)`);
    $graveSiteCardLocationField.html(graveSiteCardTitle);
    $graveSiteCardSizeField.html(view.size !== null ? `${view.size} м²` : `-`);

    let geoPosition = view.geoPositionLatitude !== null || view.geoPositionLongitude !== null
            ? [view.geoPositionLatitude, view.geoPositionLongitude].join(`, `)
            : null;
    if (geoPosition !== null && view.geoPositionError !== null) {
      geoPosition += ` (± ${view.geoPositionError} м)`;
    }
    $graveSiteCardGeoPositionField.html(geoPosition !== null ? geoPosition : `-`);
    $graveSiteCardPersonInChargeField.html(view.personInChargeFullName !== null ? view.personInChargeFullName : `-`);
    graveSiteCardModalObject.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

$graveSiteCardCloseBtn.on(`click`, () => {
  graveSiteCard_close();
});

$graveSiteCard.on(`click`, `.js-clarify-location`, () => {
  graveSiteCardModalObject.hide();
  graveSiteFormClarifyLocationModalObject.show();
});

$graveSiteCard.on(`click`, `.js-remove`, () => {
  const name = $graveSiteCardTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить участок<br>"${name}"?`,
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
      const url = $graveSiteCardCard.data(`action-remove`).replace(`{id}`, graveSiteId);
      removeGraveSite(url);
    }
  })
});

function removeGraveSite(url) {
  $spinner.show();
  const data = {
    token: $graveSiteCardCsrfTokenField.val(),
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
    graveSiteCardModalObject.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function graveSiteCard_close() {
  graveSiteCardModalObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}
