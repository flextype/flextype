<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Serializers;

use Nette\Neon\Exception;
use Nette\Neon\Neon as NeonSerializer;

use function cache;
use function registry;
use function strings;

class Neon
{
    /**
     * Returns the NEON representation of a value.
     *
     * @param mixed $input The PHP value.
     *
     * @return string A NEON string representing the original PHP value.
     */
    public function encode($input): string
    {
        $flags = registry()->get('flextype.settings.serializers.neon.encode.flags');

        try {
            $neon = NeonSerializer::encode($input, $flags);
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
    public function decode(string $input)
    {
        $cache = registry()->get('flextype.settings.serializers.neon.decode.cache');

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
     * @param  string $input Input.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input): string
    {
        return strings('neon' . $input)->hash()->toString();
    }
}
