`use strict`;

// import Swal from `sweetalert2`;

class AppServiceFailureHandler {
  constructor(props, handlers) {
    this.toast               = Swal.mixin(props.swalOptions);
    this._onValidationErrors = handlers.onValidationErrors;
    this._init();
  }
  _init() {
    this._bind();
  }
  _bind() {
    this.onFailure = this.onFailure.bind(this);
  }
  onFailure(jqXHR) {
    if (jqXHR.responseText === undefined) {
      this.toast.fire({
        icon: `error`,
        title: `Сервер не отвечает.`,
      });

      return;
    }
    const responseJson = JSON.parse(jqXHR.responseText);
    switch (responseJson.status) {
      case `fail`:
        this._processAppFailResponse(responseJson);
        break;
      case `error`:
        this._processAppErrorResponse(responseJson);
        break;
      default:
        throw `Неподдерживаемый статус ответа прикладного сервиса: "${responseJson.status}".`;
    }
  }
  _processAppFailResponse(responseJson) {
    const failType = responseJson.data.failType;
    switch (failType) {
      case `VALIDATION_ERROR`:
        delete responseJson.data.failType;
        this._onValidationErrors(responseJson.data);
        break;
      case `NOT_FOUND`:
      case `DOMAIN_EXCEPTION`:
        this.toast.fire({
          icon: `warning`,
          title: responseJson.data.message,
        })
        break;
      default:
        throw `Неподдерживаемый тип отказа выполнения запроса прикладного сервиса: "${failType}".`;
    }
  }
  _processAppErrorResponse(responseJson) {
    this.toast.fire({
      icon: `error`,
      title: responseJson.message,
    });
  }
}
