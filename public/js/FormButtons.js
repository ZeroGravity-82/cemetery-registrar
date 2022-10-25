`use strict`;

class FormButtons {
  constructor(props, handlers) {
    this.dom = {};
    this._onSaveAndCloseButtonClick = handlers.onSaveAndCloseButtonClick;
    this._onCloseButtonClick        = handlers.onCloseButtonClick;
    this._init();

  }
  _init() {
    this._bind();
    this._render();
    this._listen();
  }
  _bind() {
    this._handleSaveAndCloseButtonClick = this._handleSaveAndCloseButtonClick.bind(this);
    this._handleCloseButtonClick        = this._handleCloseButtonClick.bind(this);
  }
  _render() {
    this.dom.$saveAndCloseButton = $(`
<button type="button" class="btn btn-warning btn-sm" aria-label="Записать и закрыть">Записать и закрыть</button>
    `);
    this.dom.$closeButton = $(`
<button type="button" class="btn btn-secondary btn-sm" aria-label="Закрыть">Закрыть</button>
    `);
    this.dom.$element = $(`
<div class="container"></div>`).append($(`
  <div class="row pt-3 text-end"></div>`).append($(`
    <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end"></div>`).append($(`
      <div class="order-2 order-sm-1"></div>`).append(
        this.dom.$saveAndCloseButton)).append($(`
      <div class="order-1 order-sm-2"></div>`).append(
        this.dom.$closeButton))));
  }
  _listen() {
    this.dom.$saveAndCloseButton.off(`click`).on(`click`, this._handleSaveAndCloseButtonClick);
    this.dom.$closeButton.off(`click`).on(`click`, this._handleCloseButtonClick);
  }
  _handleSaveAndCloseButtonClick() {
    this._onSaveAndCloseButtonClick();
  }
  _handleCloseButtonClick() {
    this._onCloseButtonClick();
  }
  getElement() {
    return this.dom.$element;
  }
}
