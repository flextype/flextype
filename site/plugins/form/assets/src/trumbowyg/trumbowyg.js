$.trumbowyg.svgPath = $('input[name=trumbowyg-icons-path]').val();
$('.js-html-editor').trumbowyg({
    btnsDef: {
        // Customizables dropdowns
        image: {
            dropdown: [
                'insertImage', 'noembed'
            ],
            ico: 'insertImage'
        }
    },
    btns: [
        [
            'undo', 'redo'
        ], // Only supported in Blink browsers
        ['formatting'],
        [
            'strong', 'em', 'del'
        ],
        ['link'],
        ['image'],
        [
            'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'
        ],
        [
            'unorderedList', 'orderedList'
        ],
        ['table'],
        ['removeformat'],
        ['fullscreen'],
        ['viewHTML']
    ],
    lang: $('input[name=trumbowyg-locale]').val(),
    autogrow: false,
    removeformatPasted: true
});
