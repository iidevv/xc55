<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\CleanUrls;

class AboutPageFormat extends \XLite\View\FormField\AFormField
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_LABEL;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'form_field/clean_urls/about_page_title_format.twig';
    }

    protected function getHelpLabel()
    {
        return static::t('These options separated by X, you can change it by modify X label', [
            'delimiter'  => static::t('title-delimiter'),
            'modify_url' => $this->buildURL('labels', '', ['substring' => 'title-delimiter']),
        ]);
    }
}
