<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Notifications;

use XLite\Core\Auth;
use XC\ThemeTweaker\Core\Notifications\Data\Constant;

class StaticProvider
{
    public static function getProvidersForNotification($dir)
    {
        $result = [];

        $data = static::getNotificationsStaticData();

        if (!empty($data[$dir])) {
            foreach ($data[$dir] as $name => $datum) {
                $result[] = new Constant(
                    $name,
                    $datum,
                    $dir
                );
            }
        }

        return $result;
    }

    protected static function getNotificationsStaticData()
    {
        return [
            'failed_admin_login' => [
                'login' => 'admin@example.com',
                'ip' => '127.0.0.1'
            ],
            'failed_transaction' => [
            ],
            'profile_deleted' => [
                'login' => 'deleted@example.com',
            ],
            'recover_password_request' => [
                'profile' => Auth::getInstance()->getProfile(),
                'resetKey' => 'profile_reset_key_placeholder'
            ],
            'register_anonymous' => [
                'password' => 'new_password_placeholder',
            ],
        ];
    }
}
