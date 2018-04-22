<script src="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/jquery/dist/jquery.slim.min.js"></script>
<script src="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
<script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
<script src="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/codemirror/lib/codemirror.js"></script>
<link rel="stylesheet" href="<?php echo Flextype\Component\Http\Http::getBaseUrl(); ?>/site/plugins/admin/node_modules/codemirror/lib/codemirror.css">
<script>
    var simplemde = new SimpleMDE({ element: $("#editor")[0] });

    $(document).ready(function() {
          var editor = CodeMirror.fromTextArea(document.getElementById("frontmatter"), {
              lineNumbers: false,
              styleActiveLine: true,
              matchBrackets: true,
              viewportMargin: Infinity,
              indentUnit: 4,
              mode:  "'.$mode.'",
              indentWithTabs: true,
              theme: "'.CodeMirror::$theme.'"
          });
      });

</script>
<?php Flextype\Events::dispatch('onAdminThemeFooter'); ?>
