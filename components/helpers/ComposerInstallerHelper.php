<?php
/**
 * ComposerInstallerHelper class
 *
 * @author Putra Sudaryanto <putra@ommu.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2020 OMMU (www.ommu.id)
 * @created date 20 July 2020, 12:06 WIB
 * @link https://github.com/ommu/mod-core
 */

namespace ommu\core\components\helpers;

use Yii;
use thamtech\uuid\helpers\UuidHelper;

class ComposerInstallerHelper
{
    /**
     * Generates a cookie validation key for every app config listed in "config" in extra section.
     * You can provide one or multiple parameters as the configuration files which need to have validation key inserted.
     */
    public static function generateCookieValidationKey()
    {
        $configs = func_get_args();
        $key = self::generateRandomString(true);
        foreach ($configs as $config) {
            if (is_file($config)) {
                $content = preg_replace('/(("|\')cookieValidationKey("|\')\s*=>\s*)(""|\'\')/', "\\1'$key'", file_get_contents($config), -1, $count);
                if ($count > 0) {
                    file_put_contents($config, $content);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected static function generateRandomString($uuid=false, $length=32)
    {
        if ($uuid == true) {
            return UuidHelper::uuid();
        }

        if (!extension_loaded('openssl')) {
            throw new \Exception('The OpenSSL PHP extension is required by Yii2.');
        }
        $bytes = openssl_random_pseudo_bytes($length);
        return strtr(substr(base64_encode($bytes), 0, $length), '+/=', '_-.');
    }
}
