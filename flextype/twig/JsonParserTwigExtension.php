<?php

declare(strict_types=1);

/**
 * @link http://romanenko.digital
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flextype;

use Twig_Extension;
use Twig_SimpleFunction;

class JsonParserTwigExtension extends Twig_Extension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new Twig_SimpleFunction('json_parser_decode', [$this, 'decode']),
            new Twig_SimpleFunction('json_parser_encode', [$this, 'encode']),
        ];
    }

    /**
     * Encode JSON
     */
    public function encode($input, int $encode_options = 0, int $encode_depth = 512) : string
    {
        return JsonParser::encode($input, $encode_options, $encode_depth);
    }

    /**
     * Decode JSON
     */
    public function decode(string $input, bool $decode_assoc = true, int $decode_depth = 512, int $decode_options = 0)
    {
        return JsonParser::decode($input, $decode_assoc, $decode_depth, $decode_options);
    }
}
