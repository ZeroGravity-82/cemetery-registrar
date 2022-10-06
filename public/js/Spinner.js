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
    this._stylize();
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
  _stylize() {
    $(`head`).append($(`
<style>
  .spinner-wrapper {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    text-align: center;
    padding-top: 50vh;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 1100;
  }
</style>
    `));
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
