`use strict`;

const $container    = $(`.grave-site-card-container`);
const graveSiteCard = new GraveSiteCard(
    $container,
    spinner,
    window.GRAVE_SITE_CARD_PROPS,
);
graveSiteCard.show(`GS001`);

//
// const $tableGraveSite     = $(`#graveSiteList`);
// const $createGraveSiteBtn = $(`.js-create-grave-site-btn`);
//
// // Create
// $createGraveSiteBtn.on(`click`, () => {
//   graveSiteFormNew_show();
// });
//
// // Show
// $tableGraveSite.on(`click`, `td`, (e) => {
//   const graveSiteId = $(e.target).closest(`tr`).attr(`data-id`);
//   graveSiteCard_show(graveSiteId);
// });
