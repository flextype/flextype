<?php

declare(strict_types=1);

/**
 * Flextype (https://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Serializers;

use Symfony\Component\VarExporter\VarExporter;

use RuntimeException;

use function cache;
use function error_get_last;
use function error_reporting;
use function registry;
use function strings;
use function var_export;

use const E_ALL;

class PhpCode
{
    /**
     * Returns the PhpCode representation of a value.
     *
     * @param mixed $input The PHP value.
     *
     * @return string A PhpCode string representing the original PHP value.
     */
    public function encode($input): string
    {
        $wrap = registry()->get('flextype.settings.serializers.phpcode.encode.wrap');

        try {
            if ($wrap) {
                $data = "<?php\n return " . VarExporter::export($input) . ";\n";
            } else {
                $data = VarExporter::export($input);
            }
        } catch (Exception $e) {
            throw new RuntimeException('Encoding PhpCode failed');
        }

        return $data;
    }

    /**
     * Takes a PhpCode encoded string and converts it into a PHP variable.
     *
     * @param string $input A string containing PhpCode.
     *
     * @return mixed The PhpCode converted to a PHP value.
     */
    public function decode(string $input)
    {
        $cache = registry()->get('flextype.settings.serializers.phpcode.decode.cache');

        $decode = static function (string $input) {
            $currentErrorLevel = error_reporting();
            error_reporting(E_ALL);
            $return = null;
            $eval   = @eval('$return=' . $input . ';');
            $error  = error_get_last();
            error_reporting($currentErrorLevel);

            if ($eval === false || $error) {
                $msg = 'Decoding PhpCode failed';

                if ($eval === false) {
                    $lastError = error_get_last();
                    $msg      .= ': ' . $lastError['message'];
                }

                throw new RuntimeException($msg, 0);
            }

            return $return;
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
     * Get Cache ID for phpcode.
     *
     * @param  string $input Input.
     *
     * @return string Cache ID.
     *
     * @access public
     */
    public function getCacheID(string $input): string
    {
        return strings('phpcode' . $input)->hash()->toString();
    }
}
