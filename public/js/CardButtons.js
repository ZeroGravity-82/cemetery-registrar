`use strict`;

class CardButtons {
  constructor(props, handlers) {
    this.dom = {
      actionList: props.actionList,
    };
    this._onRemoveButtonClick = handlers.onRemoveButtonClick;
    this._onCloseButtonClick  = handlers.onCloseButtonClick;
    this._init();

  }
  _init() {
    this._bind();
    this._render();
    this._listen();
  }
  _bind() {
    this._handleRemoveButtonClick = this._handleRemoveButtonClick.bind(this);
    this._handleCloseButtonClick  = this._handleCloseButtonClick.bind(this);
  }
  _render() {
    this.dom.$removeButton = $(`
<button type="button" class="btn btn-danger btn-sm justify-content-md-start" aria-label="Удалить запись">Удалить запись</button>
    `);
    this.dom.$closeButton = $(`
<button type="button" class="btn btn-secondary btn-sm" aria-label="Закрыть">Закрыть</button>
    `);
    this.dom.$element = $(`
<div class="container"></div>`).append($(`
  <div class="row text-end"></div>`).append($(`
    <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end"></div>`).append($(`
      <div class="order-3 order-sm-1"></div>`).append(
        this.dom.$removeButton)).append($(`
      <div class="order-2 order-sm-2 btn-group"></div>`).append($(`
        <button type="button"
                class="btn btn-warning btn-sm dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Действие">Действие</button>`)).append($(`
        <ul class="dropdown-menu px-3 col-xs-12" style="white-space: nowrap"></ul>`).append(
          this.dom.actionList))).append($(`
      <div class="order-1 order-sm-3"></div>`).append(
        this.dom.$closeButton))));
  }
  _listen() {
    this.dom.$removeButton.off(`click`).on(`click`, this._handleRemoveButtonClick);
    this.dom.$closeButton.off(`click`).on(`click`, this._handleCloseButtonClick);
  }
  _handleRemoveButtonClick() {
    this._onRemoveButtonClick();
  }
  _handleCloseButtonClick() {
    this._onCloseButtonClick();
  }
  getElement() {
    return this.dom.$element;
  }
}
