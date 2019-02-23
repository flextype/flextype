<?php

namespace Flextype;

use Slim\Http\Request;
use Slim\Http\Response;

use Flextype\Component\Arr\Arr;
use Flextype\Component\Event\Event;
use Flextype\Component\Registry\Registry;

/**
 * Define site plugin routes
 */
$app->get('{uri:.+}', function (Request $request, Response $response, array $args) {

    // Get uri
    $uri = $args['uri'];

    // If uri is empty then it is main page else use entry uri
    if ($uri === '/') {
        $entry_uri = Registry::get('settings.entries.main');
    } else {
        $entry_uri = ltrim($uri, '/');
    }

    // Get entry body
    $entry_body = $this->get('entries')->fetch($entry_uri);

    // If entry body is not false
    if ($entry_body) {

        // Get 404 page if entry is not published
        if (isset($entry_body['visibility']) && ($entry_body['visibility'] === 'draft' || $entry_body['visibility'] === 'hidden')) {

            //Http::setResponseStatus(404);

            $entry['title']       = Registry::get('settings.entries.error404.title');
            $entry['description'] = Registry::get('settings.entries.error404.description');
            $entry['content']     = Registry::get('settings.entries.error404.content');
            $entry['template']    = Registry::get('settings.entries.error404.template');

            //$response->withStatus(404);

        } else {
            $entry = $entry_body;
        }
    } else {

        //Http::setResponseStatus(404);
        //$response->withStatus(404);

        $entry['title']       = Registry::get('settings.entries.error404.title');
        $entry['description'] = Registry::get('settings.entries.error404.description');
        $entry['content']     = Registry::get('settings.entries.error404.content');
        $entry['template']    = Registry::get('settings.entries.error404.template');
    }

    $path = 'themes/' . Registry::get('settings.theme') . '/' . (empty($entry['template']) ? 'templates/default' : 'templates/' . $entry['template']) . '.html';

    return $this->view->render($response,
                               $path, [
        'entry' => $entry,
        'registry' => Registry::registry()
    ]);
});
