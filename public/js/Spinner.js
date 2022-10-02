`use strict`;

class Spinner {
  constructor($container) {
    this.dom = {
      $container: $container,
    };
    this._init();
  }
  _init() {
    this._render();
  }
  _render() {
    this.dom.$container.empty();
    this.dom.$element = $(`
<div class="spinner-wrapper">
  <div class="spinner-border" role="status"></div>
</div>
    `);
    this.dom.$container.append(this.dom.$element);
  }
  getElement() {
    return this.dom.$element;
  }
  show() {
    this.dom.$element.show();
  }
  hide() {
    this.dom.$element.hide();
  }
}
