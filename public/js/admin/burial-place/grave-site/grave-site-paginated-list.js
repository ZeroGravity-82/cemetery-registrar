`use strict`;

const $modalContainer = $(`.grave-site-modal-container`);
const graveSiteCard   = new GraveSiteCard(
  $modalContainer,
  spinner,
  window.GRAVE_SITE_PROPS,
);
const graveSiteForm = new GraveSiteForm(
  $modalContainer,
  spinner,
  window.GRAVE_SITE_PROPS,
);

const $tableGraveSite     = $(`#graveSiteList`);
// const $createGraveSiteBtn = $(`.js-create-grave-site-btn`);

// // Create
// $createGraveSiteBtn.on(`click`, () => {
//   graveSiteFormNew_show();
// });

// Show
$tableGraveSite.on(`click`, `td`, (e) => {
  const graveSiteId = $(e.target).closest(`tr`).attr(`data-id`);
  graveSiteCard.show(graveSiteId);
});
