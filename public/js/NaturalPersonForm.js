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
        modalTitle = `???????????????? ??????????????`;
        this._renderCreate();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_FULL_NAME:
        modalTitle = `?????????????????? ?????? - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyFullName();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_CONTACT:
        modalTitle = `?????????????????? ???????????????????? ???????????? - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyContact();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_BIRTH_DETAILS:
        modalTitle = `?????????????????? ???????? ?? ?????????? ???????????????? - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyBirthDetails();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_PASSPORT:
        modalTitle = `?????????????????? ???????????????????? ???????????? - <span>${naturalPersonTitle}</span>`;
        this._renderClarifyPassport();
        break;
      case NaturalPersonForm.FORM_TYPE_CLARIFY_DECEASED_DETAILS:
        modalTitle = `?????????????????? ???????????? ?? ???????????? - <span>${naturalPersonTitle}</span>`;
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
    this._renderFormGroupForContact()).append(
    this._renderFormGroupForBirthDetails()).append(
    this._renderFormGroupForPassport()).append(
    this._renderFormGroupForDeceasedDetails())).append(
  this.dom.$formButtons);
  }
  _renderFormRowForFullName(fullName = null) {
    return $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="fullName" class="form-label">??????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$fullNameInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="fullName" name="fullName"
           aria-describedby="fullNameFeedback"
           aria-label="??????????????, ??????, ????????????????"
           value="${fullName ?? ``}">
        `)).append($(`
    <div id="fullNameFeedback" class="invalid-feedback"></div>
    `)));
  }
  _renderFormGroupForContact(phone = null, phoneAdditional = null, address = null, email = null) {
    const $formRowForGroupHeader = $(`
<div class="row py-2"></div>`).append($(`
  <div class="col-12">???????????????????? ????????????</div>  
    `));
    const $formRowForPhone = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="phone" class="form-label">??????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$phoneInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="phone" name="phone"
           aria-describedby="phoneFeedback"
           aria-label="??????????????"
           value="${phone ?? ``}">
        `)).append($(`
    <div id="phoneFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForPhoneAdditional = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="phoneAdditional" class="form-label">??????. ??????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$phoneAdditionalInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="phoneAdditional" name="phoneAdditional"
           aria-describedby="phoneAdditionalFeedback"
           aria-label="???????????????????????????? ??????????????"
           value="${phoneAdditional ?? ``}">
        `)).append($(`
    <div id="phoneAdditionalFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForAddress = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="address" class="form-label">??????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$addressInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="address" name="address"
           aria-describedby="addressFeedback"
           aria-label="??????????"
           value="${address ?? ``}">
        `)).append($(`
    <div id="addressFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForEmail = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="email" class="form-label">????. ??????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$emailInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="email" name="email"
           aria-describedby="emailFeedback"
           aria-label="?????????????????????? ??????????"
           value="${email ?? ``}">
        `)).append($(`
    <div id="emailFeedback" class="invalid-feedback"></div>
    `)));
    let formRows = [];
    formRows.push($formRowForGroupHeader);
    formRows.push($formRowForPhone);
    formRows.push($formRowForPhoneAdditional);
    formRows.push($formRowForAddress);
    formRows.push($formRowForEmail);

    return formRows;
  }
  _renderFormGroupForBirthDetails(bornAt = null, placeOfBirth = null) {
    const $formRowForGroupHeader = $(`
<div class="row py-2"></div>`).append($(`
  <div class="col-12">???????????? ?? ????????????????</div>  
    `));
    const $formRowForBornAt = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="bornAt" class="form-label">???????? ????????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$bornAtInput = $(`
    <input type="date" class="form-control form-control-sm"
           id="bornAt" name="bornAt"
           aria-describedby="bornAtFeedback"
           aria-label="???????? ????????????????"
           value="${bornAt ? bornAt.split(`.`).reverse().join(`-`) : ``}">
        `)).append($(`
    <div id="bornAtFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForPlaceOfBirth = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="placeOfBirth" class="form-label">?????????? ????????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$placeOfBirthInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="placeOfBirth" name="placeOfBirth"
           aria-describedby="placeOfBirthFeedback"
           aria-label="?????????? ????????????????"
           value="${placeOfBirth ?? ``}">
        `)).append($(`
    <div id="placeOfBirthFeedback" class="invalid-feedback"></div>
    `)));
    let formRows = [];
    formRows.push($formRowForGroupHeader);
    formRows.push($formRowForBornAt);
    formRows.push($formRowForPlaceOfBirth);

    return formRows;
  }
  _renderFormGroupForPassport(series = null, number = null, issuedAt = null, issuedBy = null, divisionCode = null) {
    const $formRowForGroupHeader = $(`
<div class="row py-2"></div>`).append($(`
  <div class="col-12">???????????????????? ????????????</div>  
    `));
    const $formRowForSeriesAndNumber = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="passportSeries" class="form-label">?????????? ?? ??????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$passportSeriesAndNumberInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="passportSeriesAndNumber" name="passportSeriesAndNumber"
           aria-describedby="passportSeriesAndNumberFeedback"
           aria-label="?????????? ?? ?????????? ????????????????"
           value="${series && number ? `${series} ${number}` : ``}">
        `)).append($(`
    <div id="passportSeriesAndNumberFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForIssuedBy = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="passportIssuedBy" class="form-label">?????? ??????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$passportIssuedByInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="passportIssuedBy" name="passportIssuedBy"
           aria-describedby="passportIssuedByFeedback"
           aria-label="?????? ?????????? ??????????????"
           value="${issuedBy ?? ``}">
        `)).append($(`
    <div id="passportIssuedByFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForIssuedAt = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="passportIssuedAt" class="form-label">???????? ????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$passportIssuedAtInput = $(`
    <input type="date" class="form-control form-control-sm"
           id="passportIssuedAt" name="passportIssuedAt"
           aria-describedby="passportIssuedAtFeedback"
           aria-label="???????? ???????????? ????????????????"
           value="${issuedAt ? issuedAt.split(`-`).reverse().join(`.`) : ``}">
        `)).append($(`
    <div id="passportIssuedAtFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForDivisionCode = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="passportDivisionCode" class="form-label">?????? ??????????????????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$passportDivisionCodeInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="passportDivisionCode" name="passportDivisionCode"
           aria-describedby="passportDivisionCodeFeedback"
           aria-label="?????? ?????????????????????????? ????????????????"
           value="${divisionCode ?? ``}">
        `)).append($(`
    <div id="passportDivisionCodeFeedback" class="invalid-feedback"></div>
    `)));
    let formRows = [];
    formRows.push($formRowForGroupHeader);
    formRows.push($formRowForSeriesAndNumber);
    formRows.push($formRowForIssuedBy);
    formRows.push($formRowForIssuedAt);
    formRows.push($formRowForDivisionCode);

    return formRows;
  }
  _renderFormGroupForDeceasedDetails(
      deceasedDetailsDiedAt                       = null,
      deceasedDetailsAge                          = null,
      deceasedDetailsCauseOfDeath                 = null,
      deceasedDetailsDeathCertificateSeries       = null,
      deceasedDetailsDeathCertificateNumber       = null,
      deceasedDetailsDeathCertificateIssuedAt     = null,
      deceasedDetailsCremationCertificateNumber   = null,
      deceasedDetailsCremationCertificateIssuedAt = null,
  ) {
    const $formRowForGroupHeader = $(`
<div class="row py-2"></div>`).append($(`
  <div class="col-12">???????????? ?? ????????????</div>  
    `));
    const $formRowForDiedAt = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="deceasedDetailsDiedAt" class="form-label">???????? ????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$deceasedDetailsDiedAtInput = $(`
    <input type="date" class="form-control form-control-sm"
           id="deceasedDetailsDiedAt" name="deceasedDetailsDiedAt"
           aria-describedby="deceasedDetailsDiedAtFeedback"
           aria-label="???????? ????????????"
           value="${deceasedDetailsDiedAt ? deceasedDetailsDiedAt.split(`-`).reverse().join(`.`) : ``}">
        `)).append($(`
    <div id="deceasedDetailsDiedAtFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForAge = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="deceasedDetailsAge" class="form-label">??????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$deceasedDetailsAgeInput = $(`
    <input type="number" min="0" class="form-control form-control-sm"
           id="deceasedDetailsAge" name="deceasedDetailsAge"
           aria-describedby="deceasedDetailsAgeFeedback"
           aria-label="??????????????"
           value="${deceasedDetailsAge ?? ``}">
        `)).append($(`
    <div id="deceasedDetailsAgeFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForCauseOfDeath = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="deceasedDetailsCauseOfDeath" class="form-label">?????????????? ????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$deceasedDetailsCauseOfDeathInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="deceasedDetailsCauseOfDeath" name="deceasedDetailsCauseOfDeath"
           aria-describedby="deceasedDetailsCauseOfDeathFeedback"
           aria-label="?????????????? ????????????"
           value="${deceasedDetailsCauseOfDeath ?? ``}">
        `)).append($(`
    <div id="deceasedDetailsCauseOfDeathFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForDeathCertificateGroupHeader = $(`
<div class="row py-2"></div>`).append($(`
  <div class="col-12">?????????????????????????? ?? ????????????</div>  
    `));
    const $formRowForDeathCertificateSeriesAndNumber = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="deceasedDetailsDeathCertificateSeries" class="form-label">??????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$deceasedDetailsDeathCertificateSeriesAndNumberInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="deceasedDetailsDeathCertificateSeriesAndNumber" name="deceasedDetailsDeathCertificateSeriesAndNumber"
           aria-describedby="deceasedDetailsDeathCertificateSeriesAndNumberFeedback"
           aria-label="?????????? ?????????????????????????? ?? ????????????"
           value="${deceasedDetailsDeathCertificateSeries && deceasedDetailsDeathCertificateSeries ? `${deceasedDetailsDeathCertificateSeries} ${deceasedDetailsDeathCertificateNumber}` : ``}">
        `)).append($(`
    <div id="deceasedDetailsDeathCertificateSeriesFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForDeathCertificateIssuedAt = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="deceasedDetailsDeathCertificateIssuedAt" class="form-label">???????? ????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$deceasedDetailsDeathCertificateIssuedAtInput = $(`
    <input type="date" class="form-control form-control-sm"
           id="deceasedDetailsDeathCertificateIssuedAt" name="deceasedDetailsDeathCertificateIssuedAt"
           aria-describedby="deceasedDetailsDeathCertificateIssuedAtFeedback"
           aria-label="???????? ???????????? ?????????????????????????? ?? ????????????"
           value="${deceasedDetailsDeathCertificateIssuedAt ? deceasedDetailsDeathCertificateIssuedAt.split(`-`).reverse().join(`.`) : ``}">
        `)).append($(`
    <div id="deceasedDetailsDeathCertificateIssuedAtFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForCremationCertificateGroupHeader = $(`
<div class="row py-2"></div>`).append($(`
  <div class="col-12">?????????????? ?? ????????????????</div>  
    `));
    const $formRowForCremationCertificateNumber = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="deceasedDetailsCremationCertificateNumber" class="form-label">??????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$deceasedDetailsCremationCertificateNumberInput = $(`
    <input type="text" class="form-control form-control-sm"
           id="deceasedDetailsCremationCertificateNumber" name="deceasedDetailsCremationCertificateNumber"
           aria-describedby="deceasedDetailsCremationCertificateNumberFeedback"
           aria-label="?????????? ?????????????? ?? ????????????????"
           value="${deceasedDetailsCremationCertificateNumber ?? ``}">
        `)).append($(`
    <div id="deceasedDetailsCremationCertificateNumberFeedback" class="invalid-feedback"></div>
    `)));
    const $formRowForCremationCertificateIssuedAt = $(`
<div class="row pb-2"></div>`).append($(`
  <div class="col-md-3 text-end pe-2"><label for="deceasedDetailsCremationCertificateIssuedAt" class="form-label">???????? ????????????</label></div>`)).append($(`
  <div class="col-md-9 px-0">`).append(this.dom.$deceasedDetailsCremationCertificateIssuedAtInput = $(`
    <input type="date" class="form-control form-control-sm"
           id="deceasedDetailsCremationCertificateIssuedAt" name="deceasedDetailsCremationCertificateIssuedAt"
           aria-describedby="deceasedDetailsCremationCertificateIssuedAtFeedback"
           aria-label="???????? ???????????? ?????????????? ?? ????????????????"
           value="${deceasedDetailsCremationCertificateIssuedAt ? deceasedDetailsCremationCertificateIssuedAt.split(`-`).reverse().join(`.`) : ``}">
        `)).append($(`
    <div id="deceasedDetailsCremationCertificateIssuedAtFeedback" class="invalid-feedback"></div>
    `)));
    let formRows = [];
    formRows.push($formRowForGroupHeader);
    formRows.push($formRowForDiedAt);
    formRows.push($formRowForAge);
    formRows.push($formRowForCauseOfDeath);
    formRows.push($formRowForDeathCertificateGroupHeader);
    formRows.push($formRowForDeathCertificateSeriesAndNumber);
    formRows.push($formRowForDeathCertificateIssuedAt);
    formRows.push($formRowForCremationCertificateGroupHeader);
    formRows.push($formRowForCremationCertificateNumber);
    formRows.push($formRowForCremationCertificateIssuedAt);

    return formRows;
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
    this.dom.$fullNameInput                                       && this.dom.$fullNameInput.off(`input`).on(`input`,                                       (event) => this._hideValidationError(event));
    this.dom.$phoneInput                                          && this.dom.$phoneInput.off(`input`).on(`input`,                                          (event) => this._hideValidationError(event));
    this.dom.$phoneAdditionalInput                                && this.dom.$phoneAdditionalInput.off(`input`).on(`input`,                                (event) => this._hideValidationError(event));
    this.dom.$addressInput                                        && this.dom.$addressInput.off(`input`).on(`input`,                                        (event) => this._hideValidationError(event));
    this.dom.$emailInput                                          && this.dom.$emailInput.off(`input`).on(`input`,                                          (event) => this._hideValidationError(event));
    this.dom.$bornAtInput                                         && this.dom.$bornAtInput.off(`input`).on(`input`,                                         (event) => this._hideValidationError(event));
    this.dom.$placeOfBirthInput                                   && this.dom.$placeOfBirthInput.off(`input`).on(`input`,                                   (event) => this._hideValidationError(event));
    this.dom.$passportSeriesAndNumberInput                        && this.dom.$passportSeriesAndNumberInput.off(`input`).on(`input`,                        (event) => this._hideValidationError(event));
    this.dom.$passportIssuedByInput                               && this.dom.$passportIssuedByInput.off(`input`).on(`input`,                               (event) => this._hideValidationError(event));
    this.dom.$passportIssuedAtInput                               && this.dom.$passportIssuedAtInput.off(`input`).on(`input`,                               (event) => this._hideValidationError(event));
    this.dom.$passportDivisionCodeInput                           && this.dom.$passportDivisionCodeInput.off(`input`).on(`input`,                           (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsDiedAtInput                          && this.dom.$deceasedDetailsDiedAtInput.off(`input`).on(`input`,                          (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsAgeInput                             && this.dom.$deceasedDetailsAgeInput.off(`input`).on(`input`,                             (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsCauseOfDeathInput                    && this.dom.$deceasedDetailsCauseOfDeathInput.off(`input`).on(`input`,                    (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsDeathCertificateSeriesAndNumberInput && this.dom.$deceasedDetailsDeathCertificateSeriesAndNumberInput.off(`input`).on(`input`, (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsDeathCertificateNumberInput          && this.dom.$deceasedDetailsDeathCertificateNumberInput.off(`input`).on(`input`,          (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsDeathCertificateIssuedAtInput        && this.dom.$deceasedDetailsDeathCertificateIssuedAtInput.off(`input`).on(`input`,        (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsCremationCertificateNumberInput      && this.dom.$deceasedDetailsCremationCertificateNumberInput.off(`input`).on(`input`,      (event) => this._hideValidationError(event));
    this.dom.$deceasedDetailsCremationCertificateIssuedAtInput    && this.dom.$deceasedDetailsCremationCertificateIssuedAtInput.off(`input`).on(`input`,    (event) => this._hideValidationError(event));




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
        title: `?????????????? ?????????????? ??????????????.`,
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
    const successToastTitle = `?????? ?????????????? ????????????????.`;
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
    const successToastTitle = `???????????????????? ???????????? ?????????????? ????????????????.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
  _clarifyBirthDetails() {
    const data = {
      bornAt      : this.dom.$bornAtInput.val()       !== `` ? this.dom.$bornAtInput.val()       : null,
      placeOfBirth: this.dom.$placeOfBirthInput.val() !== `` ? this.dom.$placeOfBirthInput.val() : null,
      csrfToken   : this.csrfToken,
    }
    const url               = this.urls.clarifyBirthDetails.replace(`{id}`, this.state.view.id);
    const successToastTitle = `???????????? ?? ???????????????? ?????????????? ????????????????.`;
    this._saveClarifiedData(data, url, successToastTitle);
  }
}
