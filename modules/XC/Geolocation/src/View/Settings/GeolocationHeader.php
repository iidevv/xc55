<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\View\Settings;

use XCart\Extender\Mapping\ListChild;

/**
 * Warning
 *
 * @ListChild (list="crud.modulesettings.header", zone="admin", weight="100")
 */
class GeolocationHeader extends \XLite\View\AView
{
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), [
            'module'
        ]);
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getModule() === 'XC\\Geolocation'
            && !\XLite\Core\Config::getInstance()->XC->Geolocation->extended_db_path;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/Geolocation/settings/header.twig';
    }

    public function getCSSFiles()
    {
        $return = parent::getCSSFiles();

        $return[] = 'modules/XC/Geolocation/settings/style.less';

        return $return;
    }
}
