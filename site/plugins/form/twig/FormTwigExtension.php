<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;

class FormTwigExtension extends Twig_Extension
{
    /**
     * Flextype Dependency Container
     */
    private $flextype;

    /**
     * Constructor
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('form_render', [$this, 'form_render'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Form Render
     */
    public function form_render(array $fieldset, array $values = []) : string
    {
        return $this->flextype->FormController->render($fieldset, $values);
    }
}
