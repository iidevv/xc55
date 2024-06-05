<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Migration Logic - Complete
 */
class Complete extends \XC\MigrationWizard\Logic\Migration\Step\AStep
{
    /**
     * Return step line title
     *
     * @return string
     */
    public static function getLineTitle()
    {
        return 'Step-Complete';
    }

    /**
     * Return Shop URL
     *
     * @return string
     */
    public function getShopURL()
    {
        return \XLite::getInstance()->getShopURL();
    }
}
