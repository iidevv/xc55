<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * AdsBanners widget
 *
 * @ListChild (list="admin.h1.after", zone="admin")
 */
class AdsBanners extends \XLite\View\AView
{
    /**
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() && \XLite\Core\Auth::getInstance()->hasRootAccess();
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $result = parent::getJSFiles();

        // main page already has this script in the lazy load widget (notifications on the right sidebar)
        if ($this->getTarget() !== 'main') {
            $result[] = 'marketing_info/script.js';
        }

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'ads_banners/body.twig';
    }
}
