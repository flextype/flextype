<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Twig_Extension;
use Twig_Extension_GlobalsInterface;

class FormTwigExtension extends Twig_Extension implements Twig_Extension_GlobalsInterface
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
     * Register Global variables in an extension
     */
    public function getGlobals()
    {
        return [
            'form' => new FormTwig($this->flextype),
        ];
    }
}

class FormTwig
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
     *
     */
    public function render(array $fieldset, array $values = []) : string
    {
        return $this->flextype['form']->render($fieldset, $values);
    }

    /**
     *
     */
    public function getElementID(string $element) : string
    {
        return $this->flextype['form']->getElementID($element);
    }

    /**
     *
     */
    public function getElementName(string $element) : string
    {
        return $this->flextype['form']->getElementName($element);
    }

    /**
     *
     */
    public function getElementValue(string $element, array $values, array $properties)
    {
        return $this->flextype['form']->getElementValue($element, $values, $properties);
    }
}
