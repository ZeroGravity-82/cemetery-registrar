const $graveSiteCard                 = $(`#modalGraveSiteCard`);
const $graveSiteCardTitle            = $graveSiteCard.find(`.modal-title`)
const $graveSiteCardCard             = $graveSiteCard.find(`div.card`);
const $graveSiteCardCsrfTokenField   = $graveSiteCard.find(`input[id=token]`);
const $graveSiteCardRemoveBtn        = $graveSiteCard.find(`.js-remove`);
const $graveSiteCardRemoveBtnWrapper = $graveSiteCard.find(`.js-remove-wrapper`);
const $graveSiteCardCloseBtn         = $graveSiteCard.find(`.js-close`);
const $graveSiteCardTimestamps       = $graveSiteCard.find(`.timestamps`);
const graveSiteCardModalObject       = new bootstrap.Modal(`#modalGraveSiteCard`, {});

function graveSiteCard_show(id) {
  $spinner.show();
  const url = $graveSiteCardCard.data(`action-show`).replace(`{id}`, id);
  $.ajax({
    dataType: `json`,
    method: `GET`,
    url: url,
  })
  .done((responseJson) => {
    const view = responseJson.data.view;
    $graveSiteCardCard.data(`id`, id);
    let graveSiteCardTitle = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      graveSiteCardTitle += `, место ${view.positionInRow}`;
    }
    $graveSiteCardTitle.html(`<span>${graveSiteCardTitle}</span> (Участки)`);
    // $graveSiteCardCemeteryBlockIdField.val(view.cemeteryBlockId);
    // $graveSiteCardRowInBlockField.val(view.rowInBlock);
    // $graveSiteCardPositionInRowField.val(view.positionInRow);
    // $graveSiteCardGeoPositionField.val(
    //     view.geoPositionLatitude !== null || view.geoPositionLongitude !== null
    //         ? [view.geoPositionLatitude, view.geoPositionLongitude].join(`, `)
    //         : ``
    // );
    // $graveSiteCardGeoPositionErrorField.val(view.geoPositionError);
    // $graveSiteCardSizeField.val(view.size);
    $graveSiteCardRemoveBtnWrapper.removeClass(`d-none`);
    $graveSiteCardTimestamps.removeClass(`d-none`);
    graveSiteCardModalObject.show();
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}

$graveSiteCard.on(`click`, `.js-remove`, () => {
  const name = $graveSiteCardTitle.find(`span`).html();
  Swal.fire({
    title: `Удалить участок "${name}"?`,
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
      const url = $graveSiteCard.data(`action-remove`).replace(`{id}`, idGraveSite);
      removeGraveSite(url);
    }
  })
});
function removeGraveSite(url) {
  $spinner.show();
  const data = {
    token: $graveSiteCardCsrfTokenField.val(),
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
      title: `Участок успешно удалён.`,
    });
    modalGraveSiteObject.hide();
    location.reload();        // TODO refactor not to reload entire page
  })
  .fail(onAjaxFailure)
  .always(onAjaxAlways);
}
