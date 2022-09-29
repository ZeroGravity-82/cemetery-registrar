`use strict`;

class CardButtons {
  constructor(props) {
    this.dom                  = {};
    this.$actionList          = props.$actionList;
    this._onRemoveButtonClick = props.handlers.onRemoveButtonClick;
    this._onCloseButtonClick  = props.handlers.onCloseButtonClick;
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
<button type="button"
        class="btn btn-danger btn-sm justify-content-md-start"
        aria-label="Удалить запись">Удалить запись</button>
    `);
    this.dom.$closeButton = $(`
<button type="button"
        class="btn btn-secondary btn-sm"
        aria-label="Закрыть">Закрыть</button>
    `);
    this.dom.$element = $(`
<div class="container">
  <div class="row pt-3 text-end">
    <div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end">
      <div class="order-3 order-sm-1">${this.dom.$removeButton.html()}</div>
      <div class="order-2 order-sm-2 btn-group">
        <button type="button"
                class="btn btn-warning btn-sm dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Действие">Действие</button>
        <ul class="dropdown-menu px-3 col-xs-12" style="white-space: nowrap">${this.$actionList.html()}</ul>
      </div>
      <div class="order-1 order-sm-3">${this.dom.$closeButton.html()}</div>
    </div>
  </div>
</div>
    `);
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
