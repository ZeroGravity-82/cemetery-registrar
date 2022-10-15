`use strict`;

// import {$,jQuery} from `jquery`;
// import `selectize.js`;

class NaturalPersonSelectizer {
  constructor($select, props, handlers) {
    this.dom = {
      $select: $select,
    }
    this.urls = {
      load: props.urls.load,
    }
    this.isDeceasedSelector = props.isDeceasedSelector || false;
    this.minFullNameLength  = props.minFullNameLength  || 3;
    this.numberOfListItems  = props.numberOfListItems  || 25;
    this._init();
  }
  _init() {
    this._bind();
    this._render();
    this._stylize();
    this._listen();
  }
  _bind() {}
  _render() {
    this.dom.$select.selectize({
      valueField : `id`,
      labelField : `fullName`,
      searchField: `fullName`,
      placeholder: `Введите ФИО...`,
      create     : true,
      load: (query, callback) => {
        if (query.length < this.minFullNameLength) return callback();
        $.ajax({
          dataType: `json`,
          method  : `GET`,
          url     : this.urls.load.replace(`{search}`, query),
        })
        .done((res) => {
          callback(res.data.simpleList.items.slice(0, this.numberOfListItems));
        })
        .fail(() => {
          callback();
        })
      },
      score: (search) => (item) => {
        if (search.length < this.minFullNameLength) {
          return 0;
        }
        return item.fullName.toLowerCase().startsWith(search.toLowerCase()) ? 1 : 0;
      },
      onChange: function(value) {
        if (value === ``) {
          this.clearOptions(true);
        }
      },
      render: {
        option: (item, escape) => `
<div class="natural-person-item">
  <span>${escape(item.fullName)}</span>&nbsp;
  <span class="text-secondary fs-6">
    ${item.bornAt ? `<span><i class="bi-balloon"></i>${escape(item.bornAt)}</span>` : ``} 
  </span>
</div>
        `,
        option_create: (data, escape) => `
<div class="create-natural-person">Создать <strong>${escape(data.input)}</strong>&hellip;</div>
        `
      },
    });
  }
  _stylize() {
    $(`head`).append($(`
<style>
  .selectize-dropdown .create-natural-person {
    padding: 3px .75rem;
  }
  .selectize-dropdown .natural-person-item {
    padding-left: .75rem;
  }
</style>
    `));
  }
  _listen() {}
}
