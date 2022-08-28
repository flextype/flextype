<?php

declare(strict_types=1);

 /**
 * Flextype - Hybrid Content Management System with the freedom of a headless CMS
 * and with the full functionality of a traditional CMS!
 *
 * Copyright (c) Sergey Romanenko (https://awilum.github.io)
 *
 * Licensed under The MIT License.
 *
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 */

namespace Flextype;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use function Glowy\Filesystem\filesystem;
use function Glowy\Strings\strings;
use function function_exists;


if (! function_exists('fetch')) {
    /**
     * Fetch data from entries, local files and urls.
     *
     * @param string $resource A resource that you wish to fetch.
     * @param array $options Options.
     * @return Glowy\Arrays\Arrays|GuzzleHttp\Psr7\Response Returns the data from the resource or empty collection on failure.
     */
    function fetch(string $resource, array $options = [])
    {  
        $result = collection();   
        
        // 1. Try to fetch data for the entries.
        if (entries()->has($resource)) {
            return entries()->fetch($resource, $options);
        }

        // 2. Try to fetch data from the local files.
        if (! strings($resource)->isUrl() && filesystem()->file($resource)->exists()) {
            $result = filesystem()->file($resource)->get();
            switch (filesystem()->file($resource)->extension()) {
                case 'yaml':
                case 'yml':
                    $result = serializers()->yaml()->decode($result);
                    break;
        
                case 'php':
                    $result = serializers()->phparray()->decode($result);
                    break;

                case 'neon':
                    $result = serializers()->neon()->decode($result);
                    break;
                
                case 'md':
                    $result = serializers()->frontmatter()->decode($result);
                    break;
                    
                case 'json5':
                    $result = serializers()->json5()->decode($result);
                    break;
                    
                case 'json':
                default:
                    $result = strings($result)->isJson() ? serializers()->json()->decode($result) : $result;
                    break;
            }
            return collection($result);
        }

        // 3. Try to fetch data from the url.
        if (strings($resource)->isUrl() || isset($options['base_uri'])) {
            $client = new Client($options);
            $request = new Request($options['method'] ?? 'GET', $resource);

            if (isset($options['async'])) {
                $result = $client->sendAsync($request, $options);
            } else {
                if (isset($options['response'])) {
                    $result = $client->send($request, $options);
                } else {
                    $response = $client->send($request, $options);
                    $body = $response->getBody()->getContents();
                    $result = collection(['reasonPhrase' => $response->getReasonPhrase(),
                                        'statusCode' => $response->getStatusCode(),
                                        'headers' => $response->getHeaders(),
                                        'body' => strings($body)->isJson() ? serializers()->json()->decode($body) : $body]);
                }
            }
        }

        return $result;
    }
}
