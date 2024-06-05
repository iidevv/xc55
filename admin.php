<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * @deprecated use public/ as a server document root and public/index.php as an endpoint
 */

if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== 0) {
    $_SERVER['REQUEST_URI'] = '/admin/' . ltrim($_SERVER['REQUEST_URI'], '/');
}

return include __DIR__ . '/index.php';
