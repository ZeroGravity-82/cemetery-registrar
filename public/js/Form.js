`use strict`;

class Form {
  constructor($container, spinner, props) {
    this.dom = {
      $container: $container,
      $form     : null,
    };
    this.spinner = spinner;
    this.props   = props;
    this.state   = {
      view            : null,
      formType        : null,
      validationErrors: {},
    }
    this.toast = Swal.mixin(props.swalOptions);
    this.appServiceFailureHandler = new AppServiceFailureHandler({
      swalOptions: props.swalOptions,
    }, {
      onValidationErrors: validationErrors =>
        this._displayValidationErrors(validationErrors)
    });
    this.modal      = null;
    this.returnCard = null;
  }
  _bind() {
    this._handleCloseButtonClick = this._handleCloseButtonClick.bind(this);
  }
  _render() {}
  _listen() {
    this._autofocus();
  }
  _autofocus() {
    this.dom.$element.off(`shown.bs.modal`).on(`shown.bs.modal`, () => this.dom.$element.find(`:input`).not(`button`).first().focus());
  }
  _setState(state) {
    this.state = {...this.state, ...state};
    this._render();
    this._listen();
  }
  _displayValidationErrors(validationErrors) {
    for (const [fieldId, validationError] of Object.entries(validationErrors)) {
      const $field = this.dom.$element.find(`#${fieldId}`);
      if ($field.length === 0) {      // Show toast if matching field is not found
        this.toast.fire({
          icon : `error`,
          title: validationError,
        });
        continue;
      }
      $field.removeClass(`is-invalid`).addClass(`is-invalid`);
      const ariaDescribedby  = $field.attr(`aria-describedby`);
      const $invalidFeedback = this.dom.$element.find(`#${ariaDescribedby}`);
      $invalidFeedback.html(validationError);
    }
  }
  _saveClarifiedData(data, url, successToastTitle) {
    this.spinner.show();
    $.ajax({
      dataType   : `json`,
      method     : `patch`,
      url        : url,
      data       : JSON.stringify(data),
      contentType: `application/json; charset=utf-8`,
    })
        .done(() => {
          this.toast.fire({
            icon : `success`,
            title: successToastTitle,
          });
          this.hide();
        })
        .fail(this.appServiceFailureHandler.onFailure)
        .always(() => this.spinner.hide());
  }
  _hideValidationError(event) {
    $(event.target).removeClass(`is-invalid`);
  }
  _handleCloseButtonClick() {
    this.hide();
    if (!this.returnCard) {
      location.reload();            // TODO refactor not to reload entire page
    }
  }
  show(formType, view = null, returnCard = null) {
    this._setState({
      view    : view,
      formType: formType,
    });
    this.returnCard = returnCard;
    this.modal.getObject().show();
  }
  hide() {
    if (this.modal === null) {
      return;
    }
    this.modal.getObject().hide();
    if (this.returnCard) {
      this.returnCard.show(this.state.view.id);
    }
  }
}
