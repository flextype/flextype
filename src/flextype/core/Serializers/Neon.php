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

use Nette\Neon\Exception;
use Nette\Neon\Neon as NeonSerializer;
use RuntimeException;

use function Flextype\cache;
use function Flextype\registry;
use function Glowy\Strings\strings;

class Neon
{
    /**
     * Returns the NEON representation of a value.
     *
     * @param mixed $input The PHP value.
     *
     * @return string A NEON string representing the original PHP value.
     */
    public function encode(mixed $input): string
    {
        $blockMode   = registry()->get('flextype.settings.serializers.neon.encode.blockMode');
        $indentation = registry()->get('flextype.settings.serializers.neon.encode.indentation');

        try {
            $neon = NeonSerializer::encode($input, $blockMode, $indentation);
        } catch (Exception $e) {
            throw new RuntimeException('Encoding NEON failed');
        }

        return $neon;
    }

    /**
     * Takes a NEON encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing NEON.
     *
     * @return mixed The NEON converted to a PHP value.
     */
    public function decode(string $input): mixed
    {
        $cache = registry()->get('flextype.settings.serializers.neon.decode.cache.enabled');

        $decode = static function (string $input) {
            try {
                $value = NeonSerializer::decode($input);
            } catch (Exception $e) {
                throw new RuntimeException('Decoding NEON failed');
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
     * Get Cache ID for neon.
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
        return strings('neon' . $input . $string . registry()->get('flextype.settings.serializers.neon.decode.cache.string'))->hash()->toString();
    }
}
