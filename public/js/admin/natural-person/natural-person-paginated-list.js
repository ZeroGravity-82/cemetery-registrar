`use strict`;

const $tableNaturalPerson     = $(`#naturalPersonList`);
const $createNaturalPersonBtn = $(`.js-create-natural-person-btn`);
const $modalContainer         = $(`.modal-container`);

$createNaturalPersonBtn.on(`click`, () => {
  const naturalPersonForm = new NaturalPersonForm($modalContainer, spinner, window.APP_PROPS);
  naturalPersonForm.show(NaturalPersonForm.FORM_TYPE_CREATE);
});
$tableNaturalPerson.on(`click`, `td`, event => {
  const naturalPersonCard = new NaturalPersonCard($modalContainer, spinner, window.APP_PROPS);
  const id                = $(event.target).closest(`tr`).attr(`data-id`);
  naturalPersonCard.show(id);
});
