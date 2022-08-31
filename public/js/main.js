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

function displayValidationErrors(data) {
  const $modal = $(`.modal`).has(`:visible`);
  for (const [fieldId, validationError] of Object.entries(data)) {
    const $field = $modal.find(`#${fieldId}`);
    if ($field.length === 0) {
      buildToast().fire({
        icon: `error`,
        title: validationError,
      });
      continue;
    }

    $field.removeClass(`is-invalid`).addClass(`is-invalid`);
    const ariaDescribedby  = $field.attr(`aria-describedby`);
    const $invalidFeedback = $modal.find(`#${ariaDescribedby}`);
    $invalidFeedback.html(validationError);
    $invalidFeedback.removeClass(`d-none`);
  }
}

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

// ----------------------------------------- Application services ------------------------------------------------------
function onAjaxFailure(jqXHR) {
  if (jqXHR.responseText === undefined) {
    buildToast().fire({
      icon: `error`,
      title: `Сервер не отвечает.`,
    });
    return;
  }
  const responseJson = JSON.parse(jqXHR.responseText);
  switch (responseJson.status) {
    case `fail`:
      processApplicationFailResponse(responseJson);
      break;
    case `error`:
      processApplicationErrorResponse(responseJson);
      break;
    default:
      throw `Неподдерживаемый статус ответа прикладного сервиса: "${responseJson.status}".`;
  }
}

function processApplicationFailResponse(responseJson) {
  const failType = responseJson.data.failType;
  switch (failType) {
    case `VALIDATION_ERROR`:
      delete responseJson.data.failType;
      displayValidationErrors(responseJson.data)
      break;
    case `NOT_FOUND`:
    case `DOMAIN_EXCEPTION`:
      buildToast().fire({
        icon: `warning`,
        title: responseJson.data.message,
      })
      break;
    default:
      throw `Неподдерживаемый тип отказа выполнения запроса прикладного сервиса: "${failType}".`;
  }
}

function processApplicationErrorResponse(responseJson) {
  buildToast().fire({
    icon: `error`,
    title: responseJson.message,
  });
}
