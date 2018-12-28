<?php

namespace Flextype;

use Flextype\Component\Http\Http;

class DashboardManager
{
    public static function getDashboardManager()
    {
        Http::redirect(Http::getBaseUrl().'/admin/entries');
    }
}
