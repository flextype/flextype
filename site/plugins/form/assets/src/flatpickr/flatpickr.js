flatpickr(".js-datetimepicker", {
    enableTime: true,
    dateFormat: $('input[name=flatpickr-date-format]').val(),
    locale: $('input[name=flatpickr-locale]').val()
});
