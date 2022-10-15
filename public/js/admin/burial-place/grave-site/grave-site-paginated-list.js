`use strict`;

const $tableGraveSite     = $(`#graveSiteList`);
const $createGraveSiteBtn = $(`.js-create-grave-site-btn`);
const $modalContainer     = $(`.grave-site-modal-container`);
const graveSiteForm       = new GraveSiteForm($modalContainer, spinner, window.GRAVE_SITE_PROPS);
const graveSiteCard       = new GraveSiteCard($modalContainer, spinner, window.GRAVE_SITE_PROPS);

$createGraveSiteBtn.on(`click`, () => graveSiteForm.show(`NEW`));
$tableGraveSite.on(`click`, `td`, (e) => {
  const graveSiteId = $(e.target).closest(`tr`).attr(`data-id`);
  graveSiteCard.show(graveSiteId);
});
