<?php

declare(strict_types=1);

test('[if] shortcode', function () {
    expect(parsers()->shortcodes()->parse('[if val1="2" operator="lt" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2" operator="<" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="5" operator="gt" val2="2"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2" operator="lte" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2" operator="<=" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="5" operator="gte" val2="2"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="5" operator=">=" val2="2"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="5" operator="eq" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="5" operator="=" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2" operator="neq" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2" operator="<>" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2" operator="!=" val2="5"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="foobarfoo" operator="contains" val2="bar"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="foobarfoo" operator="like" val2="bar"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="foobarfoo" operator="ncontains" val2="zed"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="foobarfoo" operator="nlike" val2="zed"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="foobar" operator="starts_with" val2="foo"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="foobar" operator="ends_with" val2="bar"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2020-11-12" operator="newer" val2="2020-11-11"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="2020-11-11" operator="older" val2="2020-11-12"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="PHP is the web scripting language of choice." operator="regexp" val2="php"]yes[/if]'))->toBe('yes');
    expect(parsers()->shortcodes()->parse('[if val1="PHP is the web scripting language of choice." operator="nregexp" val2="delphi"]yes[/if]'))->toBe('yes');
});
