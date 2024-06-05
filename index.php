<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * @deprecated use public/ as a server document root and public/index.php as an endpoint
 */

if (!defined('XC_DIR_ROOT')) {
    define('XC_DIR_ROOT', true);
}

return include __DIR__ . '/public/index.php';
