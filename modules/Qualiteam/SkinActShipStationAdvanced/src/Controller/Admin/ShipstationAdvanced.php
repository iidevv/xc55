<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Controller\Admin;

use XLite\Core\Database;
use XLite\Model\Config;
use XLite\View\Model\Settings;

class ShipstationAdvanced extends \XLite\Controller\Admin\AAdmin
{
    public function getTitle()
    {
        return static::t('SkinActShipStationAdvanced shipstation settings');
    }

    public function getOptions()
    {
        return $this->executeCachedRuntime(function () {
            return Database::getRepo(Config::class)
                ->findByCategoryAndVisible($this->getOptionsCategory());
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * Get options category
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'ShipStation\Api';
    }

    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');
    }

    protected function getModelFormClass()
    {
        return Settings::class;
    }
}
