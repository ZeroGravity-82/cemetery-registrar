`use strict`;

// import {$,jQuery} from `jquery`;
// import Swal from `sweetalert2`;
// import `Modal.js`;
// import `CardButtons.js`;
// import `AppServiceFailureHandler.js`;

class GraveSiteCard {
  constructor($container, spinner, props) {
    this.dom = {
      $container: $container,
    };
    this.spinner = spinner;
    this.state   = {
      view     : null,
      csrfToken: null,
    };
    this.toast = Swal.mixin(props.swalOptions);
    this.urls  = {
      show                 : props.urls.show,
      clearSize            : props.urls.clearSize,
      clearGeoPosition     : props.urls.clearGeoPosition,
      discardPersonInCharge: props.urls.discardPersonInCharge,
      remove               : props.urls.remove,
    };
    this.appServiceFailureHandler = new AppServiceFailureHandler({
      swalOptions: props.swalOptions,
    }, {
      onValidationErrors: this._displayValidationErrors,
    });
    this.toast = Swal.mixin(props.swalOptions);
    this.modal = null;
    this._init();
  }
  _init() {
    this._bind();
    this._stylize();
  }
  _bind() {
    this._handlePersonInChargeCardButtonClick = this._handlePersonInChargeCardButtonClick.bind(this);
    this._handleRemoveButtonClick             = this._handleRemoveButtonClick.bind(this);
    this._handleCloseButtonClick              = this._handleCloseButtonClick.bind(this);
  }
  _render() {
    this.dom.$container.empty();
    this.dom.$personInChargeCardButton = this.state.view.personInChargeFullName !== null
      ? $(`<i class="position-absolute bi-card-heading fs-4 ms-1 gsc-card-icon" title="Карточка ответственного"></i>`)
      : $();

    // Action list
    let actionButtonsListItems            = [];
    this.dom.$clarifyLocationButton       = $(`<li class="dropdown-item">Уточнить расположение</li>`);
    this.dom.$clarifySizeButton           = $(`<li class="dropdown-item">Уточнить размер</li>`);
    this.dom.$clarifyGeoPositionButton    = $(`<li class="dropdown-item">Уточнить геопозицию</li>`);
    this.dom.$assignPersonInChargeButton  = $(`<li class="dropdown-item">Назначить ответственного</li>`);
    this.dom.$clarifyPersonInChargeButton = $(`<li class="dropdown-item">Уточнить данные ответственного</li>`);
    this.dom.replacePersonInChargeButton  = $(`<li class="dropdown-item">Заменить ответственного</li>`);
    this.dom.dangerActionDivider          = $(`<li><hr class="dropdown-divider"></li>`);
    this.dom.$clearSizeButton             = $(`<li class="dropdown-item text-danger">Очистить размер</li>`);        // js-danger-action-btn
    this.dom.$clearGeoPositionButton      = $(`<li class="dropdown-item text-danger">Очистить геопозицию</li>`);    // js-danger-action-btn
    this.dom.$discardPersonInChargeButton = $(`<li class="dropdown-item text-danger">Удалить ответственного</li>`); // js-danger-action-btn
    actionButtonsListItems.push(this.dom.$clarifyLocationButton);
    actionButtonsListItems.push(this.dom.$clarifySizeButton);
    actionButtonsListItems.push(this.dom.$clarifyGeoPositionButton);
    actionButtonsListItems.push(this.dom.$assignPersonInChargeButton);
    actionButtonsListItems.push(this.dom.$clarifyPersonInChargeButton);
    actionButtonsListItems.push(this.dom.replacePersonInChargeButton);
    actionButtonsListItems.push(this.dom.dangerActionDivider);
    actionButtonsListItems.push(this.dom.$clearSizeButton);
    actionButtonsListItems.push(this.dom.$clearGeoPositionButton);
    actionButtonsListItems.push(this.dom.$discardPersonInChargeButton);

    // Card buttons
    this.dom.$cardButtons = (new CardButtons({
      $actionButtonsListItems: actionButtonsListItems,
    }, {
      onRemoveButtonClick: this._handleRemoveButtonClick,
      onCloseButtonClick : this._handleCloseButtonClick,
    })).getElement();

    // Card
    this.dom.$card = $(`
<div class="card border border-0"></div>`).append($(`
  <div class="card-body"></div>`).append($(`
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
            this.dom.$personInChargeCardButton))))).append(
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
    this.dom.$personInChargeCardButton.off(`click`).on(`click`, this._handlePersonInChargeCardButtonClick);
  }
  _stylize() {
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
  _setState(state) {
    this.state = {...this.state, ...state};
    this._render();
    this._listen();
  }
  _handlePersonInChargeCardButtonClick(event) {
    // TODO open natural person card
    console.log(`open natural person card...`);
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
    .then((result) => {
      if (result.isConfirmed) {
        this._removeGraveSite(this.state.view.id);
      }
    })
  }
  _handleCloseButtonClick() {
    this.modal.getObject().hide();
    location.reload();            // TODO refactor not to reload entire page
  }
  _displayValidationErrors(data) {
    for (const [fieldId, validationError] of Object.entries(data)) {
      this.toast.fire({
        icon : `error`,
        title: validationError,
      });
    }
  }
  _composeLocation(view) {
    let location = `Квартал ${view.cemeteryBlockName}, ряд ${view.rowInBlock}`;;
    if (view.positionInRow !== null) {
      location += `, место ${view.positionInRow}`;
    }

    return location;
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
  _removeGraveSite(id) {
    this.spinner.show();
    const data = {
      csrfToken: this.state.csrfToken,
    };
    $.ajax({
      dataType: `json`,
      method  : `delete`,
      url     : this.urls.remove.replace(`{id}`, id),
      data    : JSON.stringify(data),
    })
    .done(() => {
      this.toast.fire({
        icon: `success`,
        title: `Участок успешно удалён.`,
      });
      this.hide();
      location.reload();        // TODO refactor not to reload entire page
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  show(id) {
    this.spinner.show();
    $.ajax({
      dataType: `json`,
      method  : `get`,
      url     : this.urls.show.replace(`{id}`, id),
    })
    .done((responseJson) => {
      this._setState({
        view     : responseJson.data.view,
        csrfToken: responseJson.data.csrfToken,
      });
      this.modal.getObject().show();
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  hide() {
    if (this.modal === null) {
      return;
    }
    this.modal.getObject().show();
  }
}
