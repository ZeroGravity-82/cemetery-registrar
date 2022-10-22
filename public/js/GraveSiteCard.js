`use strict`;

// import {$,jQuery} from `jquery`;
// import Swal from `sweetalert2`;
// import `Modal.js`;
// import `Card.js`;
// import `CardButtons.js`;
// import `AppServiceFailureHandler.js`;

class GraveSiteCard extends Card {
  constructor($container, spinner, props) {
    super($container, spinner, props);
    this.urls = {
      show                 : props.urls.graveSite.show,
      clearSize            : props.urls.graveSite.clearSize,
      clearGeoPosition     : props.urls.graveSite.clearGeoPosition,
      discardPersonInCharge: props.urls.graveSite.discardPersonInCharge,
      remove               : props.urls.graveSite.remove,
    };
    this.csrfToken = props.csrfTokens.graveSite;
    this._init();
  }
  _init() {
    this._bind();
    this._stylize();
  }
  _bind() {
    super._bind();
    this._handleClarifyLocationActionClick       = this._handleClarifyLocationActionClick.bind(this);
    this._handleClarifySizeActionClick           = this._handleClarifySizeActionClick.bind(this);
    this._handleClarifyGeoPositionActionClick    = this._handleClarifyGeoPositionActionClick.bind(this);
    this._handleAssignPersonInChargeActionClick  = this._handleAssignPersonInChargeActionClick.bind(this);
    this._handleClarifyPersonInChargeActionClick = this._handleClarifyPersonInChargeActionClick.bind(this);
    this._handleReplacePersonInChargeActionClick = this._handleReplacePersonInChargeActionClick.bind(this);
    this._handleClearSizeActionClick             = this._handleClearSizeActionClick.bind(this);
    this._handleClearGeoPositionActionClick      = this._handleClearGeoPositionActionClick.bind(this);
    this._handleDiscardPersonInChargeActionClick = this._handleDiscardPersonInChargeActionClick.bind(this);
    this._handlePersonInChargeCardIconClick      = this._handlePersonInChargeCardIconClick.bind(this);
    this._handleRemoveButtonClick                = this._handleRemoveButtonClick.bind(this);
  }
  _render() {
    this.dom.$container.empty();
    this.dom.$personInChargeCardIcon = this.state.view.personInChargeFullName !== null
      ? $(`<i class="position-absolute bi-card-heading fs-4 ms-1 gsc-card-icon" title="Карточка ответственного"></i>`)
      : $();

    this.dom.$cardButtons = (new CardButtons({
      actionList: this._buildActionList(this.state.view),
    }, {
      onRemoveButtonClick: this._handleRemoveButtonClick,
      onCloseButtonClick : this._handleCloseButtonClick,
    })).getElement();

    this.dom.$card = $(`
<div class="card border border-0"></div>`).append($(`
  <div class="card-body py-0"></div>`).append($(`
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Расположение:</strong></div>
      <div class="col-sm-9 px-0"><p>${this._composeLocation(this.state.view)}</p></div></div>`)).append($(`
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Размер:</strong></div>
      <div class="col-sm-9 px-0"><p>${this._composeSize(this.state.view)}</p></div></div>`)).append($(`
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Геопозиция:</strong></div>
      <div class="col-sm-9 px-0"><p>${this._composeGeoPosition(this.state.view)}</p></div></div>`)).append($(`
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Ответственный:</strong></div></div>`).append($(`
      <div class="col-sm-9 px-0"></div>`).append($(`
        <p class="position-relative">
          <span>${this._composePersonInChargeFullName(this.state.view)}</span>&nbsp;</p>`).append(
            this.dom.$personInChargeCardIcon))))).append(
  this.dom.$cardButtons).append($(`
  <p class="mt-2 mb-0 text-muted gsc-timestamps">Создано: 20.01.2022 14:23, изменено: 22.02.2022 07:30</p>`));

    this.modal = new Modal({
      context   : `GraveSiteCard`,
      modalTitle: `Карточка участка - <span>${this._composeLocation(this.state.view)}</span>`,
      $modalBody: this.dom.$card,
    }, {
      onCloseButtonClick: this._handleCloseButtonClick,
    });
    this.dom.$element = this.modal.getElement();
    this.dom.$container.append(this.dom.$element);
  }
  _listen() {
    this.dom.$clarifyLocationAction.off(`click`).on(`click`, this._handleClarifyLocationActionClick);
    this.dom.$clarifySizeAction           && this.dom.$clarifySizeAction.off(`click`).on(`click`, this._handleClarifySizeActionClick);
    this.dom.$clarifyGeoPositionAction    && this.dom.$clarifyGeoPositionAction.off(`click`).on(`click`, this._handleClarifyGeoPositionActionClick);
    this.dom.$assignPersonInChargeAction  && this.dom.$assignPersonInChargeAction.off(`click`).on(`click`, this._handleAssignPersonInChargeActionClick);
    this.dom.$clarifyPersonInChargeAction && this.dom.$clarifyPersonInChargeAction.off(`click`).on(`click`, this._handleClarifyPersonInChargeActionClick);
    this.dom.$replacePersonInChargeAction && this.dom.$replacePersonInChargeAction.off(`click`).on(`click`, this._handleReplacePersonInChargeActionClick);
    this.dom.$clearSizeAction             && this.dom.$clearSizeAction.off(`click`).on(`click`, this._handleClearSizeActionClick);
    this.dom.$clearGeoPositionAction      && this.dom.$clearGeoPositionAction.off(`click`).on(`click`, this._handleClearGeoPositionActionClick);
    this.dom.$discardPersonInChargeAction && this.dom.$discardPersonInChargeAction.off(`click`).on(`click`, this._handleDiscardPersonInChargeActionClick);
    this.dom.$personInChargeCardIcon.off(`click`).on(`click`, this._handlePersonInChargeCardIconClick);
  }
  _stylize() {
    super._stylize();
    $(`head`).append(`
<style>
  .gsc-card-icon {
    bottom: -5px;
  }
  .gsc-card-icon:hover {
    cursor: pointer;
  }
  .gsc-timestamps {
    font-size: .625rem!important;
  }
</style>
    `);
  }
  _handleClarifyLocationActionClick(event) {
    // TODO
  }
  _handleClarifySizeActionClick(event) {
    // TODO
  }
  _handleClarifyGeoPositionActionClick(event) {
    // TODO
  }
  _handleAssignPersonInChargeActionClick(event) {
    // TODO
  }
  _handleClarifyPersonInChargeActionClick(event) {
    // TODO
  }
  _handleReplacePersonInChargeActionClick(event) {
    // TODO
  }
  _handleClearSizeActionClick() {
    Swal.fire({
      title             : `Очистить размер для<br>"${this._composeLocation(this.state.view)}"?`,
      icon              : `warning`,
      iconColor         : `red`,
      showCancelButton  : true,
      focusCancel       : true,
      confirmButtonText : `Да, очистить`,
      confirmButtonColor: `red`,
      cancelButtonText  : `Нет`,
    })
    .then(result => {
      if (result.isConfirmed) {
        this._clearGraveSiteData(this.state.view.id, this.urls.clearSize, `Размер участка успешно очищен.`);
      }
    })
  }
  _handleClearGeoPositionActionClick() {
    Swal.fire({
      title             : `Очистить геопозицию для<br>"${this._composeLocation(this.state.view)}"?`,
      icon              : `warning`,
      iconColor         : `red`,
      showCancelButton  : true,
      focusCancel       : true,
      confirmButtonText : `Да, очистить`,
      confirmButtonColor: `red`,
      cancelButtonText  : `Нет`,
    })
    .then(result => {
      if (result.isConfirmed) {
        this._clearGraveSiteData(this.state.view.id, this.urls.clearGeoPosition, `Геопозиция участка успешно очищена.`);
      }
    })
  }
  _handleDiscardPersonInChargeActionClick() {
    Swal.fire({
      title             : `Удалить ответственного для<br>"${this._composeLocation(this.state.view)}"?`,
      icon              : `warning`,
      iconColor         : `red`,
      showCancelButton  : true,
      focusCancel       : true,
      confirmButtonText : `Да, удалить`,
      confirmButtonColor: `red`,
      cancelButtonText  : `Нет`,
    })
    .then(result => {
      if (result.isConfirmed) {
        this._clearGraveSiteData(this.state.view.id, this.urls.discardPersonInCharge, `Ответственный успешно удалён.`);
      }
    })
  }
  _handlePersonInChargeCardIconClick(event) {
    // TODO
  }
  _handleRemoveButtonClick() {
    Swal.fire({
      title             : `Удалить участок<br>"${this._composeLocation(this.state.view)}"?`,
      icon              : `warning`,
      iconColor         : `red`,
      showCancelButton  : true,
      focusCancel       : true,
      confirmButtonText : `Да, удалить`,
      confirmButtonColor: `red`,
      cancelButtonText  : `Нет`,
    })
    .then(result => {
      if (result.isConfirmed) {
        this._removeGraveSite(this.state.view.id);
      }
    })
  }
  _composeLocation(view) {
    return GraveSiteCard.composeGraveSiteTitle(view);
  }
  _composeSize(view) {
    return view.size !== null ? `${view.size} м²` : `-`;
  }
  _composeGeoPosition(view) {
    let geoPosition = view.geoPositionLatitude !== null || view.geoPositionLongitude !== null
        ? [view.geoPositionLatitude, view.geoPositionLongitude].join(`, `)
        : null;
    if (geoPosition !== null && view.geoPositionError !== null) {
      geoPosition += ` (± ${view.geoPositionError} м)`;
    }

    return geoPosition !== null ? geoPosition : `-`;
  }
  _composePersonInChargeFullName(view) {
    return view.personInChargeFullName !== null ? view.personInChargeFullName : `-`;
  }
  _buildActionList(view) {
    let regularActionList = [];
    regularActionList.push(this.dom.$clarifyLocationAction         = $(`<li class="dropdown-item">Уточнить расположение</li>`));
    regularActionList.push(this.dom.$clarifySizeAction             = $(`<li class="dropdown-item">Уточнить размер</li>`));
    regularActionList.push(this.dom.$clarifyGeoPositionAction      = $(`<li class="dropdown-item">Уточнить геопозицию</li>`));
    if (view.personInChargeFullName === null) {
      regularActionList.push(this.dom.$assignPersonInChargeAction  = $(`<li class="dropdown-item">Назначить ответственного</li>`));
    } else {
      regularActionList.push(this.dom.$clarifyPersonInChargeAction = $(`<li class="dropdown-item">Уточнить данные ответственного</li>`));
      regularActionList.push(this.dom.$replacePersonInChargeAction = $(`<li class="dropdown-item">Заменить ответственного</li>`));
    }

    let dangerActionList = [];
    if (view.size !== null) {
      dangerActionList.push(this.dom.$clearSizeAction              = $(`<li class="dropdown-item text-danger">Очистить размер</li>`));
    }
    if (view.geoPositionLatitude !== null || view.geoPositionLongitude !== null) {
      dangerActionList.push(this.dom.$clearGeoPositionAction       = $(`<li class="dropdown-item text-danger">Очистить геопозицию</li>`));
    }
    if (view.personInChargeFullName !== null) {
      dangerActionList.push(this.dom.$discardPersonInChargeAction  = $(`<li class="dropdown-item text-danger">Удалить ответственного</li>`));
    }

    let actionList = regularActionList;
    if (dangerActionList.length > 0) {
      actionList.push($(`<li><hr class="dropdown-divider"></li>`));
      actionList.push(...dangerActionList);
    }

    return actionList;
  }
  _removeGraveSite(id) {
    this.spinner.show();
    const data = {
      csrfToken: this.csrfToken,
    };
    $.ajax({
      dataType: `json`,
      method  : `delete`,
      url     : this.urls.remove.replace(`{id}`, id),
      data    : JSON.stringify(data),
    })
    .done(() => {
      this.toast.fire({
        icon : `success`,
        title: `Участок успешно удалён.`,
      });
      this.hide();
      location.reload();        // TODO refactor not to reload entire page
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  _clearGraveSiteData(id, url, message) {
    this.spinner.show();
    const data = {
      csrfToken: this.csrfToken,
    };
    $.ajax({
    dataType: `json`,
    method  : `patch`,
    url     : url.replace(`{id}`, id),
    data    : JSON.stringify(data),
  })
  .done(() => {
    this.toast.fire({
      icon : `success`,
      title: message,
    });
    this.show(id);
  })
  .fail(this.appServiceFailureHandler.onFailure)
  .always(() => this.spinner.hide());
  }
  static composeGraveSiteTitle(view) {
    let title = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;
    if (view.positionInRow !== null) {
      title += `, место ${view.positionInRow}`;
    }

    return title;
  }
}
