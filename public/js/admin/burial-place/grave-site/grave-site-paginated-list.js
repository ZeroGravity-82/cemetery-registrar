`use strict`;

const $tableGraveSite     = $(`#graveSiteList`);
const $createGraveSiteBtn = $(`.js-create-grave-site-btn`);
const $modalContainer     = $(`.modal-container`);
const graveSiteForm       = new GraveSiteForm($modalContainer, spinner, window.APP_PROPS);
const graveSiteCard       = new GraveSiteCard($modalContainer, spinner, window.APP_PROPS);

$createGraveSiteBtn.on(`click`, () => graveSiteForm.show(`CREATE`));
$tableGraveSite.on(`click`, `td`, event => {
  const id = $(event.target).closest(`tr`).attr(`data-id`);
  graveSiteCard.show(id);
});
