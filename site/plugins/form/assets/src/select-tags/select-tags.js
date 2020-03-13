$('.js-select-tags').select2({tags: true, multiple: true, tokenSeparators: [',', ' ']});
$('.js-select-tags').on('change', function (e) {
    $('.js-select-tags').each(function() {
        var name = $(this).attr('data-name');
        $('input[name='+name+']').val($(this).val().toString());
    });
});
