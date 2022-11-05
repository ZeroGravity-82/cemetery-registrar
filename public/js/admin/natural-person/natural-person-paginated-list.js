`use strict`;

const $tableNaturalPerson     = $(`#naturalPersonList`);
const $createNaturalPersonBtn = $(`.js-create-natural-person-btn`);
const $modalContainer         = $(`.modal-container`);

$createNaturalPersonBtn.on(`click`, () => {
  // TODO
});
$tableNaturalPerson.on(`click`, `td`, event => {
  const naturalPersonCard = new NaturalPersonCard($modalContainer, spinner, window.APP_PROPS);
  const id                = $(event.target).closest(`tr`).attr(`data-id`);
  naturalPersonCard.show(id);
});
