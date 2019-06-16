<?php

namespace Flextype;

/**
 * Add site controller to Flextype container
 */
$flextype['SiteController'] = function($container) {
    return new SiteController($container);
};
