`use strict`;

class Form {
  constructor($container, spinner, props) {
    this.dom = {
      $container: $container,
      $form     : null,
    };
    this.spinner = spinner;
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
    this.modal = null;
  }
  _bind() {
    this._handleCloseButtonClick = this._handleCloseButtonClick.bind(this);
  }
  _render() {}
  _listen() {}
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
      $invalidFeedback.removeClass(`d-none`);
    }
  }
  _hideValidationError(e) {
    $(e.target).removeClass(`is-invalid`);
  }
  _handleCloseButtonClick() {
    this.modal.getObject().hide();
    location.reload();            // TODO refactor not to reload entire page
  }
}
