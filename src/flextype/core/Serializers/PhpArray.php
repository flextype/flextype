<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Serializers;

use RuntimeException;

use function cache;
use function registry;
use function strings;

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
        try {
            $data = "<?php\n return " . var_export($input, true) . ";\n";
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
        $cache = registry()->get('flextype.settings.serializers.phparray.decode.cache');

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
     * @param  string $input Input.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input): string
    {
        return strings('phparray' . $input)->hash()->toString();
    }
}
