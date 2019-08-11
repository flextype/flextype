<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Slim\Csrf\Guard;
use Twig_Extension;
use Twig_Extension_GlobalsInterface;
use Twig_SimpleFunction;

class CsrfTwigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
{
    /** @var Guard */
    protected $csrf;

    /**
     * Constructor
     */
    public function __construct(Guard $csrf)
    {
        $this->csrf = $csrf;
    }

    /**
     * Register Global variables in an extension
     */
    public function getGlobals()
    {
        // CSRF token name and value
        $csrfNameKey  = $this->csrf->getTokenNameKey();
        $csrfValueKey = $this->csrf->getTokenValueKey();
        $csrfName     = $this->csrf->getTokenName();
        $csrfValue    = $this->csrf->getTokenValue();

        return [
            'csrf'   => [
                'keys' => [
                    'name'  => $csrfNameKey,
                    'value' => $csrfValueKey,
                ],
                'name'  => $csrfName,
                'value' => $csrfValue,
            ],
        ];
    }

    public function getName()
    {
        return 'slim/csrf';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('csrf', [$this, 'csrf'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * CSRF
     */
    public function csrf() : string
    {
        return '<input type="hidden" name="' . $this->csrf->getTokenNameKey() . '" value="' . $this->csrf->getTokenName() . '">' .
               '<input type="hidden" name="' . $this->csrf->getTokenValueKey() . '" value="' . $this->csrf->getTokenValue() . '">';
    }
}
