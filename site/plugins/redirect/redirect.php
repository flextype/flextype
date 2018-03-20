<?php namespace Flextype;

/**
 *
 * Redirect Plugin for Flextype
 *
 * @author Romanenko Sergey / Awilum <awilum@yandex.ru>
 * @link http://flextype.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Arr;
use Request;

//
// Add listner for onPageContentAfter event
//
Events::addListener('onPageContentAfter', function () {

    //
    // Search in frontmatter of the curent page element 'redirect'
    //
    if (Arr::keyExists(Pages::$page, 'redirect')) {
        Request::redirect(Arr::get(Pages::$page, 'redirect'));
    }

    //
    // Redirect to the custom urls on specific pages
    //
    $redirects = Config::get('site.redirects');
    if (is_array($redirects) && count($redirects) > 0) {
        foreach ($redirects as $old_url => $new_url) {
            if (Url::getUriString() == $old_url) {
                Request::redirect($new_url);
            }
        }
    }

});
