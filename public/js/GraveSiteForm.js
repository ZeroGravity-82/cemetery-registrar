`use strict`;

// import {$,jQuery} from `jquery`;
// import Swal from `sweetalert2`;
// import `Form.js`;
// import `FormButtons.js`;
// import `AppServiceFailureHandler.js`;
// import `GraveSiteCard.js`;

class GraveSiteForm extends Form {
  static get FORM_TYPE_CREATE()                   { return `CREATE` };
  static get FORM_TYPE_CLARIFY_LOCATION()         { return `CLARIFY_LOCATION` };
  static get FORM_TYPE_CLARIFY_SIZE()             { return `CLARIFY_SIZE` };
  static get FORM_TYPE_CLARIFY_GEO_POSITION()     { return `CLARIFY_GEO_POSITION` };
  static get FORM_TYPE_ASSIGN_PERSON_IN_CHARGE()  { return `ASSIGN_PERSON_IN_CHARGE` };
  static get FORM_TYPE_REPLACE_PERSON_IN_CHARGE() { return `REPLACE_PERSON_IN_CHARGE` };

  constructor($container, spinner, props) {
    super($container, spinner, props);
    this.urls  = {
      listCemeteryBlocks    : props.urls.cemeteryBlock.list,
      create                : props.urls.graveSite.create,
      show                  : props.urls.graveSite.show,
      clarifyLocation       : props.urls.graveSite.clarifyLocation,
      clarifySize           : props.urls.graveSite.clarifySize,
      clarifyGeoPosition    : props.urls.graveSite.clarifyGeoPosition,
      // assignPersonInCharge  : props.urls.graveSite.assignPersonInCharge,
      replacePersonInCharge : props.urls.graveSite.replacePersonInCharge,
      naturalPersonListAlive: props.urls.graveSite.naturalPersonListAlive,
    };
    this.csrfToken                = props.csrfTokens.graveSite;
    this.cemeteryBlockList        = null;
    this.personInChargeSelectizer = null;
    this._init();
  }
  _init() {
    this._bind();
  }
  _bind() {
    super._bind();
    this._handleSaveAndCloseButtonClick = this._handleSaveAndCloseButtonClick.bind(this);
  }
  _render() {
    this.dom.$container.empty();

    this.dom.$formButtons = (new FormButtons({
    }, {
      onSaveAndCloseButtonClick: this._handleSaveAndCloseButtonClick,
      onCloseButtonClick       : this._handleCloseButtonClick,
    })).getElement();

    let modalTitle       = null;
    const graveSiteTitle = this.state.view !== null ? GraveSiteCard.composeGraveSiteTitle(this.state.view) : null;
    switch (this.state.formType) {
      case GraveSiteForm.FORM_TYPE_CREATE:
        modalTitle = `Создание участка`;
        this._renderCreate();
        break;
      case GraveSiteForm.FORM_TYPE_CLARIFY_LOCATION:
        modalTitle = `Уточнение расположения - <span>${graveSiteTitle}</span>`;
        this._renderClarifyLocation();
        break;
      case GraveSiteForm.FORM_TYPE_CLARIFY_SIZE:
        modalTitle = `Уточнение размера - <span>${graveSiteTitle}</span>`;
        this._renderClarifySize();
        break;
      case GraveSiteForm.FORM_TYPE_CLARIFY_GEO_POSITION:
        modalTitle = `Уточнение геопозиции - <span>${graveSiteTitle}</span>`;
        this._renderClarifyGeoPosition();
        break;
      case GraveSiteForm.FORM_TYPE_ASSIGN_PERSON_IN_CHARGE:
        modalTitle = `Назначение ответственного - <span>${graveSiteTitle}</span>`;
        this._renderAssignPersonInCharge();
        break;
      case GraveSiteForm.FORM_TYPE_REPLACE_PERSON_IN_CHARGE:
        modalTitle = `Замена ответственного - <span>${graveSiteTitle}</span>`;
        this._renderReplacePersonInCharge();
        break;
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
  _renderCreate() {
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
    this.personInChargeSelectizer = this._buildPersonInChargeSelectizer();
  }
  _buildCemeteryBlockListOptions(selectedCemeteryBlockId = null) {
    let $listOptions = [];
    if (selectedCemeteryBlockId === null) {
      $listOptions.push($(`<option selected></option>`))
    }
    $.each(this.cemeteryBlockList, (index, listItem) => {
      const $option = $(`<option value="${listItem.id}">${listItem.name}</option>`);
      if (selectedCemeteryBlockId === listItem.id) {
        $option.prop(`selected`, true);
      }
      $listOptions.push($option);
    });

    return $listOptions;
  }
  _renderFormRowForCemeteryBlock(cemeteryBlockId = null) {
    this.dom.$cemeteryBlockSelect = $(`
<select class="form-select form-select-sm"
        id="cemeteryBlockId" name="cemeteryBlockId"
        aria-describedby="cemeteryBlockIdFeedback"
        aria-label="Квартал"></select>`);
    this._loadCemeteryBlockList(cemeteryBlockId);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="cemeteryBlockId" class="form-label">Квартал</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
    this.dom.$cemeteryBlockSelect).append($(`
    <div id="cemeteryBlockIdFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForRowInBlock(rowInBlock = null) {
    this.dom.$rowInBlockInput = $(`
<input type="number" min="1" class="form-control form-control-sm"
       id="rowInBlock" name="rowInBlock"
       aria-describedby="rowInBlockFeedback"
       aria-label="Ряд"
       value="${rowInBlock ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="rowInBlock" class="form-label">Ряд</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
    this.dom.$rowInBlockInput).append($(`
    <div id="rowInBlockFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForPositionInRow(positionInRow = null) {
    this.dom.$positionInRowInput = $(`
<input type="number" min="1" class="form-control form-control-sm"
       id="positionInRow" name="positionInRow"
       aria-describedby="positionInRowFeedback"
       aria-label="Место"
       value="${positionInRow ?? ``}">    
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="positionInRow" class="form-label">Место</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
    this.dom.$positionInRowInput).append($(`
    <div id="positionInRowFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForSize(size = null) {
    this.dom.$sizeInput = $(`
<input type="number" min=0.1 step="0.1" class="form-control form-control-sm"
       id="size" name="size"
       aria-describedby="sizeFeedback"
       aria-label="Размер"
       value="${size ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="size" class="form-label">Размер, м<sup>2</sup></label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
    this.dom.$sizeInput).append($(`
    <div id="sizeFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForGeoPosition(geoPositionLatitude = null, geoPositionLongitude = null) {
    const geoPosition = geoPositionLatitude !== null && geoPositionLongitude !== null
        ? `${geoPositionLatitude}, ${geoPositionLongitude}`
        : null;
    this.dom.$geoPositionInput = $(`
<input type="text" class="form-control form-control-sm"
       id="geoPosition" name="geoPosition"
       aria-describedby="geoPositionFeedback"
       aria-label="Геопозиция"
       value="${geoPosition ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="geoPosition" class="form-label">Геопозиция</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
    this.dom.$geoPositionInput).append($(`
    <div id="geoPositionFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForPersonInCharge() {
    this.dom.$personInChargeSelect = $(`
<select
  id="personInCharge" name="personInCharge"
  aria-describedby="personInChargeFeedback"
  aria-label="Ответственный">
</select>
    `);

    return $(`
<div class="row"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="personInCharge" class="form-label">Ответственный</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
    this.dom.$personInChargeSelect).append($(`
    <div id="personInChargeFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderClarifyLocation() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForCemeteryBlock(this.state.view.cemeteryBlockId)).append(
    this._renderFormRowForRowInBlock(this.state.view.rowInBlock)).append(
    this._renderFormRowForPositionInRow(this.state.view.positionInRow))).append(
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
    <div class="row pb-2">
      <div class="col-md-3 px-0"><strong>Текущий ответственный:</strong></div>
      <div class="col-md-9 px-0"><p class="js-person-in-charge-current"></p>
      </div>
    </div>
    <div class="row pb-2">
      <div class="col-md-3 px-0"><label for="personInChargeNew" class="form-label">Новый ответственный</label></div>
      <div class="col-md-9 px-0">
        <select
            id="personInChargeNew" name="personInChargeNew"
            aria-describedby="personInChargeNewFeedback"
            aria-label="Новый ответственный">
        </select>
        <div id="personInChargeNewFeedback" class="invalid-feedback"></div>
      </div>
    </div>
  </div>
<!--  {% include '_form_buttons.html.twig' %}-->
</form>
    `);
  }
  _loadCemeteryBlockList(cemeteryBlockId = null) {
    this.spinner.show();
    $.ajax({
      dataType   : `json`,
      method     : `get`,
      url        : this.urls.listCemeteryBlocks,
      contentType: `application/json; charset=utf-8`,
    })
    .done(responseJson => {
      this.cemeteryBlockList = responseJson.data.list.items;
      this.dom.$cemeteryBlockSelect.append(this._buildCemeteryBlockListOptions(cemeteryBlockId));
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  _buildPersonInChargeSelectizer() {
    return new NaturalPersonSelectizer(
      this.dom.$personInChargeSelect,
      {
        urls: {
          load: this.urls.naturalPersonListAlive,
        },
        isDeceasedSelector: false,
        minFullNameLength : 3,
        numberOfListItems : 25,
      },
    );
  }
  _listen() {
    super._listen();
    this.dom.$cemeteryBlockSelect  && this.dom.$cemeteryBlockSelect.off(`input`).on(`input`,  (event) => this._hideValidationError(event));
    this.dom.$rowInBlockInput      && this.dom.$rowInBlockInput.off(`input`).on(`input`,      (event) => this._hideValidationError(event));
    this.dom.$positionInRowInput   && this.dom.$positionInRowInput.off(`input`).on(`input`,   (event) => this._hideValidationError(event));
    this.dom.$sizeInput            && this.dom.$sizeInput.off(`input`).on(`input`,            (event) => this._hideValidationError(event));
    this.dom.$geoPositionInput     && this.dom.$geoPositionInput.off(`input`).on(`input`,     (event) => this._hideValidationError(event));
    this.dom.$personInChargeSelect && this.dom.$personInChargeSelect.off(`input`).on(`input`, (event) => this._hideValidationError(event));
  }
  _handleSaveAndCloseButtonClick() {
    switch (this.state.formType) {
      case GraveSiteForm.FORM_TYPE_CREATE:
        this._create();
        break;
      case GraveSiteForm.FORM_TYPE_CLARIFY_LOCATION:
        this._clarifyLocation();
        break;
      case GraveSiteForm.FORM_TYPE_CLARIFY_SIZE:
        this._clarifySize();
        break;
      case GraveSiteForm.FORM_TYPE_CLARIFY_GEO_POSITION:
        this._clarifyGeoPosition();
        break;
    }
  }
  _create() {
    const geoPositionLatitude  = this.dom.$geoPositionInput.val().split(`,`)[0] ?? ``;
    const geoPositionLongitude = this.dom.$geoPositionInput.val().split(`,`)[1] ?? ``;
    const data                 = {
      cemeteryBlockId     : this.dom.$cemeteryBlockSelect.val()  !== `` ? this.dom.$cemeteryBlockSelect.val()          : null,
      rowInBlock          : this.dom.$rowInBlockInput.val()      !== `` ? parseInt(this.dom.$rowInBlockInput.val())    : null,
      positionInRow       : this.dom.$positionInRowInput.val()   !== `` ? parseInt(this.dom.$positionInRowInput.val()) : null,
      geoPositionLatitude : geoPositionLatitude.trim()           !== `` ? geoPositionLatitude.trim()                   : null,
      geoPositionLongitude: geoPositionLongitude.trim()          !== `` ? geoPositionLongitude.trim()                  : null,
      geoPositionError    : null,
      size                : this.dom.$sizeInput.val()            !== `` ? this.dom.$sizeInput.val()                    : null,
      personInChargeId    : this.dom.$personInChargeSelect.val() !== `` ? this.dom.$personInChargeSelect.val()         : null,
      csrfToken           : this.csrfToken,
    };
    this.spinner.show();
    $.ajax({
      dataType   : `json`,
      method     : `post`,
      url        : this.urls.create,
      data       : JSON.stringify(data),
      contentType: `application/json; charset=utf-8`,
    })
    .done(responseJson => {
      this.toast.fire({
        icon : `success`,
        title: `Участок успешно создан.`,
      });
      this.hide();
      const graveSiteCard = new GraveSiteCard(this.dom.$container, this.spinner, this.props);
      graveSiteCard.show(responseJson.data.id);
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  _clarifyLocation() {
    const data = {
      cemeteryBlockId: this.dom.$cemeteryBlockSelect.val() !== `` ? this.dom.$cemeteryBlockSelect.val()          : null,
      rowInBlock     : this.dom.$rowInBlockInput.val()     !== `` ? parseInt(this.dom.$rowInBlockInput.val())    : null,
      positionInRow  : this.dom.$positionInRowInput.val()  !== `` ? parseInt(this.dom.$positionInRowInput.val()) : null,
      csrfToken      : this.csrfToken,
    };
    const url               = this.urls.clarifyLocation.replace(`{id}`, this.state.view.id);
    const successToastTitle = `Расположение участка успешно уточнено.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
  _clarifySize() {
    const data = {
      size     : this.dom.$sizeInput.val() !== `` ? this.dom.$sizeInput.val() : null,
      csrfToken: this.csrfToken,
    };
    const url               = this.urls.clarifySize.replace(`{id}`, this.state.view.id);
    const successToastTitle = `Размер участка успешно уточнён.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
  _clarifyGeoPosition() {
    const geoPositionLatitude  = this.dom.$geoPositionInput.val().split(`,`)[0];
    const geoPositionLongitude = this.dom.$geoPositionInput.val().split(`,`)[1];
    const data = {
      geoPositionLatitude: this.dom.$geoPositionInput.val() !== ``
          ? geoPositionLatitude !== undefined ? geoPositionLatitude.trim() : null
          : null,
      geoPositionLongitude: this.dom.$geoPositionInput.val() !== ``
          ? geoPositionLongitude !== undefined ? geoPositionLongitude.trim() : null
          : null,
      geoPositionError: null,
      csrfToken       : this.csrfToken,
    }
    const url               = this.urls.clarifyGeoPosition.replace(`{id}`, this.state.view.id);
    const successToastTitle = `Геопозиция участка успешно уточнена.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
}
