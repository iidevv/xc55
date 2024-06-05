<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// It's the feature of PHP 5. We need to explicitly define current time zone.
// See also http://bugs.php.net/bug.php?id=48914
@date_default_timezone_set(@date_default_timezone_get());

// No PHP warnings are allowed in LC
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', true);
ini_set('arg_separator.output', '&');

// Short name
/** @deprecated */
define('LC_DS', DIRECTORY_SEPARATOR);

// Define admin zone
// @todo: remove after Includes/Decorator
if (
    isset($_SERVER['REQUEST_URI'])
    && strpos($_SERVER['REQUEST_URI'], '/admin/') === 0
    && defined('XCN_ADMIN_SCRIPT') === false
) {
    define('XCN_ADMIN_SCRIPT', true);
}

// Modes
define('LC_IS_CLI_MODE', PHP_SAPI === 'cli');

define('LC_DEVELOPER_MODE', $_ENV['APP_ENV'] === 'dev');

// Timestamp of the application start
define('LC_START_TIME', time());
define('MAX_TIMESTAMP', PHP_INT_SIZE === 4 ? PHP_INT_MAX : PHP_INT_MAX >> 32);

// Paths
define('LC_DIR', realpath(__DIR__));
define('LC_DIR_ROOT', rtrim(LC_DIR, '/') . '/');

define('LC_DIR_PUBLIC', LC_DIR_ROOT . 'public/');
define('LC_DIR_IMAGES', LC_DIR_PUBLIC . 'images/');
define('LC_DIR_RESOURCES', LC_DIR_PUBLIC . 'assets/');
define('LC_DIR_FILES', LC_DIR_PUBLIC . 'files/');
define('LC_DIR_SERVICE', LC_DIR_FILES . 'service/');
define('LC_DIR_CACHE_RESOURCES', LC_DIR_PUBLIC . 'var/resources/');
define('LC_DIR_CACHE_IMAGES', LC_DIR_PUBLIC . 'var/images/');

define('LC_DIR_CLASSES', LC_DIR_ROOT . 'classes/');
define('LC_DIR_LIB', LC_DIR_ROOT . 'lib/');
define('LC_DIR_SKINS', LC_DIR_ROOT . 'templates/');
define('LC_DIR_INCLUDES', LC_DIR_ROOT . 'Includes/');
define('LC_DIR_MODULES', LC_DIR_ROOT . 'modules/');

define('LC_DIR_VAR', LC_DIR_ROOT . 'var/');
define('LC_DIR_GMV', LC_DIR_VAR . 'gmv/');
define('LC_DIR_DATA', LC_DIR_VAR . 'data/');
define('LC_DIR_TMP', LC_DIR_VAR . 'tmp/');
define('LC_DIR_LOCALE', LC_DIR_VAR . 'locale/');
define('LC_DIR_DATACACHE', LC_DIR_VAR . 'datacache/');
define('LC_DIR_LOG', LC_DIR_VAR . 'log/');
define('LC_DIR_COMPILE', LC_DIR_VAR . 'run/');
define('LC_DIR_CACHE_CLASSES', LC_DIR_COMPILE . 'classes/');

define('LC_IS_PHP_7', version_compare(PHP_VERSION, '7.0.0', '>='));

// Temporary directories
define('LC_VAR_URL', 'var');

// Images subsystem settings
define('LC_IMAGES_URL', 'images');
define('LC_IMAGES_CACHE_URL', LC_VAR_URL . '/images');

// Files
define('LC_FILES_URL', 'files');

// Current X-Cart version
define('LC_VERSION', '5.5.0.29');

define('LC_USE_CLEAN_URLS', true);

// Disabled xdebug coverage for Selenium-based tests [DEVELOPMENT PURPOSE]
if (isset($_COOKIE) && !empty($_COOKIE['no_xdebug_coverage']) && function_exists('xdebug_stop_code_coverage')) {
    @xdebug_stop_code_coverage();
}

// Correct error handling mode
ini_set('display_errors', LC_DEVELOPER_MODE);

set_include_path(LC_DIR_LIB . PATH_SEPARATOR . get_include_path());

// Common error reporting settings
ini_set('log_errors', true);
ini_set("auto_detect_line_endings", true);

// Set default memory limit
func_set_memory_limit('128M');
