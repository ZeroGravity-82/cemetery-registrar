`use strict`;

// import {$,jQuery} from `jquery`;
// import Swal from `sweetalert2`;
// import `Form.js`;
// import `FormButtons.js`;
// import `AppServiceFailureHandler.js`;

class GraveSiteForm extends Form {
  constructor($container, spinner, props) {
    super();
    this.dom = {
      $container: $container,
      $form     : null,
    };
    this.spinner = spinner;
    this.state   = {
      view    : null,
      formType: null,
    }
    this.toast = Swal.mixin(props.swalOptions);
    this.urls  = {
      show                 : props.urls.show,
      clarifyLocation      : props.urls.clarifyLocation,
      clarifySize          : props.urls.clarifySize,
      clarifyGeoPosition   : props.urls.clarifyGeoPosition,
      // assignPersonInCharge : props.urls.assignPersonInCharge,
      replacePersonInCharge: props.urls.replacePersonInCharge,
    };
    this.csrfToken                = props.csrfToken;
    this.appServiceFailureHandler = new AppServiceFailureHandler({
      swalOptions: props.swalOptions,
    }, {
      onValidationErrors: this._displayValidationErrors,
    });
    this.modal = null;
    this._init();
  }
  _init() {
    this._bind();
  }
  _bind() {
    // TODO add bindings
    this._handleCloseButtonClick = this._handleCloseButtonClick.bind(this);
  }
  _render() {
    this.dom.$container.empty();

    this.dom.$formButtons = null;

    let modalTitle       = null;
    const graveSiteTitle = GraveSiteCard.composeGraveSiteTitle(this.state.view);
    switch (this.state.formType) {
      case `NEW`:
        modalTitle = `Создание участка`;
        this._renderNew();
        break;
      case `CLARIFY_LOCATION`:
        modalTitle = `Уточнение расположения участка - <span>${graveSiteTitle}</span>`;
        this._renderClarifyLocation();
        break;
      case `CLARIFY_SIZE`:
        modalTitle = `Уточнение размера участка - <span>${graveSiteTitle}</span>`;
        this._renderClarifySize();
        break;
      case `CLARIFY_GEO_POSITION`:
        modalTitle = `Уточнение геопозиции участка - <span>${graveSiteTitle}</span>`;
        this._renderClarifyGeoPosition();
        break;
      case `ASSIGN_PERSON_IN_CHARGE`:
        modalTitle = `Назначение ответственного за участок - <span>${graveSiteTitle}</span>`;
        this._renderAssignPersonInCharge();
        break;
      case `REPLACE_PERSON_IN_CHARGE`:
        modalTitle = `Замена ответственного за участок - <span>${graveSiteTitle}</span>`;
        this._renderReplacePersonInCharge();
        break;
      default:
        throw `Неподдерживаемый тип формы для участка: "${this.state.formType}"`;
    }

    this.modal = new Modal({
      context   : `GraveSiteForm`,
      modalTitle: modalTitle,
      $modalBody: this.dom.$form,
    }, {
      onCloseButtonClick: this._handleCloseButtonClick,
    });
    this.dom.$element = this.modal.getElement();
    this.dom.$container.append(this.dom.$element);
  }
  _renderNew() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForCemeteryBlock()).append(
    this._renderFormRowForRowInBlock()).append(
    this._renderFormRowForPositionInRow()).append(
    this._renderFormRowForSize()).append(
    this._renderFormRowForGeoPosition()).append(
    this._renderFormRowForPersonInCharge())).append(
  this.dom.$formButtons);
  }
  _renderFormRowForCemeteryBlock() {
    return $(`
<div class="row">
  <div class="col-md-3 text-start"><label for="cemeteryBlockId" class="form-label">Квартал</label></div>
  <div class="col-md-9">
    <select class="form-select form-select-sm"
            id="cemeteryBlockId" name="cemeteryBlockId"
            aria-describedby="cemeteryBlockIdFeedback"
            aria-label="Квартал">
      <option selected>
<!--      {% for listItem in cemeteryBlockList.items %}-->
<!--        <option value="{{ listItem.id }}">-->
<!--          {{- listItem.name -}}-->
<!--        </option>-->
<!--      {% endfor %}-->
    </select>
    <div id="cemeteryBlockIdFeedback" class="invalid-feedback d-none"></div>
  </div>
</div>
    `);
  }
  _renderFormRowForRowInBlock() {

  }
  _renderFormRowForPositionInRow() {

  }
  _renderFormRowForSize() {

  }
  _renderFormRowForGeoPosition() {
    const geoPosition = this.state.view.geoPositionLatitude !== null && this.state.view.geoPositionLongitude !== null
        ? `${this.state.view.geoPositionLatitude}, ${this.state.view.geoPositionLongitude}`
        : null;

  }
  _renderFormRowForPersonInCharge() {

  }
  _renderClarifyLocation() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForLocation(this.state.view.cemeteryBlockId, this.state.view.rowInBlock, this.state.view.positionInRow))).append(
    this.dom.$formButtons);
  }
  _renderClarifySize() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForSize(this.state.view.size))).append(
    this.dom.$formButtons);
  }
  _renderClarifyGeoPosition() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForGeoPosition(this.state.view.geoPositionLatitude, this.state.view.geoPositionLongitude))).append(
    this.dom.$formButtons);
  }
  _renderAssignPersonInCharge() {
    // TODO
  }
  _renderReplacePersonInCharge() {
    this.dom.$form = $(`
<form>
  <div class="container">
    <div class="row">
      <div class="col-md-3 px-0"><strong>Текущий ответственный:</strong></div>
      <div class="col-md-9 px-0"><p class="js-person-in-charge-current"></p>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3"><label for="personInChargeNew" class="form-label">Новый ответственный</label></div>
      <div class="col-md-9">
        <select
            id="personInChargeNew" name="personInChargeNew"
            aria-describedby="personInChargeNewFeedback"
            aria-label="Новый ответственный">
        </select>
        <div id="personInChargeNewFeedback" class="invalid-feedback d-none"></div>
      </div>
    </div>
  </div>
<!--  {% include '_form_buttons.html.twig' %}-->
</form>
    `);
  }
  _listen() {

  }
  _setState(state) {
    this.state = {...this.state, ...state};
    this._render();
    this._listen();
  }
  _handleCloseButtonClick() {
    this.modal.getObject().hide();
    location.reload();            // TODO refactor not to reload entire page
  }
  _displayValidationErrors(validationErrors) {
    this._setState({
      validationErrors: validationErrors,
    })
  }
  _loadView(id, callback) {
    this.spinner.show();
    $.ajax({
      dataType: `json`,
      method  : `get`,
      url     : this.urls.show.replace(`{id}`, id),
    })
    .done((responseJson) => {
      callback(responseJson);
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  show(formType, id = null) {
    if (id === null) {
      this._setState({
        view    : null,
        formType: formType,
      });
      this.modal.getObject().show();

      return;
    }
    this._loadView(id, (responseJson) => {
      this._setState({
        view    : responseJson.data.view,
        formType: formType,
      });
    });
  }
  hide() {
    if (this.modal === null) {
      return;
    }
    this.modal.getObject().show();
  }
}
