<?php
namespace Flextype;
use Flextype\Component\Http\Http;
use Flextype\Component\Arr\Arr;
use Flextype\Component\Registry\Registry;
?>
<div class="content <?php if (in_array(Registry::get('sidebar_menu_item'), ['entries', 'templates', 'snippets', 'fieldsets', 'menus'])): ?> entry-editor <?php endif ?>">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
