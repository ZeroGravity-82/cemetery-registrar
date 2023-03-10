`use strict`;

// import {$,jQuery} from `jquery`;
// import Swal from `sweetalert2`;
// import `Modal.js`;
// import `Card.js`;
// import `CardButtons.js`;
// import `AppServiceFailureHandler.js`;
// import `NaturalPersonForm.js`;

class NaturalPersonCard extends Card {
  static composeNaturalPersonTitle(view) {
    return view.fullName;
  }

  constructor($container, spinner, props) {
    super($container, spinner, props);
    this.urls = {
      show                  : props.urls.naturalPerson.show,
      clearContact          : props.urls.naturalPerson.clearContact,
      clearBirthDetails     : props.urls.naturalPerson.clearBirthDetails,
      clearPassport         : props.urls.naturalPerson.clearPassport,
      discardDeceasedDetails: props.urls.naturalPerson.discardDeceasedDetails,
      remove                : props.urls.naturalPerson.remove,
    };
    this.csrfToken = props.csrfTokens.naturalPerson;
    this._init();
  }
  _init() {
    this._bind();
    this._stylize();
  }
  _bind() {
    super._bind();
    this._handleClarifyFullNameActionClick        = this._handleClarifyFullNameActionClick.bind(this);
    this._handleClarifyContactActionClick         = this._handleClarifyContactActionClick.bind(this);
    this._handleClarifyBirthDetailsActionClick    = this._handleClarifyBirthDetailsActionClick.bind(this);
    this._handleClarifyPassportActionClick        = this._handleClarifyPassportActionClick.bind(this);
    this._handleClarifyDeceasedDetailsActionClick = this._handleClarifyDeceasedDetailsActionClick.bind(this);
    this._handleClearContactActionClick           = this._handleClearContactActionClick.bind(this);
    this._handleClearBirthDetailsActionClick      = this._handleClearBirthDetailsActionClick.bind(this);
    this._handleClearPassportActionClick          = this._handleClearPassportActionClick.bind(this);
    this._handleDiscardDeceasedDetailsActionClick = this._handleDiscardDeceasedDetailsActionClick.bind(this);
    this._handleRemoveButtonClick                 = this._handleRemoveButtonClick.bind(this);
  }
  _render() {
    this.dom.$container.empty();

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
      <div class="col-sm-3 text-end pe-2"><strong>??????:</strong></div>
      <div class="col-sm-9 px-0"><p>${this._composeFullName(this.state.view)}</p></div></div>`)).append($(`
    <div class="row pb-2">
      <div class="col-sm-3 text-end pe-2"><strong>???????????????????? ????????????:</strong></div>
      <div class="col-sm-9 px-0"><p>${this._composeContact(this.state.view)}</p></div></div>`)).append($(`
    <div class="row pb-2">
      <div class="col-sm-3 text-end pe-2"><strong>???????????? ?? ????????????????:</strong></div>
      <div class="col-sm-9 px-0"><p>${this._composeBirthDetails(this.state.view)}</p></div></div>`)).append($(`
    <div class="row pb-2">
      <div class="col-sm-3 text-end pe-2"><strong>???????????????????? ????????????:</strong></div>
      <div class="col-sm-9 px-0"><p>${this._composePassport(this.state.view)}</p></div></div>`)).append(this.state.view.diedAt !== null ? $(`
    <div class="row pb-2">
      <div class="col-sm-3 text-end pe-2"><strong>???????????? ?? ????????????:</strong></div>
      <div class="col-sm-9 px-0">${this._composeDeceasedDetails(this.state.view)}</div></div>`) : $(``))).append(
  this.dom.$cardButtons).append($(`
  <p class="mt-2 mb-0 text-muted card-timestamps">??????????????: 20.01.2022 14:23, ????????????????: 22.02.2022 07:30</p>`));

    let modalTitle = this._composeFullName(this.state.view);
    if (this.state.view.diedAt !== null) {
      modalTitle += ` <span class="badge text-end text-bg-dark">??????????????</span>`;
    }
    this.modal = new Modal({
      context   : `NaturalPersonCard`,
      modalTitle: `???????????????? ?????????????? - ${modalTitle}`,
      $modalBody: this.dom.$card,
    }, {
      onCloseButtonClick: this._handleCloseButtonClick,
    });

    this.dom.$element = this.modal.getElement();
    this.dom.$container.append(this.dom.$element);
  }
  _listen() {
    this.dom.$clarifyFullNameAction.off(`click`).on(`click`, this._handleClarifyFullNameActionClick);
    this.dom.$clarifyContactAction.off(`click`).on(`click`, this._handleClarifyContactActionClick);
    this.dom.$clarifyBirthDetailsAction.off(`click`).on(`click`, this._handleClarifyBirthDetailsActionClick);
    this.dom.$clarifyPassportAction.off(`click`).on(`click`, this._handleClarifyPassportActionClick);
    this.dom.$clarifyDeceasedDetailsAction.off(`click`).on(`click`, this._handleClarifyDeceasedDetailsActionClick);
    this.dom.$clearContactAction           && this.dom.$clearContactAction.off(`click`).on(`click`, this._handleClearContactActionClick);
    this.dom.$clearBirthDetailsAction      && this.dom.$clearBirthDetailsAction.off(`click`).on(`click`, this._handleClearBirthDetailsActionClick);
    this.dom.$clearPassportAction          && this.dom.$clearPassportAction.off(`click`).on(`click`, this._handleClearPassportActionClick);
    this.dom.$discardDeceasedDetailsAction && this.dom.$discardDeceasedDetailsAction.off(`click`).on(`click`, this._handleDiscardDeceasedDetailsActionClick);
  }
  _handleClarifyFullNameActionClick(event) {
    this.hide();
    const naturalPersonForm = new NaturalPersonForm(this.dom.$container, this.spinner, this.props);
    naturalPersonForm.show(NaturalPersonForm.FORM_TYPE_CLARIFY_FULL_NAME, this.state.view, this);
  }
  _handleClarifyContactActionClick(event) {
    this.hide();
    const naturalPersonForm = new NaturalPersonForm(this.dom.$container, this.spinner, this.props);
    naturalPersonForm.show(NaturalPersonForm.FORM_TYPE_CLARIFY_CONTACT, this.state.view, this);
  }
  _handleClarifyBirthDetailsActionClick(event) {
    this.hide();
    const naturalPersonForm = new NaturalPersonForm(this.dom.$container, this.spinner, this.props);
    naturalPersonForm.show(NaturalPersonForm.FORM_TYPE_CLARIFY_BIRTH_DETAILS, this.state.view, this);
  }
  _handleClarifyPassportActionClick(event) {
    this.hide();
    const naturalPersonForm = new NaturalPersonForm(this.dom.$container, this.spinner, this.props);
    naturalPersonForm.show(NaturalPersonForm.FORM_TYPE_CLARIFY_PASSPORT, this.state.view, this);
  }
  _handleClarifyDeceasedDetailsActionClick(event) {
    this.hide();
    const naturalPersonForm = new NaturalPersonForm(this.dom.$container, this.spinner, this.props);
    naturalPersonForm.show(NaturalPersonForm.FORM_TYPE_CLARIFY_DECEASED_DETAILS, this.state.view, this);
  }
  _handleClearContactActionClick() {
    this._handleDangerActionClick(
      `???????????????? ???????????????????? ???????????? ??????<br>"${this._composeFullName(this.state.view)}"?`,
      () => this._clearData(this.state.view.id, this.urls.clearContact, `???????????????????? ???????????? ?????????????? ??????????????.`),
    );
  }
  _handleClearBirthDetailsActionClick() {
    this._handleDangerActionClick(
      `???????????????? ???????????? ?? ???????????????? ??????<br>"${this._composeFullName(this.state.view)}"?`,
      () => this._clearData(this.state.view.id, this.urls.clearBirthDetails, `???????????? ?? ???????????????? ?????????????? ??????????????.`),
    );
  }
  _handleClearPassportActionClick() {
    this._handleDangerActionClick(
      `???????????????? ???????????????????? ???????????? ??????<br>"${this._composeFullName(this.state.view)}"?`,
      () => this._clearData(this.state.view.id, this.urls.clearPassport, `???????????????????? ???????????? ?????????????? ??????????????.`),
    );
  }
  _handleDiscardDeceasedDetailsActionClick() {
    this._handleDangerActionClick(
      `?????????????? ???????????? ?? ???????????? ??????<br>"${this._composeFullName(this.state.view)}"?`,
      () => this._clearData(this.state.view.id, this.urls.discardDeceasedDetails, `???????????? ?? ???????????? ?????????????? ??????????????.`),
    );
  }
  _handleRemoveButtonClick() {
    this._handleDangerActionClick(
      `?????????????? ??????????????<br>"${this._composeFullName(this.state.view)}"?`,
      () => this._remove(this.state.view.id, `?????????????? ?????????????? ??????????????.`),
    );
  }
  _composeFullName(view) {
    return NaturalPersonCard.composeNaturalPersonTitle(view);
  }
  _composeContact(view) {
    let contactPhoneLine = null;
    if (view.phone !== null) {
      contactPhoneLine = view.phone;
    }
    if (view.phoneAdditional !== null) {
      contactPhoneLine = contactPhoneLine !== null ? `${contactPhoneLine}, ${view.phoneAdditional}` : view.phoneAdditional;
    }
    const contactAddressLine = view.address;
    const contactEmailLine   = view.email;

    let contact = null;
    if (contactPhoneLine !== null) {
      contact = contactPhoneLine;
    }
    if (contactAddressLine !== null) {
      contact = contact !== null ? `${contact}<br>${contactAddressLine}` : contactAddressLine;
    }
    if (contactEmailLine !== null) {
      contact = contact !== null ? `${contact}<br>${contactEmailLine}` : contactEmailLine;
    }

    return contact !== null ? contact : `-`;
  }
  _composeBirthDetails(view) {
    let birthDetails = `-`;
    switch (true) {
      case view.bornAt !== null && view.placeOfBirth === null:
        birthDetails = view.bornAt;
        break;
      case view.bornAt === null && view.placeOfBirth !== null:
        birthDetails = view.placeOfBirth;
        break;
      case view.bornAt !== null && view.placeOfBirth !== null:
        birthDetails = `${view.bornAt}, ${view.placeOfBirth}`;
        break;
    }

    return birthDetails;
  }
  _composePassport(view) {
    let passport = `-`;

    if (view.passportSeries !== null) {
      passport = `${view.passportSeries} ??? ${view.passportNumber}, ?????????? ${view.passportIssuedBy} ${view.passportIssuedAt}`;
      if (view.passportDivisionCode !== null) {
        passport = `${passport} (${view.passportDivisionCode})`;
      }
    }

    return passport;
  }
  _composeDeceasedDetails(view) {
    return `
<div class="row">
  <p class="col-sm-4 mb-1">???????? ????????????</p>
  <p class="col-sm-8 mb-1">${this._composeDiedAt(view)}</p>
</div>
<div class="row">
  <p class="col-sm-4 mb-1">??????????????</p>
  <p class="col-sm-8 mb-1">${this._composeAge(view)}</p>
</div>
<div class="row">
  <p class="col-sm-4 mb-1">?????????????? ????????????</p>
  <p class="col-sm-8 mb-1">${this._composeCauseOfDeath(view)}</p>
</div>
<div class="row">
  <p class="col-sm-4 mb-1">????????. ?? ????????????</p>
  <p class="col-sm-8 mb-1">${this._composeDeathCertificate(view)}</p>
</div>
<div class="row">
  <p class="col-sm-4 mb-1">?????????????? ?? ????????????????</p>
  <p class="col-sm-8 mb-1">${this._composeCremationCertificate(view)}</p>
</div>
    `;
  }
  _composeDiedAt(view) {
    return view.diedAt ?? `-`;
  }
  _composeAge(view) {
    return view.age ?? `-`;
  }
  _composeCauseOfDeath(view) {
    return view.causeOfDeathName ?? `-`;
  }
  _composeDeathCertificate(view) {
    return view.deathCertificateSeries
        ? `${view.deathCertificateSeries} ??? ${view.deathCertificateNumber} ???? ${view.deathCertificateIssuedAt}`
        : `-`;
  }
  _composeCremationCertificate(view) {
    return view.cremationCertificateNumber
        ? `??? ${view.cremationCertificateNumber} ???? ${view.cremationCertificateIssuedAt}`
        : `-`;
  }
  _buildActionList(view) {
    let regularActionList = [];
    regularActionList.push(this.dom.$clarifyFullNameAction        = $(`<li class="dropdown-item">???????????????? ??????</li>`));
    regularActionList.push(this.dom.$clarifyContactAction         = $(`<li class="dropdown-item">???????????????? ???????????????????? ????????????</li>`));
    regularActionList.push(this.dom.$clarifyBirthDetailsAction    = $(`<li class="dropdown-item">???????????????? ???????????? ?? ????????????????</li>`));
    regularActionList.push(this.dom.$clarifyPassportAction        = $(`<li class="dropdown-item">???????????????? ???????????????????? ????????????</li>`));
    regularActionList.push(this.dom.$clarifyDeceasedDetailsAction = $(`<li class="dropdown-item">${view.diedAt !== null ? `????????????????` : `????????????`} ???????????? ?? ????????????</li>`));

    let dangerActionList = [];
    if (view.phone !== null || view.phoneAdditional !== null || view.address !== null || view.email !== null) {
      dangerActionList.push(this.dom.$clearContactAction = $(`<li class="dropdown-item text-danger">???????????????? ???????????????????? ????????????</li>`));
    }
    if (view.bornAt !== null || view.placeOfBirth !== null) {
      dangerActionList.push(this.dom.$clearBirthDetailsAction = $(`<li class="dropdown-item text-danger">???????????????? ???????????? ?? ????????????????</li>`));
    }
    if (view.passportSeries !== null) {
      dangerActionList.push(this.dom.$clearPassportAction = $(`<li class="dropdown-item text-danger">???????????????? ???????????????????? ????????????</li>`));
    }
    if (view.diedAt !== null) {
      dangerActionList.push(this.dom.$discardDeceasedDetailsAction = $(`<li class="dropdown-item text-danger">?????????????? ???????????? ?? ????????????</li>`));
    }

    let actionList = regularActionList;
    if (dangerActionList.length > 0) {
      actionList.push($(`<li><hr class="dropdown-divider"></li>`));
      actionList.push(...dangerActionList);
    }

    return actionList;
  }
}
