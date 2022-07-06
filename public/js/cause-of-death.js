const $body              = $(`body`);
const $modalCauseOfDeath = $(`#modalCauseOfDeath`);
const $modalTitle        = $modalCauseOfDeath.find(`.modal-title`)
const $modalNameField    = $modalCauseOfDeath.find(`input[id=name]`);
const $modalTimestamps   = $modalCauseOfDeath.find(`.timestamps`);

const modalCauseOfDeath  = new bootstrap.Modal(`#modalCauseOfDeath`, {});

$body.on(`click`, `.js-create-cause-of-death-btn`, function() {
    $modalCauseOfDeath.removeClass(`edit-form`);
    $modalTimestamps.removeClass(`d-none`).addClass(`d-none`);
    $modalTitle.html(`Причины смерти (создание)`);
    $modalNameField.val(null);
    modalCauseOfDeath.show();
});

$body.on(`click`, `tr`, function(e) {
    const $tr = $(e.target).closest(`tr`);
    const id  = $tr.attr(`data-id`);
    $.ajax({
        type: `GET`,
        url: `/admin/cause-of-death/edit/${id}`,
        success: function (causeOfDeathView) {
            $modalCauseOfDeath.removeClass(`edit-form`).addClass(`edit-form`);
            $modalTimestamps.removeClass(`d-none`);
            $modalTitle.html(`${causeOfDeathView.name} (Причины смерти)`);
            $modalNameField.val(causeOfDeathView.name);
            modalCauseOfDeath.show();
        }
    });
});

$(document).ready(function(){
    $("#modalCauseOfDeath").on('shown.bs.modal', function () {
        $(this).find('#name').select();
    });
});
