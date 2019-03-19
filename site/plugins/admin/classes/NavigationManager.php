<?php

namespace Flextype;

use Flextype\Component\Registry\Registry;

class NavigationManager
{
    public static function addItem(string $area, string $item, string $title, string $link, array $attributes = []) : void
    {
        $flextype->registry->set("admin_navigation.{$area}.{$item}.area", $area);
        $flextype->registry->set("admin_navigation.{$area}.{$item}.item", $item);
        $flextype->registry->set("admin_navigation.{$area}.{$item}.title", $title);
        $flextype->registry->set("admin_navigation.{$area}.{$item}.link", $link);
        $flextype->registry->set("admin_navigation.{$area}.{$item}.attributes", $attributes);
    }

    public static function getItems(string $area)
    {
        return Registry::get("admin_navigation.{$area}");
    }
}
