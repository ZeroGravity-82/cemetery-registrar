`use strict`;

class Modal {
  constructor(props, handlers) {
    this.dom = {
      $modalBody: props.$modalBody
    };
    this.context             = props.context;
    this.object              = null;
    this._onCloseButtonClick = handlers.onCloseButtonClick;

    this._init();
  }
  _init() {
    this._render();
    this._listen();
  }

  _render() {
    this.dom.$element = $(`
<div class="modal fade"
     id="modal${this.context}"
     data-bs-backdrop="static"
     data-bs-keyboard="false"
     data-bs-focus="false"
     tabindex="-1"
     aria-labelledby="modal${this.context}Label"
     aria-hidden="true"></div>
    `).append(this.dom.$modalDialog = $(`
  <div class="modal-dialog modal-lg"></div>`).append(this.dom.$modalContent = $(`
    <div class="modal-content"></div>`).append(this.dom.$modalHeader = $(`
      <div class="modal-header">
        <h5 class="modal-title" id="modal${this.context}Label"></h5></div>`).append(this.dom.$closeButton = $(`
        <button type="button" class="btn-close" tabindex="-1" aria-label="Закрыть"></button>`))).append(this.dom.$modalBodyWrapper = $(`
      <div class="modal-body"></div>`).append(this.dom.$modalBody))));

    this.object = new bootstrap.Modal(this.dom.$element, {});
  }
  _listen() {
    this.dom.$closeButton.on(`click`, this._onCloseButtonClick);
  }
  getElement() {
    return this.dom.$element;
  }
  getObject() {
    return this.object;
  }
}
