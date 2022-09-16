const $tableNaturalPerson     = $(`#naturalPersonList`);
const $createNaturalPersonBtn = $(`.js-create-natural-person-btn`);

// Create
$createNaturalPersonBtn.on(`click`, () => {
  naturalPersonFormNew_show();
});

// Show
$tableNaturalPerson.on(`click`, `td`, (e) => {
  const naturalPersonId = $(e.target).closest(`tr`).attr(`data-id`);
  naturalPersonCard_show(naturalPersonId);
});
