<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Controller\Admin;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Api;

/**
 * Shipping settings management page controller
 */
class GraphqlApi extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return true;
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        require_once(LC_DIR_MODULES . 'Qualiteam/SkinActGraphQLApi/vendor/autoload.php');

        Api::getInstance()->start();

        exit;
    }
}
