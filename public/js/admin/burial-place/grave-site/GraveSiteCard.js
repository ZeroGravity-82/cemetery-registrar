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
      view: null,
    };
    this.urls = {
      show:                  props.urls.show,
      clearSize:             props.urls.clearSize,
      clearGeoPosition:      props.urls.clearGeoPosition,
      discardPersonInCharge: props.urls.discardPersonInCharge,
      remove:                props.urls.remove,
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
  }
  _bind() {
    this._handlePersonInChargeCardButtonClick = this._handlePersonInChargeCardButtonClick.bind(this);
    this._handleRemoveButtonClick             = this._handleRemoveButtonClick.bind(this);
    this._handleCloseButtonClick              = this._handleCloseButtonClick.bind(this);
  }
  _render() {
    this.dom.$container.empty();
    this.dom.$personInChargeCardButton = $(`
<i class="position-absolute bi-card-heading fs-4 ms-1 card-icon" title="Карточка ответственного"></i>
    `);

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
      $actionButtonsListItems: $(actionButtonsListItems),
      handlers  : {
        onRemoveButtonClick: this._handleRemoveButtonClick,
        onCloseButtonClick : this._handleCloseButtonClick,
      },
    })).getElement();

    // Card
    this.dom.$locationRow = $(`
<div class="row pb-2">
  <div class="col-sm-3 px-0"><strong>Расположение:</strong></div>
  <div class="col-sm-9 px-0"><p>${this.state.view.location}</p></div>
</div>
    `);
    this.dom.$sizeRow = $(`
<div class="row pb-2">
  <div class="col-sm-3 px-0"><strong>Размер:</strong></div>
  <div class="col-sm-9 px-0"><p>${this.state.view.size}</p></div>
</div>
    `);
    this.dom.$geoPositionRow = $(`
<div class="row pb-2">
  <div class="col-sm-3 px-0"><strong>Геопозиция:</strong></div>
  <div class="col-sm-9 px-0"><p>${this.state.view.geoPosition}</p></div>
</div>
    `);
    this.dom.$personInChargeRowValue = $(`
<p class="position-relative">
  <span>${this.state.view.personInChargeName}</span>&nbsp;
</p>
    `);
    this.dom.$personInChargeRowValue.append(this.dom.$personInChargeCardButton);
    this.dom.$personInChargeColSm9 = $(`
<div class="col-sm-9 px-0"></div>
    `);
    this.dom.$personInChargeColSm9.append(this.dom.$personInChargeRowValue);
    this.dom.$personInChargeRow = $(`
<div class="row pb-2">
  <div class="col-sm-3 px-0"><strong>Ответственный:</strong></div>
</div>
    `);
    this.dom.$personInChargeRow.append(this.dom.$personInChargeColSm9);
    this.dom.$cardBody = $(`
<div class="card-body"></div>
    `);
    this.dom.$cardBody.append(this.dom.$locationRow);
    this.dom.$cardBody.append(this.dom.$sizeRow);
    this.dom.$cardBody.append(this.dom.$geoPositionRow);
    this.dom.$cardBody.append(this.dom.$personInChargeRow);
    this.dom.$csrfToken = $(`
<input type="hidden" id="token" name="token" value="{{ csrf_token('grave_site') }}">
    `);

    this.dom.$card = $(`
<div class="card border border-0"></div>
    `);
    this.dom.$card.append(this.dom.$cardBody);
    this.dom.$card.append(this.dom.$csrfToken);
    // this.dom.$card.append(this.dom.$cardButtons);
    // this.dom.$card.append(this.dom.$cardFooter);

    // this.modal = new Modal({
    //   context: `GraveSiteCard`,
    //   $modalBody : this.dom.$card,
    // });
    // this.dom.$element = $(this.modal.getElement());
    this.dom.$container.append(this.dom.$element);
  }
  _listen() {
    this.dom.$personInChargeCardButton.off(`click`).on(`click`, this._handlePersonInChargeCardButtonClick);
  }
  _setState(state) {
    this.state = {...this.state, ...state};
    this._render();
    this._listen();
  }
  _displayValidationErrors(data) {
    for (const [fieldId, validationError] of Object.entries(data)) {
      this.toast.fire({
        icon: `error`,
        title: validationError,
      });
    }
  }
  _handlePersonInChargeCardButtonClick(event) {
    // TODO open natural person card
    console.log(`open natural person card...`);
  }
  _handleRemoveButtonClick(event) {

  }
  _handleCloseButtonClick(event) {

  }
  show(id) {
    this.spinner.show();
    $.ajax({
      dataType: `json`,
      method: `get`,
      url: this.urls.show.replace(`{id}`, id),
    })
    .done((responseJson) => {
      this._setState({
        view: responseJson.data.view,
      });
      this.modal.getModalObject().show();
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());


  }
  hide() {

  }
}
