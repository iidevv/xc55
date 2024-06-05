<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Migrate images
 *
 * @ListChild (list="crud.modulesettings.header", zone="admin")
 */
class ModuleTopNote extends \XLite\View\AView
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
        return 'modules/QSL/OAuth2Client/module_top_note.twig';
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getModule() === 'QSL-OAuth2Client';
    }
}
