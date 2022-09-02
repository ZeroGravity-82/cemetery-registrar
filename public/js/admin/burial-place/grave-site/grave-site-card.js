const $graveSiteCard                    = $(`#modalGraveSiteCard`);
const $graveSiteCardTitle               = $graveSiteCard.find(`.modal-title`)
const $graveSiteCardCard                = $graveSiteCard.find(`div.card`);
const $graveSiteCardLocationField       = $graveSiteCard.find(`.js-location`);
const $graveSiteCardSizeField           = $graveSiteCard.find(`.js-size`);
const $graveSiteCardGeoPositionField    = $graveSiteCard.find(`.js-geo-position`);
const $graveSiteCardPersonInChargeField = $graveSiteCard.find(`.js-person-in-charge`);
const $graveSiteCardCsrfTokenField      = $graveSiteCard.find(`input[id=token]`);
const $graveSiteCardCloseBtn            = $graveSiteCard.find(`.js-close`);
const graveSiteCardModalObject          = new bootstrap.Modal(`#modalGraveSiteCard`, {});

let currentGraveSiteId;
let currentView;

function graveSiteCard_show(id) {
  $spinner.show();

  currentGraveSiteId = id;
  $graveSiteCardCard.data(`id`, currentGraveSiteId);

  const url = $graveSiteCardCard.data(`action-show`).replace(`{id}`, currentGraveSiteId);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: url,
  })
  .done((responseJson) => {
    const view             = responseJson.data.view;
    currentView            = view;
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
    toggleActionButtons(view);
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
  graveSiteFormClarifyLocation_show(currentView, `graveSiteCard_show`, [currentGraveSiteId]);
});

$graveSiteCard.on(`click`, `.js-clarify-size`, () => {
  graveSiteCardModalObject.hide();
  graveSiteFormClarifySize_show(currentView, `graveSiteCard_show`, [currentGraveSiteId]);
});

$graveSiteCard.on(`click`, `.js-clarify-geo-position`, () => {
  graveSiteCardModalObject.hide();
  graveSiteFormClarifyGeoPosition_show(currentView, `graveSiteCard_show`, [currentGraveSiteId]);
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
      const url = $graveSiteCardCard.data(`action-remove`).replace(`{id}`, currentGraveSiteId);
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

function toggleActionButtons(view) {
  toggleActionBtnsForSize(view);
  toggleActionButtonsForGeoPosition(view);
  toggleActionButtonsForPersonInCharge(view);
  toggleActionButtonsDangerDivider();
}

function toggleActionBtnsForSize(view) {
  if (view.size === null) {
    $(`.js-clear-size`).removeClass(`d-none`).addClass(`d-none`);
  } else {
    $(`.js-clear-size`).removeClass(`d-none`);
  }
}

function toggleActionButtonsForGeoPosition(view) {
  if (view.geoPositionLatitude === null && view.geoPositionLongitude === null) {
    $(`.js-clear-geo-position`).removeClass(`d-none`).addClass(`d-none`);
  } else {
    $(`.js-clear-geo-position`).removeClass(`d-none`);
  }
}

function toggleActionButtonsForPersonInCharge(view) {
  if (view.personInChargeFullName === null) {
    $(`.js-assign-person-in-charge`).removeClass(`d-none`);
    $(`.js-clarify-person-in-charge`).removeClass(`d-none`).addClass(`d-none`);
    $(`.js-replace-person-in-charge`).removeClass(`d-none`).addClass(`d-none`);
    $(`.js-discard-person-in-charge`).removeClass(`d-none`).addClass(`d-none`);
  } else {
    $(`.js-assign-person-in-charge`).removeClass(`d-none`).addClass(`d-none`);
    $(`.js-clarify-person-in-charge`).removeClass(`d-none`);
    $(`.js-replace-person-in-charge`).removeClass(`d-none`);
    $(`.js-discard-person-in-charge`).removeClass(`d-none`);
  }
}

function toggleActionButtonsDangerDivider() {
  let areDangerBtnsVisible = false;
  const $dangerActionBtns = $graveSiteCard.find(`.js-danger-action-btn`);
  $.each($dangerActionBtns, function (index, dangerActionBtns) {
    if (!$(dangerActionBtns).hasClass(`d-none`)) {
      areDangerBtnsVisible = true;
    }
  });
  if (areDangerBtnsVisible) {
    $(`.js-danger-action-divider`).removeClass(`d-none`);
  } else {
    $(`.js-danger-action-divider`).removeClass(`d-none`).addClass(`d-none`);
  }
}
