<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge;

use XLite\Core\Database;
use XLite\Core\Config;

abstract class Main extends \XLite\Module\AModule
{
    public const DEFAULT_WRITE_KEY = '0Xhidk9qjbFQav8TRzHrzlIXXF0MjV4D';

    /**
     * Fill concierge config data with default key and first root admin email
     */
    public static function fillDefaultConciergeOptions()
    {
        $rootAdminEmail = static::getRootAdminEmail();

        if ($rootAdminEmail) {
            $options = [
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'write_key',
                    'value'    => static::DEFAULT_WRITE_KEY,
                ],
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'user_id',
                    'value'    => $rootAdminEmail,
                ],
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'additional_config_loaded',
                    'value'    => 'true',
                ],
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'is_user_id_correct',
                    'value'    => 'true',
                ],
            ];

            Database::getRepo('XLite\Model\Config')->createOptions($options);
        }
    }

    /**
     * Return first active root administrator email
     *
     * @return string
     */
    public static function getRootAdminEmail()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_PERMISSIONS} = \XLite\Model\Role\Permission::ROOT_ACCESS;
        $cnd->{\XLite\Model\Repo\Profile::P_ORDER_BY} = ['p.profile_id'];
        $rootAdmins = Database::getRepo(\XLite\Model\Profile::class)->search($cnd);

        $rootAdminEmail = null;

        if ($rootAdmins) {
            /** @var \XLite\Model\Profile $admin */
            foreach ($rootAdmins as $admin) {
                if (
                    $admin->isAdmin()
                    && $admin->isEnabled()
                    && !preg_match('/(@x-cart\.com|@cflsystems\.com)$/i', $admin->getLogin())
                ) {
                    $rootAdminEmail = $admin->getLogin();
                    break;
                }
            }
        }

        return $rootAdminEmail;
    }

    public static function checkAndCorrectUserId()
    {
        $userId = Config::getInstance()->XC->Concierge->user_id;
        if (
            !$userId
            || preg_match('/(@x-cart\.com|@cflsystems\.com)$/i', $userId)
        ) {
            $rootAdminEmail = static::getRootAdminEmail();

            if ($rootAdminEmail) {
                $options = [
                    [
                        'category' => 'XC\Concierge',
                        'name'     => 'user_id',
                        'value'    => $rootAdminEmail,
                    ],
                    [
                        'category' => 'XC\Concierge',
                        'name'     => 'is_user_id_correct',
                        'value'    => 'true',
                    ],
                ];
            } else {
                $options = [
                    [
                        'category' => 'XC\Concierge',
                        'name'     => 'user_id',
                        'value'    => '',
                    ],
                    [
                        'category' => 'XC\Concierge',
                        'name'     => 'is_user_id_correct',
                        'value'    => 'false',
                    ],
                ];
            }
        } else {
            $options = [
                [
                    'category' => 'XC\Concierge',
                    'name'     => 'is_user_id_correct',
                    'value'    => 'true',
                ],
            ];
        }

        Database::getRepo(\XLite\Model\Config::class)->createOptions($options);
    }
}
