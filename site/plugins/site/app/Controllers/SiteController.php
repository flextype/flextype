<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function ltrim;

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
     * @param array    $args     Args
     */
    public function index(Request $request, Response $response, array $args) : Response
    {
        // Get Query Params
        $query = $request->getQueryParams();

        // Get uri
        $uri = $args['uri'];

        // Is JSON Format
        $is_json = (isset($query['format']) && $query['format'] == 'json') ? true : false;

        // If uri is empty then it is main entry else use entry uri
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
            // Get 404 page if entry visibility is draft or hidden and if routable is false
            if ((isset($entry_body['visibility']) && ($entry_body['visibility'] === 'draft' || $entry_body['visibility'] === 'hidden')) ||
                (isset($entry_body['routable']) && ($entry_body['routable'] === false))) {
                $entry = $this->error404();
                $is_entry_not_found = true;
            } else {
                $entry = $entry_body;
            }
        } else {
            $entry = $this->error404();
            $is_entry_not_found = true;
        }

        // Set entry
        $this->entry = $entry;

        // Run event onSiteEntryAfterInitialized
        $this->emitter->emit('onSiteEntryAfterInitialized');

        // Return in JSON Format
        if ($is_json) {
            if ($is_entry_not_found) {
                return $response->withJson($this->entry, 404);
            }

            return $response->withJson($this->entry);
        }

        // Set template path for current entry
        $path = 'themes/' . $this->registry->get('settings.theme') . '/' . (empty($this->entry['template']) ? 'templates/default' : 'templates/' . $this->entry['template']) . '.html';

        if ($is_entry_not_found) {
            return $this->view->render($response->withStatus(404), $path, ['entry' => $this->entry, 'query' => $query]);
        }

        return $this->view->render($response, $path, ['entry' => $this->entry, 'query' => $query]);
    }

    /**
     * Error404 page
     *
     * @return array The 404 error entry array data.
     *
     * @access public
     */
     public function error404()
     {
         return [
                  'title'       => $this->registry->get('settings.entries.error404.title'),
                  'description' => $this->registry->get('settings.entries.error404.description'),
                  'content'     => $this->registry->get('settings.entries.error404.content'),
                  'template'    => $this->registry->get('settings.entries.error404.template')
                ];
     }
}
