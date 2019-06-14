<?php

namespace Flextype;

$app->get('{uri:.+}', 'SiteController:index')->setName('site.index');
