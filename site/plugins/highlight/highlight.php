<?php namespace Flextype;

/**
 *
 * Highlight Plugin for Flextype
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

 //
 // Add listner for onThemeHeader event
 //
Events::addListener('onThemeHeader', function () {
    echo('<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/'.Config::get('plugins.highlight.theme').'.min.css">');
});

 //
 // Add listner for onThemeFooter event
 //
Events::addListener('onThemeFooter', function () {
    echo('<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>
          <script>hljs.initHighlightingOnLoad();</script>');
});
