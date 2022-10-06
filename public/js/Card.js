`use strict`;

class Card {
  _stylize() {
    $(`head`).append($(`
<style>
  li.dropdown-item {
    cursor: pointer;
}
</style>
    `));
  }
}
