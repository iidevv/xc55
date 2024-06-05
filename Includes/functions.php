<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Executable lookup
 * Return false if not executable.
 *
 * @deprecated use Symfony\Component\Process\ExecutableFinder (https://symfony.com/doc/current/components/process.html#finding-an-executable)
 */
function func_find_executable($filename)
{
    $directories = explode(PATH_SEPARATOR, getenv('PATH'));
    array_unshift($directories, './bin', '/usr/bin', '/usr/local/bin');

    $result = false;

    foreach ($directories as $dir) {
        $file = $dir . '/' . $filename;
        if (func_is_executable($file)) {
            $result = @realpath($file);
            break;
        }

        $file .= '.exe';
        if (func_is_executable($file)) {
            $result = @realpath($file);
            break;
        }
    }

    return $result;
}

/**
 * Emulator for the is_executable function if it doesn't exists (f.e. under windows)
 *
 * @deprecated (used only in deprecated function)
 */
function func_is_executable($file)
{
    return function_exists('is_executable')
        ? (file_exists($file) && is_executable($file))
        : (is_file($file) && is_readable($file));
}

function func_convert_to_byte($file_size)
{
    $val  = trim($file_size);
    $last = strtolower(substr($val, -1));

    $val = (int) $val;

    switch ($last) {
        case 'g':
            $val *= 1024;
        // next case will multiple the $val
        case 'm':
            $val *= 1024;
        // next case will multiple the $val
        case 'k':
            $val *= 1024;
    }

    return $val;
}

function func_check_memory_limit($current_limit, $required_limit)
{
    $result   = true;
    $limit    = func_convert_to_byte($current_limit);
    $required = func_convert_to_byte($required_limit);
    // On 64-bit system we double the memory limit required.
    $required = PHP_INT_SIZE === 8 ? 2 * $required : $required;

    if ($limit < $required) {
        @ini_set('memory_limit', $required);
        $limit = @ini_get('memory_limit');

        $result = intval($limit) === $required;
    }

    return $result;
}

function func_set_memory_limit($new_limit)
{
    $current_limit = @ini_get('memory_limit');

    return func_check_memory_limit($current_limit, $new_limit);
}

function func_htmlspecialchars($str)
{
    $str = preg_replace(
        '/&(?!(?:amp|nbsp|#\d+|#x\d+|euro|copy|pound|curren|cent|yen|reg|trade|lt|gt|lte|gte|quot|minus|#8197);)/Ss',
        '&amp;',
        $str
    );

    return str_replace(
        ['"', '\'', '<', '>'],
        ['&quot;', '&#039;', '&lt;', '&gt;'],
        $str
    );
}

/**
 * UTF-8 safety basename wrapper
 *
 * @param string $path Path
 *
 * @return string
 */
function func_basename($path)
{
    if (strpos($path, '/') !== false) {
        $path     = explode('/', $path);
        $basename = end($path);
    } elseif (strpos($path, '\\') !== false) {
        $path     = explode('\\', $path);
        $basename = end($path);
    } else {
        $basename = $path;
    }

    return $basename;
}

if (!function_exists('getallheaders')) {
    /**
     * Returns all headers (apache getallheaders polyfill)
     * https://www.php.net/manual/en/function.getallheaders.php#84262
     *
     * @return array|false
     */
    function getallheaders()
    {
        $result = [];

        if (is_array($_SERVER)) {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $result[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }

        return !empty($result) ? $result : false;
    }
}
