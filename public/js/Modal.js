`use strict`;

class Modal {
  constructor(props, handlers) {
    this.dom = {
      $modalBody: props.$modalBody
    };
    this.context             = props.context;
    this.modalTitle          = props.modalTitle;
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
    `).append($(`
  <div class="modal-dialog modal-lg"></div>`).append($(`
    <div class="modal-content"></div>`).append($(`
      <div class="modal-header">
        <h5 class="modal-title" id="modal${this.context}Label">
          ${this.modalTitle}</h5></div>`).append(this.dom.$closeButton = $(`
        <button type="button" class="btn-close" tabindex="-1" aria-label="Закрыть"></button>`))).append($(`
      <div class="modal-body"></div>`).append(
        this.dom.$modalBody))));

    this.object = new bootstrap.Modal(this.dom.$element, {});
  }
  _listen() {
    this.dom.$closeButton.off(`click`).on(`click`, this._onCloseButtonClick);
  }
  getElement() {
    return this.dom.$element;
  }
  getObject() {
    return this.object;
  }
}
