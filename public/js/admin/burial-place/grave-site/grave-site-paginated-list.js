`use strict`;

const $tableGraveSite     = $(`#graveSiteList`);
const $createGraveSiteBtn = $(`.js-create-grave-site-btn`);
const $modalContainer     = $(`.modal-container`);

$createGraveSiteBtn.on(`click`, () => {
  const graveSiteForm = new GraveSiteForm($modalContainer, spinner, window.APP_PROPS);
  graveSiteForm.show(GraveSiteForm.FORM_TYPE_CREATE);
});
$tableGraveSite.on(`click`, `td`, event => {
  const graveSiteCard = new GraveSiteCard($modalContainer, spinner, window.APP_PROPS);
  const id            = $(event.target).closest(`tr`).attr(`data-id`);
  graveSiteCard.show(id);
});
