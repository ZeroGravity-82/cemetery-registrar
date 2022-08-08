const $body    = $(`body`);
const $spinner = $(`#spinner-div`);

$(document).ready(() => {
  fireFlashMessages();
});

// -------------------------------------------- Flash messages ---------------------------------------------------------
function fireFlashMessages()
{
  $(`.js-flash-message`).each((index, flashMessage) => {
    const $flashMessage = $(flashMessage);
    const flashLabel    = $flashMessage.data(`flash-label`);
    const flashText     = $flashMessage.html();
    buildToast().fire({
      icon: flashLabel,
      title: flashText,
    });
  });
}

// ------------------------------------------------- Forms -------------------------------------------------------------
$(`form`).on(`submit`, (e) => e.preventDefault());

// --------------------------------------------- Notifications ---------------------------------------------------------
function buildToast() {
  return Swal.mixin({
    toast: true,
    position: `top-end`,
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
      toast.addEventListener(`mouseenter`, Swal.stopTimer)
      toast.addEventListener(`mouseleave`, Swal.resumeTimer)
    },
  })
}

function onAjaxAlways() {
  $spinner.hide();
}
