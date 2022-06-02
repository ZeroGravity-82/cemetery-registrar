const $customerTypeSelectorWrapper = $(`.js-customer-type-selector-wrapper`);
const naturalPersonTypeValue       = $customerTypeSelectorWrapper.data(`natural-person-value`);
const juristicPersonTypeValue      = $customerTypeSelectorWrapper.data(`juristic-person-value`);
const soleProprietorTypeValue      = $customerTypeSelectorWrapper.data(`sole-proprietor-value`);
const $customerTypeSelector        = $customerTypeSelectorWrapper.find(`input:radio[name=customerType]`);
const $naturalPersonSubform        = $(`.js-natural-person-subform`);
const $juristicPersonSubform       = $(`.js-juristic-person-subform`);
const $soleProprietorSubform       = $(`.js-sole-proprietor-subform`);

// Initialize customer type selector
if($customerTypeSelector.is(`:checked`) === false) {
    $customerTypeSelector.filter(`[value=${naturalPersonTypeValue}]`).prop(`checked`, true);
}
$naturalPersonSubform.removeClass(`d-none`);

// Switch customer type
$customerTypeSelector.on(`change`, function (event) {
    $naturalPersonSubform.removeClass(`d-none`).addClass(`d-none`);
    $juristicPersonSubform.removeClass(`d-none`).addClass(`d-none`);
    $soleProprietorSubform.removeClass(`d-none`).addClass(`d-none`);

    const $target                   = $(event.target);
    const customerTypeSelectorValue = $target.val();
    switch (customerTypeSelectorValue) {
        case naturalPersonTypeValue:
            $naturalPersonSubform.removeClass(`d-none`);
            resetSubform($juristicPersonSubform);
            resetSubform($soleProprietorSubform);
            break;
        case juristicPersonTypeValue:
            $juristicPersonSubform.removeClass(`d-none`);
            resetSubform($naturalPersonSubform);
            resetSubform($soleProprietorSubform);
            break;
        case soleProprietorTypeValue:
            $soleProprietorSubform.removeClass(`d-none`);
            resetSubform($naturalPersonSubform);
            resetSubform($juristicPersonSubform);
            break;
    }
});

function resetSubform($subform)
{
    $subform.find(`input`).val(null);
}
