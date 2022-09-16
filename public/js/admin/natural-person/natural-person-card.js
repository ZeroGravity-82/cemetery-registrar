const $naturalPersonCard                    = $(`#modalNaturalPersonCard`);
const $naturalPersonCardTitle               = $naturalPersonCard.find(`.modal-title`)
const $naturalPersonCardCard                = $naturalPersonCard.find(`div.card`);
// const $naturalPersonCardLocationField       = $naturalPersonCard.find(`.js-location`);
// const $naturalPersonCardSizeField           = $naturalPersonCard.find(`.js-size`);
// const $naturalPersonCardPersonInChargeField = $naturalPersonCard.find(`.js-person-in-charge`);
const $naturalPersonCardCsrfTokenField      = $naturalPersonCard.find(`input[id=token]`);
const $naturalPersonCardCloseBtn            = $naturalPersonCard.find(`.js-close`);
const naturalPersonCardModalObject          = new bootstrap.Modal(`#modalNaturalPersonCard`, {});

let currentNaturalPersonId;
let currentView;

function naturalPersonCard_show(id) {
  $spinner.show();

  currentNaturalPersonId = id;
  $naturalPersonCardCard.data(`id`, currentNaturalPersonId);

  const url = $naturalPersonCardCard.data(`action-show`).replace(`{id}`, currentNaturalPersonId);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: url,
  })
  .done((responseJson) => {
    const view  = responseJson.data.view;
    currentView = view;
    $naturalPersonCardTitle.html(`<span>${view.fullName}</span> (Физлица)`);
    // $naturalPersonCardLocationField.html(naturalPersonCardTitle);
    // $naturalPersonCardSizeField.html(view.size !== null ? `${view.size} м²` : `-`);
    // $naturalPersonCardPersonInChargeField.html(view.personInChargeFullName !== null ? view.personInChargeFullName : `-`);
    toggleActionButtons(view);
    naturalPersonCardModalObject.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

$naturalPersonCardCloseBtn.on(`click`, () => {
  naturalPersonCard_close();
});

// $naturalPersonCard.on(`click`, `.js-clarify-location`, () => {
//   naturalPersonCardModalObject.hide();
//   naturalPersonFormClarifyLocation_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
// });
//
// $naturalPersonCard.on(`click`, `.js-clarify-size`, () => {
//   naturalPersonCardModalObject.hide();
//   naturalPersonFormClarifySize_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
// });
//
// $naturalPersonCard.on(`click`, `.js-clarify-geo-position`, () => {
//   naturalPersonCardModalObject.hide();
//   naturalPersonFormClarifyGeoPosition_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
// });

$naturalPersonCard.on(`click`, `.js-remove`, () => {
  const name = $naturalPersonCardTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить физлицо<br>"${name}"?`,
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
      const url = $naturalPersonCardCard.data(`action-remove`).replace(`{id}`, currentNaturalPersonId);
      removeNaturalPerson(url);
    }
  })
});

function removeNaturalPerson(url) {
  $spinner.show();
  const data = {
    token: $naturalPersonCardCsrfTokenField.val(),
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
      title: `Физлицо успешно удалено.`,
    });
    naturalPersonCardModalObject.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

function naturalPersonCard_close() {
  naturalPersonCardModalObject.hide();
  location.reload();            // TODO refactor not to reload entire page
}

function toggleActionButtons(view) {
  // toggleActionBtnsForSize(view);
  // toggleActionButtonsForGeoPosition(view);
  // toggleActionButtonsForPersonInCharge(view);
  // toggleActionButtonsDangerDivider();
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
  const $dangerActionBtns = $naturalPersonCard.find(`.js-danger-action-btn`);
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
