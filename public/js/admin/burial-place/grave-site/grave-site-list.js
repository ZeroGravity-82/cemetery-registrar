const $tableGraveSite     = $(`#graveSiteList`);
const $createGraveSiteBtn = $(`.js-create-grave-site-btn`);

// Create
$createGraveSiteBtn.on(`click`, () => {
  graveSiteFormNew_show();
});

// Show
$tableGraveSite.on(`click`, `td`, (e) => {
  const graveSiteId = $(e.target).closest(`tr`).attr(`data-id`);
  graveSiteCard_show(graveSiteId);
});
