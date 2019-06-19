<?php

/**
 * @package Flextype
 *
 * @author Romanenko Sergey <hello@romanenko.digital>
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Flextype\Component\Arr\Arr;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SiteController extends Controller
{
    /**
     * Current entry data array
     *
     * @var array
     * @access private
     */
    public $entry = [];

    /**
     * Index page
     *
     * @param Request  $request  PSR7 request
     * @param Response $response PSR7 response
     * @param array $args Args
     * @return Response
     */
   public function index(Request $request, Response $response, array $args) {

       // Get Query Params
       $query = $request->getQueryParams();

       // Get uri
       $uri = $args['uri'];

       // If uri is empty then it is main page else use entry uri
       if ($uri === '/') {
           $entry_uri = $this->registry->get('settings.entries.main');
       } else {
           $entry_uri = ltrim($uri, '/');
       }

       // Get entry body
       $entry_body = $this->entries->fetch($entry_uri);

       // is entry not found
       $is_entry_not_found = false;

       // If entry body is not false
       if ($entry_body) {

           // Get 404 page if entry is not published
           if (isset($entry_body['visibility']) && ($entry_body['visibility'] === 'draft' || $entry_body['visibility'] === 'hidden')) {

               $entry['title']       = $this->registry->get('settings.entries.error404.title');
               $entry['description'] = $this->registry->get('settings.entries.error404.description');
               $entry['content']     = $this->registry->get('settings.entries.error404.content');
               $entry['template']    = $this->registry->get('settings.entries.error404.template');

               $is_entry_not_found = true;

           } else {
               $entry = $entry_body;
           }
       } else {

           $entry['title']       = $this->registry->get('settings.entries.error404.title');
           $entry['description'] = $this->registry->get('settings.entries.error404.description');
           $entry['content']     = $this->registry->get('settings.entries.error404.content');
           $entry['template']    = $this->registry->get('settings.entries.error404.template');

            $is_entry_not_found = true;
       }

       // Set entry
       $this->entry = $entry;

       // Run event onSiteEntryAfterInitialized
       $this->emitter->emit('onSiteEntryAfterInitialized');

       // Set template path for current entry
       $path = 'themes/' . $this->registry->get('settings.theme') . '/' . (empty($this->entry['template']) ? 'templates/default' : 'templates/' . $this->entry['template']) . '.html';

       if ($is_entry_not_found) {
           return $this->view->render($response->withStatus(404), $path, ['entry' => $this->entry]);
       } else {
           return $this->view->render($response, $path, ['entry' => $this->entry, 'page' => ($query['page']) ?? '']);
       }
   }

}
