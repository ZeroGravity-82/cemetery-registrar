const $customerTypeSelector = $('.js-customer-type-selector');
const naturalPersonValue = $customerTypeSelector.data('natural-person-value');

const $customerTypeSelector = $('.js-customer-type-selector input:radio[name=customerType]');
if($customerTypeSelector.is(':checked') === false) {
    $customerTypeSelector.filter('[value=Male]').prop('checked', true);
}
