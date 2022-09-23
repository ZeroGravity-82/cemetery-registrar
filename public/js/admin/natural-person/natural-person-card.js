const $naturalPersonCard                          = $(`#modalNaturalPersonCard`);
const $naturalPersonCardTitle                     = $naturalPersonCard.find(`.modal-title`)
const $naturalPersonCardCard                      = $naturalPersonCard.find(`div.card`);
const $naturalPersonCardFullNameField             = $naturalPersonCard.find(`.js-full-name`);
const $naturalPersonCardContactField              = $naturalPersonCard.find(`.js-contact`);
const $naturalPersonCardBirthDetailsField         = $naturalPersonCard.find(`.js-birth-details`);
const $naturalPersonCardPassportField             = $naturalPersonCard.find(`.js-passport`);
const $deceasedDetailsWrapper                     = $naturalPersonCard.find(`.js-deceased-details-wrapper`);
const $naturalPersonCardDiedAtField               = $naturalPersonCard.find(`.js-died-at`);
const $naturalPersonCardAgeField                  = $naturalPersonCard.find(`.js-age`);
const $naturalPersonCardCauseOfDeathField         = $naturalPersonCard.find(`.js-cause-of-death`);
const $naturalPersonCardDeathCertificateField     = $naturalPersonCard.find(`.js-death-certificate`);
const $naturalPersonCardCremationCertificateField = $naturalPersonCard.find(`.js-cremation-certificate`);
const $naturalPersonCardCsrfTokenField            = $naturalPersonCard.find(`input[id=token]`);
const $naturalPersonCardCloseButton               = $naturalPersonCard.find(`.js-close`);
const naturalPersonCardModalObject                = new bootstrap.Modal(`#modalNaturalPersonCard`, {});

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
    const view          = responseJson.data.view;
    currentView         = view;
    const deceasedBadge = view.diedAt !== null
      ? `<span class="badge text-end text-bg-dark">Умерший</span>`
      : ``;
    $naturalPersonCardTitle.html(`Карточка физлица - <span>${view.fullName}</span> ${deceasedBadge}`);
    $naturalPersonCardFullNameField.html(view.fullName);
    $naturalPersonCardContactField.html(composeContact(view));
    $naturalPersonCardBirthDetailsField.html(composeBirthDetails(view));
    $naturalPersonCardPassportField.html(composePassport(view));
    $naturalPersonCardDiedAtField.html(composeDiedAt(view));
    $naturalPersonCardAgeField.html(composeAge(view));
    $naturalPersonCardCauseOfDeathField.html(composeCauseOfDeath(view));
    $naturalPersonCardDeathCertificateField.html(composeDeathCertificate(view));
    $naturalPersonCardCremationCertificateField.html(composeCremationCertificate(view));

    toggleActionButtons(view);
    toggleDeceasedDetails(view);
    naturalPersonCardModalObject.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

$naturalPersonCardCloseButton.on(`click`, () => {
  naturalPersonCard_close();
});

$naturalPersonCard.on(`click`, `.js-clarify-full-name`, () => {
  naturalPersonCardModalObject.hide();
  naturalPersonFormClarifyFullName_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
});

$naturalPersonCard.on(`click`, `.js-clarify-contact`, () => {
  naturalPersonCardModalObject.hide();
  naturalPersonFormClarifyContact_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
});

$naturalPersonCard.on(`click`, `.js-clarify-birth-details`, () => {
  naturalPersonCardModalObject.hide();
  naturalPersonFormClarifyBirthDetails_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
});

$naturalPersonCard.on(`click`, `.js-clarify-passport`, () => {
  naturalPersonCardModalObject.hide();
  naturalPersonFormClarifyPassport_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
});

$naturalPersonCard.on(`click`, `.js-clarify-deceased-details`, () => {
  naturalPersonCardModalObject.hide();
  naturalPersonFormClarifyDeceasedDetails_show(currentView, `naturalPersonCard_show`, [currentNaturalPersonId]);
});

$naturalPersonCard.on(`click`, `.js-clear-contact`, () => {
  const name = $naturalPersonCardTitle.find(`span`).html();
  Swal.fire({
    title: `Очистить контактные данные физлица<br>"${name}"?`,
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
      const url = $naturalPersonCardCard.data(`action-clear-contact`).replace(`{id}`, currentNaturalPersonId);
      clearDataNaturalPerson(url, `Контактные данные физлица успешно очищены.`);
    }
  })
});

$naturalPersonCard.on(`click`, `.js-clear-birth-details`, () => {
  const name = $naturalPersonCardTitle.find(`span`).html();
  Swal.fire({
    title: `Очистить дату и место рождения физлица<br>"${name}"?`,
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
      const url = $naturalPersonCardCard.data(`action-clear-birth-details`).replace(`{id}`, currentNaturalPersonId);
      clearDataNaturalPerson(url, `Дата и место рождения физлица успешно очищены.`);
    }
  })
});

$naturalPersonCard.on(`click`, `.js-clear-passport`, () => {
  const name = $naturalPersonCardTitle.find(`span`).html();
  Swal.fire({
    title: `Очистить паспортные данные физлица<br>"${name}"?`,
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
      const url = $naturalPersonCardCard.data(`action-clear-passport`).replace(`{id}`, currentNaturalPersonId);
      clearDataNaturalPerson(url, `Паспортные данные физлица успешно очищены.`);
    }
  })
});

$naturalPersonCard.on(`click`, `.js-discard-deceased-details`, () => {
  const name = $naturalPersonCardTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить данные о смерти физлица<br>"${name}"?`,
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
      const url = $naturalPersonCardCard.data(`action-discard-deceased-details`).replace(`{id}`, currentNaturalPersonId);
      clearDataNaturalPerson(url, `Данные о смерти физлица успешно удалены.`);
    }
  })
});

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

function clearDataNaturalPerson(url, message) {
  $spinner.show();
  const data = {
    token: $naturalPersonCardCsrfTokenField.val(),
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
    naturalPersonCard_show(currentNaturalPersonId);
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

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
  toggleActionButtonsForContact(view);
  toggleActionButtonsForBirthDetails(view);
  toggleActionButtonsForPassport(view);
  toggleActionButtonsForDeceasedDetails(view);
  toggleActionButtonsDangerDivider();
}
function toggleDeceasedDetails(view) {
  if (view.diedAt === null) {
    $deceasedDetailsWrapper.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $deceasedDetailsWrapper.removeClass(`d-none`);
  }
}

function toggleActionButtonsForContact(view) {
  const $clearContactButton = $naturalPersonCard.find(`.js-clear-contact`);
  if (view.address === null && view.phone === null && view.phoneAdditional === null && view.email === null) {
    $clearContactButton.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $clearContactButton.removeClass(`d-none`);
  }
}

function toggleActionButtonsForBirthDetails(view) {
  const $clearBirthDetailsButton = $naturalPersonCard.find(`.js-clear-birth-details`);
  if (view.bornAt === null && view.placeOfBirth === null) {
    $clearBirthDetailsButton.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $clearBirthDetailsButton.removeClass(`d-none`);
  }
}

function toggleActionButtonsForPassport(view) {
  const $clearPassportButton = $naturalPersonCard.find(`.js-clear-passport`);
  if (view.passportSeries === null) {
    $clearPassportButton.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $clearPassportButton.removeClass(`d-none`);
  }
}

function toggleActionButtonsForDeceasedDetails(view) {
  const $clarifyDeceasedDetailsButton = $naturalPersonCard.find(`.js-clarify-deceased-details`);
  $clarifyDeceasedDetailsButton.find(`span`).html(
    view.diedAt === null
      ? `Внести`
      : `Уточнить`
  );

  const $discardDeceasedDetailsButton = $naturalPersonCard.find(`.js-discard-deceased-details`);
  if (view.diedAt === null) {
    $discardDeceasedDetailsButton.removeClass(`d-none`).addClass(`d-none`);
  } else {
    $discardDeceasedDetailsButton.removeClass(`d-none`);
  }
}

function toggleActionButtonsDangerDivider() {
  let areDangerButtonsVisible = false;
  const $dangerActionButtons = $naturalPersonCard.find(`.js-danger-action-btn`);
  $.each($dangerActionButtons, function (index, dangerActionButtons) {
    if (!$(dangerActionButtons).hasClass(`d-none`)) {
      areDangerButtonsVisible = true;
    }
  });
  const $dangerActionDivider = $naturalPersonCard.find(`.js-danger-action-divider`);
  if (areDangerButtonsVisible) {
    $dangerActionDivider.removeClass(`d-none`);
  } else {
    $dangerActionDivider.removeClass(`d-none`).addClass(`d-none`);
  }
}

function composeContact(view) {
  let contactPhoneLine = null;
  if (view.phone !== null) {
    contactPhoneLine = view.phone;
  }
  if (view.phoneAdditional !== null) {
    contactPhoneLine = contactPhoneLine !== null ? `${contactPhoneLine}, ${view.phoneAdditional}` : view.phoneAdditional;
  }
  const contactAddressLine = view.address;
  const contactEmailLine   = view.email;

  let contact = null;
  if (contactPhoneLine !== null) {
    contact = contactPhoneLine;
  }
  if (contactAddressLine !== null) {
    contact = contact !== null ? `${contact}<br>${contactAddressLine}` : contactAddressLine;
  }
  if (contactEmailLine !== null) {
    contact = contact !== null ? `${contact}<br>${contactEmailLine}` : contactEmailLine;
  }

  return contact !== null ? contact : `-`;
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

function composeDiedAt(view) {
  return view.diedAt ?? `-`;
}

function composeAge(view) {
  return view.age ?? `-`;
}

function composeCauseOfDeath(view) {
  return view.causeOfDeathName ?? `-`;
}

function composeDeathCertificate(view) {
  return view.deathCertificateSeries
      ? `${view.deathCertificateSeries} № ${view.deathCertificateNumber} от ${view.deathCertificateIssuedAt}`
      : `-`;
}

function composeCremationCertificate(view) {
  return view.cremationCertificateNumber
      ? `№ ${view.cremationCertificateNumber} от ${view.cremationCertificateIssuedAt}`
      : `-`;
}
