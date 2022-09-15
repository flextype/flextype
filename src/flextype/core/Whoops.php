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

use Psr\Http\Message\ServerRequestInterface;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\XmlResponseHandler;
use Whoops\Run as WhoopsRun;
use Whoops\Util\Misc;

use function array_merge;
use function method_exists;

class Whoops
{
    protected $settings = [];
    protected $request  = null;
    protected $handlers = [];

    /**
     * Instance the whoops guard object
     *
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = array_merge([
            'enable' => true,
            'editor' => 'vscode',
            'title'  => 'Error!',
            'hadler' => 'plain',
        ], $settings);
    }

    /**
     * Set the server request object
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Set the custom handlers for whoops
     *
     * @param array $handlers
     */
    public function setHandlers(array $handlers): void
    {
        $this->handlers = $handlers;
    }

    /**
     * Install the whoops guard object
     */
    public function install(): ?WhoopsRun
    {
        if ($this->settings['enable'] === false) {
            return null;
        }

        // Set Whoops to default exception handler
        $whoops = new WhoopsRun();

        switch ($this->settings['handler']) {
            case 'json':
                $whoops->pushHandler(new JsonResponseHandler());
                break;

            case 'xml':
                $whoops->pushHandler(new XmlResponseHandler());
                break;

            case 'plain':
                $whoops->pushHandler(new PlainTextHandler());
                break;

            case 'pretty':
            default:
                $prettyPageHandler = new PrettyPageHandler();

                if (empty($this->settings['editor']) === false) {
                    $prettyPageHandler->setEditor($this->settings['editor']);
                }

                if (empty($this->settings['title']) === false) {
                    $prettyPageHandler->setPageTitle($this->settings['title']);
                }

                // Add more information to the PrettyPageHandler
                $contentCharset = '<none>';
                if (
                    method_exists($this->request, 'getContentCharset') === true &&
                    $this->request->getContentCharset() !== null
                ) {
                    $contentCharset = $this->request->getContentCharset();
                }

                $prettyPageHandler->addDataTable('Flextype', [
                    'Version'         => Flextype::VERSION,
                    'Accept Charset'  => $this->request->getHeader('ACCEPT_CHARSET') ?: '<none>',
                    'Content Charset' => $contentCharset,
                    'HTTP Method'     => $this->request->getMethod(),
                    'Path'            => $this->request->getUri()->getPath(),
                    'Query String'    => $this->request->getUri()->getQuery() ?: '<none>',
                    'Base URL'        => (string) $this->request->getUri(),
                    'Scheme'          => $this->request->getUri()->getScheme(),
                    'Port'            => $this->request->getUri()->getPort(),
                    'Host'            => $this->request->getUri()->getHost(),
                ]);

                $whoops->pushHandler($prettyPageHandler);
                break;
        }

        // Enable JsonResponseHandler when request is AJAX
        if (Misc::isAjaxRequest() === true) {
            $whoops->pushHandler(new JsonResponseHandler());
        }

        // Add each custom handler to whoops handler stack
        if (empty($this->handlers) === false) {
            foreach ($this->handlers as $handler) {
                $whoops->pushHandler($handler);
            }
        }

        $whoops->register();

        return $whoops;
    }
}
