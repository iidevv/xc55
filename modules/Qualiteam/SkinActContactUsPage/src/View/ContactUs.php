<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\View;

use XCart\Extender\Mapping\Extender;

/**
 * Contact us controller
 *
 * @Extender\Mixin
 */
class ContactUs extends \CDev\ContactUs\View\ContactUs
{

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActContactUsPage/contact_us/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActContactUsPage/contact_us/body.twig';
    }
}