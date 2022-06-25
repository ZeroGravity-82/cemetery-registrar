const $customerTypeSelectorWrapper            = $(`#customerTypeSelectorWrapper`);
const naturalPersonTypeValue                  = $customerTypeSelectorWrapper.data(`natural-person-value`);
const juristicPersonTypeValue                 = $customerTypeSelectorWrapper.data(`juristic-person-value`);
const soleProprietorTypeValue                 = $customerTypeSelectorWrapper.data(`sole-proprietor-value`);
const $customerTypeSelector                   = $customerTypeSelectorWrapper.find(`input:radio[name=customerType]`);
const $customerNaturalPersonSubform           = $(`#customerNaturalPersonSubform`);
const $customerJuristicPersonSubform          = $(`#customerJuristicPersonSubform`);
const $customerSoleProprietorSubform          = $(`#customerSoleProprietorSubform`);
const $personInChargeIsSameAsCustomerCheckbox = $(`#personInChargeIsSameAsCustomer`);
const $personInChargeSubform                  = $(`#personInChargeSubform`);

// Initialize customer type selector
if ($customerTypeSelector.is(`:checked`) === false) {
    $customerTypeSelector.filter(`[value=${naturalPersonTypeValue}]`).prop(`checked`, true);
}
$customerNaturalPersonSubform.removeClass(`d-none`);

// Switch customer type
$customerTypeSelector.on(`change`, function (event) {
    $customerNaturalPersonSubform.removeClass(`d-none`).addClass(`d-none`);
    $customerJuristicPersonSubform.removeClass(`d-none`).addClass(`d-none`);
    $customerSoleProprietorSubform.removeClass(`d-none`).addClass(`d-none`);

    const $target                   = $(event.target);
    const customerTypeSelectorValue = $target.val();
    switch (customerTypeSelectorValue) {
        case naturalPersonTypeValue:
            $customerNaturalPersonSubform.removeClass(`d-none`);
            resetSubform($customerJuristicPersonSubform);
            resetSubform($customerSoleProprietorSubform);
            break;
        case juristicPersonTypeValue:
            $customerJuristicPersonSubform.removeClass(`d-none`);
            resetSubform($customerNaturalPersonSubform);
            resetSubform($customerSoleProprietorSubform);
            break;
        case soleProprietorTypeValue:
            $customerSoleProprietorSubform.removeClass(`d-none`);
            resetSubform($customerNaturalPersonSubform);
            resetSubform($customerJuristicPersonSubform);
            break;
    }
    updatePersonInChargeSubform(customerTypeSelectorValue);
});

// Initialize "the same as the customer" checkbox for person in charge sub-form
$personInChargeIsSameAsCustomerCheckbox.prop(`checked`, true);

// Switch "the same as the customer" checkbox for person in charge sub-form
$personInChargeIsSameAsCustomerCheckbox.on(`change`, function (event) {
    $personInChargeSubform.removeClass(`d-none`).addClass(`d-none`);
    resetSubform($personInChargeSubform);

    const $target          = $(event.target);
    const isSameAsCustomer = $target.prop(`checked`);
    if (!isSameAsCustomer) {
        $personInChargeSubform.removeClass(`d-none`);
    }
});

// Update person in charge sub-form depending on customer type selector value
function updatePersonInChargeSubform(customerTypeSelectorValue) {
    const isSameAsCustomer = $personInChargeIsSameAsCustomerCheckbox.prop(`checked`);
    switch (customerTypeSelectorValue) {
        case naturalPersonTypeValue:
            $personInChargeIsSameAsCustomerCheckbox.prop(`disabled`, false);
            break;
        case juristicPersonTypeValue:
        case soleProprietorTypeValue:
            if (isSameAsCustomer) {
                $personInChargeIsSameAsCustomerCheckbox.prop(`checked`, false);
            }
            $personInChargeIsSameAsCustomerCheckbox.prop(`disabled`, true);
            break;
    }
}

// Utilities
function resetSubform($subform)
{
    $subform.find(`input`).val(null);
}
function disableSubform($subform)
{
    $subform.find(`input`).prop(`disabled`, true);
}
