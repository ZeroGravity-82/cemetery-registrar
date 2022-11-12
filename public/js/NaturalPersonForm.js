`use strict`;

// import {$,jQuery} from `jquery`;
// import Swal from `sweetalert2`;
// import `Form.js`;
// import `FormButtons.js`;
// import `AppServiceFailureHandler.js`;
// import `NaturalPersonCard.js`;

class NaturalPersonForm extends Form {
  static get FORM_TYPE_CREATE()                   { return `CREATE` };
  static get FORM_TYPE_CLARIFY_FULL_NAME()        { return `CLARIFY_FULL_NAME` };
  static get FORM_TYPE_CLARIFY_CONTACT()          { return `CLARIFY_CONTACT` };
  static get FORM_TYPE_CLARIFY_BIRTH_DETAILS()    { return `CLARIFY_BIRTH_DETAILS` };
  static get FORM_TYPE_CLARIFY_PASSPORT()         { return `CLARIFY_PASSPORT` };
  static get FORM_TYPE_CLARIFY_DECEASED_DETAILS() { return `CLARIFY_DECEASED_DETAILS` };

  constructor($container, spinner, props) {
    super($container, spinner, props);
    this.urls  = {
      create                 : props.urls.naturalPerson.create,
      show                   : props.urls.naturalPerson.show,
      clarifyFullName        : props.urls.naturalPerson.clarifyFullName,
      clarifyContact         : props.urls.naturalPerson.clarifyContact,
      clarifyBirthDetails    : props.urls.naturalPerson.clarifyBirthDetails,
      clarifyPassport        : props.urls.naturalPerson.clarifyPassport,
      clarifyDeceasedDetails : props.urls.naturalPerson.clarifyDeceasedDetails,
    };
    this.csrfToken = props.csrfTokens.naturalPerson;
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

    let modalTitle           = null;
    const naturalPersonTitle = this.state.view !== null ? NaturalPersonCard.composeNaturalPersonTitle(this.state.view) : null;
    switch (this.state.formType) {
      case NaturalPersonForm.FORM_TYPE_CREATE:
        modalTitle = `Создание физлица`;
        this._renderCreate();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_FULL_NAME:
        modalTitle = `Уточнение ФИО - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyFullName();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_CONTACT:
        modalTitle = `Уточнение контактных данных - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyContact();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_BIRTH_DETAILS:
        modalTitle = `Уточнение даты и места рождения - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyBirthDetails();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_PASSPORT:
        modalTitle = `Уточнение паспортных данных - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyPassport();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_DECEASED_DETAILS:
        modalTitle = `Уточнение данных о смерти - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyDeceasedDetails();
        break;
    }
    this.modal = new Modal({
      context   : `NaturalPersonForm`,
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
    this._renderFormRowForFullName()).append(
    this._renderFormRowForPhone()).append(
    this._renderFormRowForPhoneAdditional()).append(
    this._renderFormRowForAddress()).append(
    this._renderFormRowForEmail()).append(
    this._renderFormRowForBornAt()).append(
    this._renderFormRowForPlaceOfBirth()).append(
    this._renderFormRowForPassport()).append(
    this._renderFormRowForDeceasedDetails())).append(
  this.dom.$formButtons);
  }
  _renderFormRowForFullName(fullName = null) {
    this.dom.$fullNameInput = $(`
<input type="text" class="form-control form-control-sm"
       id="fullName" name="fullName"
       aria-describedby="fullNameFeedback"
       aria-label="ФИО"
       value="${fullName ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="fullName" class="form-label">ФИО</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
        this.dom.$fullNameInput).append($(`
    <div id="fullNameFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForPhone(phone = null) {
    this.dom.$phoneInput = $(`
<input type="text" class="form-control form-control-sm"
       id="phone" name="phone"
       aria-describedby="phoneFeedback"
       aria-label="Телефон"
       value="${phone ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="phone" class="form-label">Телефон</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
        this.dom.$phoneInput).append($(`
    <div id="phoneFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForPhoneAdditional(phoneAdditional = null) {
    this.dom.$phoneAdditionalInput = $(`
<input type="text" class="form-control form-control-sm"
       id="phoneAdditional" name="phoneAdditional"
       aria-describedby="phoneAdditionalFeedback"
       aria-label="Доп. телефон"
       value="${phoneAdditional ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="phoneAdditional" class="form-label">Доп. телефон</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
        this.dom.$phoneAdditionalInput).append($(`
    <div id="phoneAdditionalFeedback" class="invalid-feedback"></div>
    `)));
  }

  _renderFormRowForAddress(address = null) {
    this.dom.$addressInput = $(`
<input type="text" class="form-control form-control-sm"
       id="address" name="address"
       aria-describedby="addressFeedback"
       aria-label="Адрес"
       value="${address ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="address" class="form-label">Адрес</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
        this.dom.$addressInput).append($(`
    <div id="addressFeedback" class="invalid-feedback"></div>
    `)));
  }

  _renderFormRowForEmail(email = null) {
    this.dom.$emailInput = $(`
<input type="text" class="form-control form-control-sm"
       id="email" name="email"
       aria-describedby="emailFeedback"
       aria-label="Эл. почта"
       value="${email ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="email" class="form-label">Эл. почта</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
        this.dom.$emailInput).append($(`
    <div id="emailFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForBornAt(bornAt = null) {
    this.dom.$bornAtInput = $(`
<input type="date" class="form-control form-control-sm"
       id="bornAt" name="bornAt"
       aria-describedby="bornAtFeedback"
       aria-label="Дата рождения"
       value="${bornAt.split(`.`).reverse().join(`-`) ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="bornAt" class="form-label">Дата рождения</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
        this.dom.$bornAtInput).append($(`
    <div id="bornAtFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForPlaceOfBirth(placeOfBirth = null) {
    this.dom.$placeOfBirthInput = $(`
<input type="text" class="form-control form-control-sm"
       id="placeOfBirth" name="placeOfBirth"
       aria-describedby="placeOfBirthFeedback"
       aria-label="Место рождения"
       value="${placeOfBirth ?? ``}">
    `);

    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 px-0"><label for="placeOfBirth" class="form-label">Место рождения</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(
        this.dom.$placeOfBirthInput).append($(`
    <div id="placeOfBirthFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormRowForPassport() {
    // TODO
  }
  _renderFormRowForDeceasedDetails() {
    // TODO
  }





  _renderClarifyFullName() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForFullName(this.state.view.fullName))).append(
    this.dom.$formButtons);
  }
  _renderClarifyContact() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForPhone(this.state.view.phone)).append(
    this._renderFormRowForPhoneAdditional(this.state.view.phoneAdditional)).append(
    this._renderFormRowForAddress(this.state.view.address)).append(
    this._renderFormRowForEmail(this.state.view.email))).append(
    this.dom.$formButtons);
  }
  _renderClarifyBirthDetails() {
    this.dom.$form = $(`
<form></form>`).append($(`
  <div class="container"></div>`).append(
    this._renderFormRowForBornAt(this.state.view.bornAt)).append(
    this._renderFormRowForPlaceOfBirth(this.state.view.placeOfBirth))).append(
    this.dom.$formButtons);
  }






  _listen() {
    super._listen();
    this.dom.$fullNameInput        && this.dom.$fullNameInput.off(`input`).on(`input`,        (event) => this._hideValidationError(event));
    this.dom.$phoneInput           && this.dom.$phoneInput.off(`input`).on(`input`,           (event) => this._hideValidationError(event));
    this.dom.$phoneAdditionalInput && this.dom.$phoneAdditionalInput.off(`input`).on(`input`, (event) => this._hideValidationError(event));
    this.dom.$addressInput         && this.dom.$addressInput.off(`input`).on(`input`,         (event) => this._hideValidationError(event));
    this.dom.$emailInput           && this.dom.$emailInput.off(`input`).on(`input`,           (event) => this._hideValidationError(event));
    this.dom.$bornAtInput          && this.dom.$bornAtInput.off(`input`).on(`input`,          (event) => this._hideValidationError(event));
    this.dom.$placeOfBirthInput    && this.dom.$placeOfBirthInput.off(`input`).on(`input`,    (event) => this._hideValidationError(event));




  }
  _handleSaveAndCloseButtonClick() {
    switch (this.state.formType) {
      case NaturalPersonForm.FORM_TYPE_CREATE:
        this._create();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_FULL_NAME:
        this._clarifyFullName();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_CONTACT:
        this._clarifyContact();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_BIRTH_DETAILS:
        this._clarifyBirthDetails();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_PASSPORT:
        this._clarifyPassport();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_DECEASED_DETAILS:
        this._clarifyDeceasedDetails();
        break;
    }
  }
  _create() {
    const data = {
      fullName       : this.dom.$fullNameInput.val()        !== `` ? this.dom.$fullNameInput.val()        : null,
      phone          : this.dom.$phoneInput.val()           !== `` ? this.dom.$phoneInput.val()           : null,
      phoneAdditional: this.dom.$phoneAdditionalInput.val() !== `` ? this.dom.$phoneAdditionalInput.val() : null,
      address        : this.dom.$addressInput.val()         !== `` ? this.dom.$addressInput.val()         : null,
      email          : this.dom.$emailInput.val()           !== `` ? this.dom.$emailInput.val()           : null,
      bornAt         : this.dom.$bornAtInput.val()          !== `` ? this.dom.$bornAtInput.val()          : null,
      placeOfBirth   : this.dom.$placeOfBirthInput.val()    !== `` ? this.dom.$placeOfBirthInput.val()    : null,




      csrfToken      : this.csrfToken,
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
        title: `Физлицо успешно создано.`,
      });
      this.hide();
      const naturalPersonCard = new NaturalPersonCard(this.dom.$container, this.spinner, this.props);
      naturalPersonCard.show(responseJson.data.id);
    })
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  _clarifyFullName() {
    const data = {
      fullName : this.dom.$fullNameInput.val() !== `` ? this.dom.$fullNameInput.val() : null,
      csrfToken: this.csrfToken,
    };
    const url               = this.urls.clarifyFullName.replace(`{id}`, this.state.view.id);
    const successToastTitle = `ФИО успешно уточнено.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
  _clarifyContact() {
    const data = {
      phone          : this.dom.$phoneInput.val()           !== `` ? this.dom.$phoneInput.val()           : null,
      phoneAdditional: this.dom.$phoneAdditionalInput.val() !== `` ? this.dom.$phoneAdditionalInput.val() : null,
      address        : this.dom.$addressInput.val()         !== `` ? this.dom.$addressInput.val()         : null,
      email          : this.dom.$emailInput.val()           !== `` ? this.dom.$emailInput.val()           : null,
      csrfToken      : this.csrfToken,
    };
    const url               = this.urls.clarifyContact.replace(`{id}`, this.state.view.id);
    const successToastTitle = `Контактные данные успешно уточнены.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
  _clarifyBirthDetails() {
    const data = {
      bornAt      : this.dom.$bornAtInput.val()       !== `` ? this.dom.$bornAtInput.val()       : null,
      placeOfBirth: this.dom.$placeOfBirthInput.val() !== `` ? this.dom.$placeOfBirthInput.val() : null,
      csrfToken   : this.csrfToken,
    }
    const url               = this.urls.clarifyBirthDetails.replace(`{id}`, this.state.view.id);
    const successToastTitle = `Дата и место рождения успешно уточнены.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
}
