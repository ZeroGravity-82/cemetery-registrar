const $graveSiteCard                    = $(`#modalGraveSiteCard`);
const $graveSiteCardTitle               = $graveSiteCard.find(`.modal-title`)
const $graveSiteCardCard                = $graveSiteCard.find(`div.card`);
const $graveSiteCardLocationField       = $graveSiteCard.find(`.js-location`);
const $graveSiteCardSizeField           = $graveSiteCard.find(`.js-size`);
const $graveSiteCardGeoPositionField    = $graveSiteCard.find(`.js-geo-position`);
const $graveSiteCardPersonInChargeField = $graveSiteCard.find(`.js-person-in-charge`);
const $graveSiteCardCsrfTokenField      = $graveSiteCard.find(`input[id=token]`);
const $graveSiteCardCloseButton         = $graveSiteCard.find(`.js-close`);
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
    $graveSiteCardTitle.html(`Карточка участка - <span>${graveSiteCardTitle}</span>`);
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

$graveSiteCardCloseButton.on(`click`, () => {
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

$graveSiteCard.on(`click`, `.js-clear-size`, () => {
  const name = $graveSiteCardTitle.find(`span`).html();
  Swal.fire({
    title: `Очистить размер для<br>"${name}"?`,
    icon: `warning`,
    iconColor: `red`,
    showCancelButton: true,
    focusCancel: true,
    confirmButtonText: `Да, очистить`,
    confirmButtonColor: `red`,
    cancelButtonText: `Нет`,
  })
  .then((result) => {
    if (result.isConfirmed) {
      const url = $graveSiteCard.data(`action-clear-size`).replace(`{id}`, currentGraveSiteId);
      clearDataGraveSite(url, `Размер участка успешно очищен.`);
    }
  })
});

$graveSiteCard.on(`click`, `.js-clear-geo-position`, () => {
  const name = $graveSiteCardTitle.find(`span`).html();
  Swal.fire({
    title: `Очистить геопозицию для<br>"${name}"?`,
    icon: `warning`,
    iconColor: `red`,
    showCancelButton: true,
    focusCancel: true,
    confirmButtonText: `Да, очистить`,
    confirmButtonColor: `red`,
    cancelButtonText: `Нет`,
  })
  .then((result) => {
    if (result.isConfirmed) {
      const url = $graveSiteCard.data(`action-clear-geo-position`).replace(`{id}`, currentGraveSiteId);
      clearDataGraveSite(url, `Геопозиция участка успешно очищена.`);
    }
  })
});

$graveSiteCard.on(`click`, `.js-discard-person-in-charge`, () => {
  const name = $graveSiteCardTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить ответственного для<br>"${name}"?`,
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
          const url = $graveSiteCard.data(`action-discard-person-in-charge`).replace(`{id}`, currentGraveSiteId);
          clearDataGraveSite(url, `Ответственный успешно удален.`);
        }
      })
});

function clearDataGraveSite(url, message) {
  $spinner.show();
  const data = {
    token: $graveSiteCardCsrfTokenField.val(),
  };
  $.ajax({
    dataType: `json`,
    method: `PATCH`,
    url: url,
    data: JSON.stringify(data),
  })
  .done(() => {
    buildToast().fire({
      icon: `success`,
      title: message,
    });
    graveSiteCard_show(currentGraveSiteId);
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

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
  toggleActionButtonsForSize(view);
  toggleActionButtonsForGeoPosition(view);
  toggleActionButtonsForPersonInCharge(view);
  toggleActionButtonsDangerDivider();
}

function toggleActionButtonsForSize(view) {
  const $clearSizeButton = $graveSiteCard.find(`.js-clear-size`);
  if (view.size === null) {
    $clearSizeButton.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $clearSizeButton.removeClass(`d-none`);
  }
}

function toggleActionButtonsForGeoPosition(view) {
  const $clearGeoPositionButton = $graveSiteCard.find(`.js-clear-geo-position`);
  if (view.geoPositionLatitude === null && view.geoPositionLongitude === null) {
    $clearGeoPositionButton.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $clearGeoPositionButton.removeClass(`d-none`);
  }
}

function toggleActionButtonsForPersonInCharge(view) {
  const $assignPersonInChargeButton  = $graveSiteCard.find(`.js-assign-person-in-charge`);
  const $clarifyPersonInChargeButton = $graveSiteCard.find(`.js-clarify-person-in-charge`);
  const $replacePersonInChargeButton = $graveSiteCard.find(`.js-replace-person-in-charge`);
  const $discardPersonInChargeButton = $graveSiteCard.find(`.js-discard-person-in-charge`);
  if (view.personInChargeFullName === null) {
    $assignPersonInChargeButton.removeClass(`d-none`);
    $clarifyPersonInChargeButton.removeClass(`d-none`).addClass(`d-none`);
    $replacePersonInChargeButton.removeClass(`d-none`).addClass(`d-none`);
    $discardPersonInChargeButton.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $assignPersonInChargeButton.removeClass(`d-none`).addClass(`d-none`);
    $clarifyPersonInChargeButton.removeClass(`d-none`);
    $replacePersonInChargeButton.removeClass(`d-none`);
    $discardPersonInChargeButton.removeClass(`d-none`);
  }
}

function toggleActionButtonsDangerDivider() {
  let areDangerButtonsVisible = false;
  const $dangerActionButtons  = $graveSiteCard.find(`.js-danger-action-btn`);
  $.each($dangerActionButtons, function (index, dangerActionButtons) {
    if (!$(dangerActionButtons).hasClass(`d-none`)) {
      areDangerButtonsVisible = true;
    }
  });
  const $dangerActionDivider = $graveSiteCard.find(`.js-danger-action-divider`);
  if (areDangerButtonsVisible) {
    $dangerActionDivider.removeClass(`d-none`);
  } else {
    $dangerActionDivider.removeClass(`d-none`).addClass(`d-none`);
  }
}
