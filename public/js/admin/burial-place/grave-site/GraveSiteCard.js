`use strict`;

// import `jquery.js`;
// import `bootstrap.js`;
// import `Modal.js`;
// import `CardButtons.js`;

class GraveSiteCard {
  constructor($container, props) {
    this.dom = {
      $container: $container,
    };
    this.state = {
      location:           null,
      size:               null,
      geoPosition:        null,
      personInChargeName: null,
    };
    this.urls  = {
      show:                  props.urls.show,
      clearSize:             props.urls.clearSize,
      clearGeoPosition:      props.urls.clearGeoPosition,
      discardPersonInCharge: props.urls.discardPersonInCharge,
      remove:                props.urls.remove,
    };
    this._init();
  }
  _init() {
    this._bind();
    this._render();
    this._listen();
  }
  _bind() {
    this._handlePersonInChargeCardButtonClick = this._handlePersonInChargeCardButtonClick.bind(this);
  }
  _render() {
    this.dom.$personInChargeCardButton = $(`
<i class="position-absolute bi-card-heading fs-4 ms-1 card-icon" title="Карточка ответственного"></i>
    `);

    // Action list
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
    this.dom.$actionList                  = null;

    // Card buttons
    this.dom.$cardButtons              = (new CardButtons({
      $actionList: this.dom.$actionList,
      handlers   : {
        onRemoveButtonClick: null,  // TODO add handlers
        onCloseButtonClick : null,  // TODO add handlers
      },
    })).getElement();

    // Card
    this.dom.$modalBody = $(`
<div class="card border border-0">
  <div class="card-body">
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Расположение:</strong></div>
      <div class="col-sm-9 px-0"><p>${this.state.location}</p></div>
    </div>
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Размер:</strong></div>
      <div class="col-sm-9 px-0"><p>${this.state.size}</p></div>
    </div>
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Геопозиция:</strong></div>
      <div class="col-sm-9 px-0"><p>${this.state.geoPosition}</p></div>
    </div>
    <div class="row pb-2">
      <div class="col-sm-3 px-0"><strong>Ответственный:</strong></div>
      <div class="col-sm-9 px-0">
        <p class="position-relative">
          <span>${this.state.personInChargeName}</span>&nbsp;
            ${this.dom.$personInChargeCardButton.html()}
        </p>
      </div>
    </div>
  </div>
  <input type="hidden" id="token" name="token" value="{{ csrf_token('grave_site') }}">
  ${this.dom.$cardButtons.html()}
<!--  {% include '_card_footer.html.twig' %}-->
</div>
    `);

    const modal = new Modal({
      context: `GraveSiteCard`,
    });
    this.dom.$element = $();



    this.dom.$container.append(this.dom.$element);
  }
  _listen() {
    this.dom.$personInChargeCardButton.on(`click`, this._handlePersonInChargeCardButtonClick);
  }
  _setState(state) {
    this.state = {...this.state, ...state};
    this._render();
  }
  _handlePersonInChargeCardButtonClick(event) {
    // TODO open natural person card
    console.log(`open natural person card...`);
  }
  show(graveSiteId) {

  }
  hide() {

  }
}
