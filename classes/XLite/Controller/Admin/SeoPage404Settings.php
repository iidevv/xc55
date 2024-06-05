<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class SeoPage404Settings extends \XLite\Controller\Admin\Settings
{
    /**
     * Get options for current tab (category)
     *
     * @param bool $getAllOptions
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions($getAllOptions = false)
    {
        $currentOpts = ['page_404', 'regular_text_404', 'show_email_404', 'about_404_page', 'result_404_page_preview'];

        return array_filter(parent::getOptions(true), static function ($opt) use ($currentOpts) {
            return in_array($opt->getName(), $currentOpts);
        });
    }

    protected function getLabelsToUpdate()
    {
        return [
            'regular_text_404' => 'default-404-text',
        ];
    }
}
