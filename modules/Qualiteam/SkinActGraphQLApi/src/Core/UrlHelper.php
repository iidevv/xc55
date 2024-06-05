<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core;

/**
 * Class Languages
 */
class UrlHelper
{
    public static function insertWebAuth($url)
    {
        if (!isset($_ENV['XP_DEV_WEB_USER'], $_ENV['XP_DEV_WEB_PASS'])) {
            return $url;
        }

        $user = $_ENV['XP_DEV_WEB_USER'];
        $password = $_ENV['XP_DEV_WEB_PASS'];

        $matches = [];

        $result = preg_match('@(.+)(://)(.+)@', $url, $matches);

        if ($result > 0) {
            $url = $matches[1] . $matches[2] . $user . ':' . $password . '@' . $matches[3];
        }

        return $url;
    }
}
