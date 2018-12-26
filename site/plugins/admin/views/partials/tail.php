<?php
namespace Flextype;
use Flextype\Component\{Http\Http, Event\Event, Registry\Registry, Assets\Assets, Notification\Notification};
?>

<?php Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/js/build.min.js', 'admin', 1); ?>
<?php if (Registry::get("settings.locale") != 'en') Assets::add('js', Http::getBaseUrl() . '/site/plugins/admin/assets/dist/langs/trumbowyg/langs/'.Registry::get("settings.locale").'.min.js', 'admin', 10); ?>
<?php foreach (Assets::get('js', 'admin') as $assets_by_priorities) { foreach ($assets_by_priorities as $assets) { ?>
    <script type="text/javascript" src="<?php echo $assets['asset']; ?>"></script>
<?php } } ?>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>

<script>

    Messenger.options = {
        extraClasses: 'messenger-fixed messenger-on-bottom messenger-on-right',
        theme: 'flat'
    }

    <?php if (Notification::get('success')) { ?> Messenger().post({ type: "success", message : "<?php echo Notification::get('success'); ?>", hideAfter: '3' }); <?php } ?>
    <?php if (Notification::get('warning')) { ?> Messenger().post({ type: "warning", message : "<?php echo Notification::get('warning'); ?>", hideAfter: '3' }); <?php } ?>
    <?php if (Notification::get('error'))   { ?> Messenger().post({ type: "error", message : "<?php echo Notification::get('error'); ?>", hideAfter: '3' });     <?php } ?>

    if (typeof $.flextype == 'undefined') $.flextype = {};

    $.flextype.plugins = {

        init: function() {
            this.changeStatusProcess();
        },

        changeStatus: function(plugin, status, token) {
            $.ajax({
                type: "post",
                data: "plugin_change_status=1&plugin="+plugin+"&status="+status+"&token="+token,
                url: $('form input[name="url"]').val()
            });
        },

        changeStatusProcess: function() {
            $(".js-switch").click(function() {
                if ($(this).is(':checked')) {
                    $.flextype.plugins.changeStatus($(this).data("plugin"), "true", $(this).data("token"));
                } else {
                    $.flextype.plugins.changeStatus($(this).data("plugin"), "false", $(this).data("token"));
                }
            });
        }
    };

    $(document).ready(function() {

        $.trumbowyg.svgPath = '<?php echo Http::getBaseUrl(); ?>/site/plugins/admin/assets/dist/fonts/trumbowyg/icons.svg';

        $('.js-html-editor').trumbowyg({
            btnsDef: {
                // Customizables dropdowns
                image: {
                    dropdown: ['insertImage', 'noembed'],
                    ico: 'insertImage'
                }
            },
            btns: [
                ['undo', 'redo'], // Only supported in Blink browsers
                ['formatting'],
                ['strong', 'em', 'del'],
                ['link'],
                ['image'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['table'],
                ['removeformat'],
                ['fullscreen']
            ],
            lang: '<?php echo Registry::get("settings.locale"); ?>',
            autogrow: true,
            removeformatPasted: true
        });

        $.flextype.plugins.init();

        $('.js-save-form-submit').click(function() {
            $("#form" ).submit();
        });

        $('.navbar-toggler').click(function () {
            $('.sidebar').addClass('show-sidebar');
            $('.sidebar-off-canvas').addClass('show-sidebar-off-canvas');
        });

        $('.sidebar-off-canvas').click(function () {
            $('.sidebar').removeClass('show-sidebar');
            $('.sidebar-off-canvas').removeClass('show-sidebar-off-canvas');
        });

        $('.js-plugins-info').click(function () {
            $('#pluginInfoModal').modal();
            $('.js-plugin-name-placeholder').html($(this).attr('data-name'));
            $('.js-plugin-version-placeholder').html($(this).attr('data-version'));
            $('.js-plugin-description-placeholder').html($(this).attr('data-description'));
            $('.js-plugin-author-name-placeholder').html($(this).attr('data-author-name'));
            $('.js-plugin-author-email-placeholder').html($(this).attr('data-author-email'));
            $('.js-plugin-author-url-placeholder').html($(this).attr('data-author-url'));
            $('.js-plugin-homeentry-placeholder').html($(this).attr('data-homeentry'));
            $('.js-plugin-bugs-placeholder').html($(this).attr('data-bugs'));
            $('.js-plugin-license-placeholder').html($(this).attr('data-license'));
        });

        $('.js-entries-image-preview').click(function () {
            $('#entriesImagePreview').modal();
            $('.js-entry-image-preview-placeholder').css('background-image', 'url(' + $(this).attr('data-image-url') + ')');
            $('.js-entry-image-url-placeholder').val($(this).attr('data-image-url'));
            $('.js-entry-image-delete-url-placeholder').attr('href', $(this).attr('data-image-delete-url'));
        });

        $('.js-settings-entry-modal').click(function () {
            $('#settingsEntryModal').modal();
        });

        $.validate({});

        var editor = CodeMirror.fromTextArea(document.getElementById("codeMirrorEditor"), {
            lineNumbers: true,
            <?php if (Http::get('fieldset') || Http::get('menu')) { ?>
            indentUnit: 2,
            tabSize: 2,
            <?php } else { ?>
            tabSize: 4,
            indentUnit: 4,
            <?php } ?>
            <?php if (Http::get('fieldset') || Http::get('menu')) { ?>
            mode: "yaml",
            <?php } else { ?>
            mode: "application/x-httpd-php",
            <?php } ?>
            indentWithTabs: false,
            theme: "monokai",
            styleActiveLine: true,
        });
        
        editor.addKeyMap({
            "Tab": function (cm) {
                if (cm.somethingSelected()) {
                    var sel = editor.getSelection("\n");
                    // Indent only if there are multiple lines selected, or if the selection spans a full line
                    if (sel.length > 0 && (sel.indexOf("\n") > -1 || sel.length === cm.getLine(cm.getCursor().line).length)) {
                        cm.indentSelection("add");
                        return;
                    }
                }

                if (cm.options.indentWithTabs)
                    cm.execCommand("insertTab");
                else
                    cm.execCommand("insertSoftTab");
            },
            "Shift-Tab": function (cm) {
                cm.indentSelection("subtract");
            }
        });
    });
</script>

<?php Event::dispatch('onAdminThemeFooter'); ?>
