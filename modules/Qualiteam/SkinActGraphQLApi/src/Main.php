<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi;

use Includes\Utils\Module\Manager;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\JWT;


/**
 * Module main class
 */
abstract class Main extends \XLite\Module\AModule
{
    public static function init()
    {


        parent::init();
    }

    /**
     * Method to call just after the module is installed
     *
     * @return void
     */
    public static function callInstallEvent()
    {
        parent::callInstallEvent();

        JWT::generateSecureData();
    }
}
