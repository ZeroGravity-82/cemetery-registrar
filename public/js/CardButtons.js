`use strict`;

class CardButtons {
  constructor(props) {
    this.dom = {
      $actionButtonsListItems: props.$actionButtonsListItems,
    };
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
    // this.dom.$removeButtonWrapper = $(`<div class="order-3 order-sm-1">`);
    // this.dom.$removeButtonWrapper.append(this.dom.$removeButton);

//     this.dom.$actionButtonsDropdownToggle = $(`
// <button type="button"
//         class="btn btn-warning btn-sm dropdown-toggle"
//         data-bs-toggle="dropdown"
//         aria-expanded="false"
//         aria-label="Действие">Действие</button>
//     `);
//     this.dom.$actionButtonsList = $(`<ul class="dropdown-menu px-3 col-xs-12" style="white-space: nowrap"></ul>`);
//     this.dom.$actionButtonsList.append(this.dom.$actionButtonsListItems);
//     this.dom.$actionButtonsWrapper = $(`<div class="order-2 order-sm-2 btn-group">`);
//     this.dom.$actionButtonsWrapper.append(this.dom.$actionButtonsDropdownToggle);
//     this.dom.$actionButtonsWrapper.append(this.dom.$actionButtonsList);
//
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
//     this.dom.$closeButtonWrapper = $(`<div class="order-1 order-sm-3">`);
//     this.dom.$closeButtonWrapper.append(this.dom.$closeButton);

    // this.dom.$col12 = $(`<div class="col-12 d-grid gap-2 d-md-flex justify-content-md-end">`);
    // this.dom.$col12.append(this.dom.$removeButtonWrapper);
    // this.dom.$col12.append(this.dom.$actionButtonsWrapper);
    // this.dom.$col12.append(this.dom.$closeButtonWrapper);
    //
    // this.dom.$row = $(`<div class="row pt-3 text-end">`);
    // this.dom.$row.append(this.dom.$col12);
    //
    // this.dom.$element = $(`<div class="container"></div>`);
    // this.dom.$element.append(this.dom.$row);
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
