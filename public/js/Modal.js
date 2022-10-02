`use strict`;

// import `bootstrap`;

class Modal {
  constructor(props) {
    this.dom = {
      $modalBody: props.body
    };
    this.context = props.context;
    this.object  = null;

    this._init();
  }
  _init() {
    this._bind();
    this._render();
    this._listen();
  }
  _bind() {

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
     aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal${this.context}Label"></h5>
        <button type="button" class="btn-close js-close" tabindex="-1" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        ${this.body}
      </div>
    </div>
  </div>
</div>
    `);
    this.object = new bootstrap.Modal(this.dom.$element.get(), {});
  }
  _listen() {

  }
  getElement() {
    return this.dom.$element;
  }
  getModalObject() {
    return this.object;
  }
}
