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

namespace Flextype\Serializers;

use Symfony\Component\VarExporter\VarExporter;

use RuntimeException;
use Exception;

use function Flextype\cache;
use function Flextype\registry;
use function Glowy\Strings\strings;
use function var_export;

class PhpArray
{
    /**
     * Returns the PhpArray representation of a value.
     *
     * @param mixed $input The PHP value.
     *
     * @return string A PhpArray string representing the original PHP value.
     */
    public function encode($input): string
    {
        $wrap = registry()->get('flextype.settings.serializers.phparray.encode.wrap');

        try {
            if ($wrap) {
                $data = "<?php\n return " . VarExporter::export($input) . ";\n";
            } else {
                $data = VarExporter::export($input);
            }
        } catch (Exception $e) {
            throw new RuntimeException('Encoding PhpArray failed');
        }

        return $data;
    }

    /**
     * Takes a PhpArray encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing PhpArray.
     *
     * @return mixed The PhpArray converted to a PHP value.
     */
    public function decode(string $input)
    {
        $cache = registry()->get('flextype.settings.serializers.phparray.decode.cache.enabled');

        $decode = static function (string $input) {
            try {
                $value = include $input;
            } catch (Exception $e) {
                throw new RuntimeException('Decoding PhpArray failed');
            }

            return $value;
        };

        if ($cache === true && registry()->get('flextype.settings.cache.enabled') === true) {
            $key = $this->getCacheID($input);

            if ($dataFromCache = cache()->get($key)) {
                return $dataFromCache;
            }

            $data = $decode($input);
            cache()->set($key, $data);

            return $data;
        }

        return $decode($input);
    }

    /**
     * Get Cache ID for phparray.
     *
     * @param  string $input  Input.
     * @param  string $string String to append to the Cache ID.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input, string $string = ''): string
    {
        return strings('phparray' . $input . $string . registry()->get('flextype.settings.serializers.phparray.decode.cache.string'))->hash()->toString();
    }
}
