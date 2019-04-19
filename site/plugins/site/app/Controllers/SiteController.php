<?php

namespace Flextype;

use Flextype\Component\Arr\Arr;

class SiteController extends Controller
{
   public function index($request, $response, $args) {

       // Get uri
       $uri = $args['uri'];

       // If uri is empty then it is main page else use entry uri
       if ($uri === '/') {
           $entry_uri = $this->container->get('registry')->get('settings.entries.main');
       } else {
           $entry_uri = ltrim($uri, '/');
       }

       // Get entry body
       $entry_body = $this->container->get('entries')->fetch($entry_uri);

       // is entry not found
       $is_entry_not_found = false;

       // If entry body is not false
       if ($entry_body) {

           // Get 404 page if entry is not published
           if (isset($entry_body['visibility']) && ($entry_body['visibility'] === 'draft' || $entry_body['visibility'] === 'hidden')) {

               $entry['title']       = $this->container->get('registry')->get('settings.entries.error404.title');
               $entry['description'] = $this->container->get('registry')->get('settings.entries.error404.description');
               $entry['content']     = $this->container->get('registry')->get('settings.entries.error404.content');
               $entry['template']    = $this->container->get('registry')->get('settings.entries.error404.template');

               $is_entry_not_found = true;

           } else {
               $entry = $entry_body;
           }
       } else {

           $entry['title']       = $this->container->get('registry')->get('settings.entries.error404.title');
           $entry['description'] = $this->container->get('registry')->get('settings.entries.error404.description');
           $entry['content']     = $this->container->get('registry')->get('settings.entries.error404.content');
           $entry['template']    = $this->container->get('registry')->get('settings.entries.error404.template');

            $is_entry_not_found = true;
       }

       $path = 'themes/' . $this->container->get('registry')->get('settings.theme') . '/' . (empty($entry['template']) ? 'templates/default' : 'templates/' . $entry['template']) . '.html';

       if ($is_entry_not_found) {
           return $this->view->render($response->withStatus(404), $path, ['entry' => $entry]);
       } else {
           return $this->view->render($response, $path, ['entry' => $entry]);
       }
   }

}
