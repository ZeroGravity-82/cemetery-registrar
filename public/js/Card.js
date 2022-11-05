`use strict`;

class Card {
  constructor($container, spinner, props) {
    this.dom = {
      $container: $container,
      $card     : null,
    };
    this.spinner = spinner;
    this.props   = props;
    this.state   = {
      view: null,
    };
    this.toast                    = Swal.mixin(props.swalOptions);
    this.appServiceFailureHandler = new AppServiceFailureHandler({
      swalOptions: props.swalOptions,
    }, {
      onValidationErrors: this._displayValidationErrors,
    });
    this.modal = null;
  }
  _stylize() {
    $(`head`).append($(`
<style>
  li.dropdown-item {
    cursor: pointer;
  }
  .card-timestamps {
    font-size: .625rem!important;
  }
</style>
    `));
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
  _handleCloseButtonClick() {
    this.hide();
    location.reload();            // TODO refactor not to reload entire page
  }
  _displayValidationErrors(validationErrors) {
    for (const [fieldId, validationError] of Object.entries(validationErrors)) {
      this.toast.fire({
        icon : `error`,
        title: validationError,
      });
      break;  // The toast can display no more than one error at a time
    }
  }
  _loadView(id, callback) {
    this.spinner.show();
    $.ajax({
      dataType: `json`,
      method  : `get`,
      url     : this.urls.show.replace(`{id}`, id),
    })
    .done(responseJson =>
      callback(responseJson)
    )
    .fail(this.appServiceFailureHandler.onFailure)
    .always(() => this.spinner.hide());
  }
  show(id) {
    this._loadView(id, responseJson => {
      this._setState({
        view: responseJson.data.view,
      });
      this.modal.getObject().show();
    });
  }
  hide() {
    if (this.modal === null) {
      return;
    }
    this.modal.getObject().hide();
  }
}
