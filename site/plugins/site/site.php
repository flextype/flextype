<?php

namespace Flextype;

use Slim\Http\Request;
use Slim\Http\Response;

use Flextype\Component\Arr\Arr;

/**
 * Define site plugin routes
 */
$app->get('{uri:.+}', function (Request $request, Response $response, array $args) {

    // Get uri
    $uri = $args['uri'];

    // If uri is empty then it is main page else use entry uri
    if ($uri === '/') {
        $entry_uri = $this->get('registry')->get('settings.entries.main');
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

            $entry['title']       = $this->get('registry')->get('settings.entries.error404.title');
            $entry['description'] = $this->get('registry')->get('settings.entries.error404.description');
            $entry['content']     = $this->get('registry')->get('settings.entries.error404.content');
            $entry['template']    = $this->get('registry')->get('settings.entries.error404.template');

            //$response->withStatus(404);

        } else {
            $entry = $entry_body;
        }
    } else {

        //Http::setResponseStatus(404);
        //$response->withStatus(404);

        $entry['title']       = $this->get('registry')->get('settings.entries.error404.title');
        $entry['description'] = $this->get('registry')->get('settings.entries.error404.description');
        $entry['content']     = $this->get('registry')->get('settings.entries.error404.content');
        $entry['template']    = $this->get('registry')->get('settings.entries.error404.template');
    }

    $path = 'themes/' . $this->get('registry')->get('settings.theme') . '/' . (empty($entry['template']) ? 'templates/default' : 'templates/' . $entry['template']) . '.html';

    return $this->view->render($response,
                               $path, [
        'entry' => $entry,
        'registry' => $this->get('registry')->dump()
    ]);
});
