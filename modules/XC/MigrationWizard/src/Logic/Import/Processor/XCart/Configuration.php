<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Blowfish\Wrapper;

/**
 * Configuration processor
 */
class Configuration extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const CLEAN_URL_TYPE_C = 'c';
    public const CLEAN_URL_TYPE_M = 'm';
    public const CLEAN_URL_TYPE_P = 'p';
    public const CLEAN_URL_TYPE_S = 's';

    public const CONFIG_OPTION_CLEAN_URLS_ENABLED    = 'clean_urls_enabled';
    public const CONFIG_OPTION_NO_INVENTORY_TRACKING = 'unlimited_products';
    public const CONFIG_OPTION_VALUE_DISABLED        = 'N';
    public const CONFIG_OPTION_VALUE_ENABLED         = 'Y';

    public const MODULE_ADVANCED_CUSTOMER_REVIEW = 'Advanced_Customer_Reviews';
    public const MODULE_DETAILED_PRODUCT_IMAGES  = 'Detailed_Product_Images';
    public const MODULE_EXTRA_FIELDS             = 'Extra_Fields';
    public const MODULE_FEATURE_COMPARISON       = 'Feature_Comparison';
    public const MODULE_MANUFACTURERS            = 'Manufacturers';
    public const MODULE_PRODUCT_OPTIONS          = 'Product_Options';
    public const MODULE_SITEMAP                  = 'Sitemap';
    public const MODULE_XMLSITEMAP               = 'XML_Sitemap';
    public const MODULE_UPS_SHIPPING             = 'UPS_OnLine_Tools';
    public const MODULE_WHOLESALE_TRADING        = 'Wholesale_Trading';

    // }}} </editor-fold>

    // {{{ Getters <editor-fold desc="Getters" defaultstate="collapsed">

    /**
     * Get available languages
     *
     * @return array
     */
    public static function getAvailableLanguages()
    {
        if (static::hasMigrationCache('storage', 'availableLanguages')) {
            return static::getMigrationCache('storage', 'availableLanguages');
        }

        $prefix    = static::getTablePrefix();
        $languages = static::getColumnData(
            'SELECT code'
            . " FROM {$prefix}languages"
            . ' WHERE code <> ""'
            . ' GROUP BY code'
            . ' ORDER BY code'
        );
        $_languages = [];
        array_walk($languages, static function (&$code) {
            $code = strtoupper($code);
        });

        if (version_compare(static::getPlatformVersion(), '4.5.0') < 0) {
            $defaultAdminLng = static::getCellData('SELECT value'
                . " FROM {$prefix}config"
                . ' WHERE name = "default_admin_language"');
            $defaultAdminLng = strtoupper($defaultAdminLng);
            if (in_array($defaultAdminLng, $languages)) {
                $_languages[] = $defaultAdminLng;
            }

            $defaultCustomerLng = static::getCellData('SELECT value'
                . " FROM {$prefix}config"
                . ' WHERE name = "default_customer_language"');
            $defaultCustomerLng = strtoupper($defaultCustomerLng);
            if (in_array($defaultCustomerLng, $languages)) {
                $_languages[] = $defaultCustomerLng;
            }

            $_languages = array_unique($_languages);

            if (empty($_languages)) {
                $_languages = $languages;
            }
        } else {
            foreach ($languages as $lng) {
                $lng = strtolower($lng);
                if (static::isTableExists("products_lng_{$lng}")) {
                    $_languages[] = $lng;
                }
            }
        }
        array_walk($_languages, static function (&$code) {
            $code = strtolower($code);
        });
        unset($code);

        if (!in_array('en', $_languages) && in_array('us', $_languages)) {
            foreach ($_languages as &$code) {
                if ($code == 'us') {
                    $code = 'en';
                    unset($code);
                    break;
                }
            }
        }

        if (!in_array('en', $_languages)) {
            $_languages[] = 'en';
        }

        if (version_compare(static::getPlatformVersion(), '4.5.0') < 0) {
            $_languages = ['en'];
        }

        static::setMigrationCache('storage', 'availableLanguages', $_languages);

        return $_languages;
    }

    /**
     * Return configuration option value or NULL
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getConfigurationOptionValue($name)
    {
        if (static::hasMigrationCache('configurationOption', $name)) {
            return static::getMigrationCache('configurationOption', $name);
        }

        $PDOStatement = static::getConfigurationOptionValuePDOStatement();

        $value = $PDOStatement && $PDOStatement->execute([$name])
            ? $PDOStatement->fetch(\PDO::FETCH_COLUMN)
            : null;

        static::setMigrationCache('configurationOption', $name, $value);

        return $value;
    }

    /**
     * @return bool|\PDOStatement
     */
    protected static function getConfigurationOptionValuePDOStatement()
    {
        /** @var string $prefix */
        $prefix = static::getTablePrefix();

        return static::getPreparedPDOStatement(
            'SELECT value'
            . " FROM {$prefix}config"
            . ' WHERE name = ?'
        );
    }

    /**
     * Return default customer language
     *
     * @return string
     */
    public static function getDefaultCustomerLanguage()
    {
        $lng = static::getConfigurationOptionValue('default_customer_language');

        if ($lng == 'US') {
            $lng = 'en';
        }

        return $lng;
    }

    /**
     * Return default customer language
     *
     * @return string
     */
    public static function getDefaultAdminLanguage()
    {
        $lng = static::getConfigurationOptionValue('default_admin_language');

        if ($lng == 'US') {
            $lng = 'en';
        }

        return $lng;
    }

    /**
     * Return static page URL
     *
     * @param integer $id
     * @param string  $lng
     *
     * @return string
     */
    public static function getStaticPageURL($id, $lng)
    {
        return static::getSiteUrl() . "/pages.php?pageid={$id}&sl={$lng}";
    }

    // }}} </editor-fold>

    // {{{ Checkers <editor-fold desc="Checkers" defaultstate="collapsed">

    /**
     * Return TRUE if module enabled
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function isModuleEnabled($name)
    {
        if (static::hasMigrationCache('storage', 'enabledModules')) {
            $modules = static::getMigrationCache('storage', 'enabledModules');
        } else {
            $prefix  = self::getTablePrefix();
            $modules = static::getKeyValueData(
                'SELECT module_name, active'
                . " FROM {$prefix}modules"
                . ' WHERE active="Y"'
            );

            static::setMigrationCache('storage', 'enabledModules', $modules);
        }

        return isset($modules[$name]);
    }

    /**
     * Return TRUE if secret key is valid
     *
     * @return boolean
     */
    public static function isSecretKeyValid($key)
    {
        $result = false;

        $cryptedData = static::getConfigurationOptionValue('crypted_data');

        if ($cryptedData) {
            $result = in_array(static::textDecrypt($cryptedData, $key), ['TEXT', 'TEST'], true);
        }

        return $result;
    }

    /**
     * Returns TRUE if clean URls are enabled
     *
     * @return boolean
     */
    public static function areCleanURLsEnabled()
    {
        return (static::getConfigurationOptionValue(self::CONFIG_OPTION_CLEAN_URLS_ENABLED) === 'Y');
    }

    // }}} </editor-fold>

    // {{{ Cipher methods <editor-fold desc="Cipher methods" defaultstate="collapsed">

    /**
     * Return encrypted text
     *
     * @param string      $text
     * @param string|bool $key
     *
     * @return string
     */
    public static function textCrypt($text, $key = false)
    {
        if ($key === false) {
            $key = static::getStepConnect()->getSecret();
        }

        if (empty($key) || $text === '') {
            return $text;
        }

        $crypted = Wrapper::crypt($text . Wrapper::crc32(md5($text)), $key);

        return "B-{$crypted}";
    }

    /**
     * Return decrypted text or FALSE
     *
     * @param string      $text
     * @param string|bool $key
     *
     * @return mixed
     */
    public static function textDecrypt($text, $key = false)
    {
        $result = false;

        if ($key === false) {
            $key = static::getStepConnect()->getSecret();
        }

        $type = substr($text, 0, 1);

        if ($type === false) {
            return false;
        } elseif (substr($text, 1, 1) == '-') {
            $crc32 = true;
            $text  = substr($text, 2);
        } else {
            $crc32 = substr($text, 1, 8);
            $text  = substr($text, 9);
        }

        $result1 = trim(Wrapper::decrypt($text, $key));

        // CRC32 check
        if ($crc32 === true) {
            // Inner CRC32
            $crc32  = substr($result1, -8);
            $result = substr($result1, 0, -8);

            if (Wrapper::crc32(md5($result)) != $crc32) {
                $result = false;
            }
        } elseif ($crc32 !== false) {
            // Outer CRC32
            if (Wrapper::crc32($result1) != $crc32) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Return text hash
     *
     * @param string $text
     *
     * @return string
     */
    public static function textHash($text)
    {
        return (new Hash\PasswordHash())->HashPassword($text);
    }

    /**
     * Return TRUE on correct text
     *
     * @param string $text
     * @param string $hash
     *
     * @return boolean
     */
    public static function textVerify($text, $hash)
    {
        return (new Hash\PasswordHash())->CheckPassword($text, $hash);
    }

    // }}} </editor-fold>
}
