<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class SeoGeneralSettings extends \XLite\Controller\Admin\Settings
{
    /**
     * Get options for current tab (category)
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions($getAllOptions = false)
    {
        $notGeneralOpts = ['home_page_title_and_meta', 'page_404', 'regular_text_404', 'show_email_404', 'about_404_page', 'result_404_page_preview'];

        return array_filter(parent::getOptions(true), static function ($opt) use ($notGeneralOpts) {
            return !in_array($opt->getName(), $notGeneralOpts);
        });
    }
}
