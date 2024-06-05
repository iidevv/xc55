<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OneClickUpsellAfterCheckout\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Migrate images
 *
 * @ListChild (list="crud.modulesettings.header", zone="admin", weight="100")
 */
class Help extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'module';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/OneClickUpsellAfterCheckout/help.twig';
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Request::getInstance()->moduleId == 'QSL-OneClickUpsellAfterCheckout';
    }
}
