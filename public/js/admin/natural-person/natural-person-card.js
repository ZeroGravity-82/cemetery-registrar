const $naturalPersonCard                     = $(`#modalNaturalPersonCard`);
const $naturalPersonCardTitle                = $naturalPersonCard.find(`.modal-title`)
const $naturalPersonCardCard                 = $naturalPersonCard.find(`div.card`);
const $naturalPersonCardFullNameField        = $naturalPersonCard.find(`.js-full-name`);
const $naturalPersonCardContactField         = $naturalPersonCard.find(`.js-contact`);
const $naturalPersonCardBirthDetailsField    = $naturalPersonCard.find(`.js-birth-details`);
const $naturalPersonCardPassportField        = $naturalPersonCard.find(`.js-passport`);
const $naturalPersonCardDeceasedDetailsField = $naturalPersonCard.find(`.js-deceased-details`);
const $naturalPersonCardCsrfTokenField       = $naturalPersonCard.find(`input[id=token]`);
const $naturalPersonCardCloseBtn             = $naturalPersonCard.find(`.js-close`);
const naturalPersonCardModalObject           = new bootstrap.Modal(`#modalNaturalPersonCard`, {});

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
    $naturalPersonCardFullNameField.html(view.fullName);
    $naturalPersonCardContactField.html(composeContact(view));
    $naturalPersonCardBirthDetailsField.html(composeBirthDetails(view));
    $naturalPersonCardPassportField.html(composePassport(view));

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

function composeContact(view) {
  let contactAddressLine    = view.address;
  let contactPhoneEmailLine = null;
  switch (true) {
    case view.phone !== null && view.phoneAdditional === null:
      contactPhoneEmailLine = view.phone;
      break;
    case view.phone === null && view.phoneAdditional !== null:
      contactPhoneEmailLine = view.phoneAdditional;
      break;
    case view.phone !== null && view.phoneAdditional !== null:
      contactPhoneEmailLine = `${view.phone}, ${view.phoneAdditional}`;
      break;
  }
  switch (true) {
    case contactPhoneEmailLine === null && view.email !== null:
      contactPhoneEmailLine = view.email;
      break;
    case contactPhoneEmailLine !== null && view.email !== null:
      contactPhoneEmailLine = `${contactPhoneEmailLine}, ${view.email}`;
      break;
  }

  let contact = `-`;
  switch (true) {
    case contactAddressLine !== null && contactPhoneEmailLine === null:
      contact = contactAddressLine;
      break;
    case contactAddressLine === null && contactPhoneEmailLine !== null:
      contact = contactPhoneEmailLine;
      break;
    case contactAddressLine !== null && contactPhoneEmailLine !== null:
      contact = `${contactAddressLine}<br>${contactPhoneEmailLine}`;
      break;
  }

  return contact;
}

function composeBirthDetails(view) {
  let birthDetails = `-`;
  switch (true) {
    case view.bornAt !== null && view.placeOfBirth === null:
      birthDetails = view.bornAt;
      break;
    case view.bornAt === null && view.placeOfBirth !== null:
      birthDetails = view.placeOfBirth;
      break;
    case view.bornAt !== null && view.placeOfBirth !== null:
      birthDetails = `${view.bornAt}, ${view.placeOfBirth}`;
      break;
  }

  return birthDetails;
}

function composePassport(view) {
  let passport = `-`;

  if (view.passportSeries !== null) {
    passport = `${view.passportSeries} № ${view.passportNumber}, выдан ${view.passportIssuedBy} ${view.passportIssuedAt}`;
    if (view.passportDivisionCode !== null) {
      passport = `${passport} (${view.passportDivisionCode})`;
    }
  }

  return passport;
}