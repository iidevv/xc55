<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\CleanUrls;

class DefaultSiteFieldsPreview extends \XLite\View\FormField\AFormField
{
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/clean_urls/default_site_fields_preview.less';

        return $list;
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/clean_urls/default_site_fields_preview.js';

        return $list;
    }

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
        return 'form_field/clean_urls/default_site_fields_preview.twig';
    }

    protected function getDefaultFieldsPreview()
    {
        $shop_url      = \XLite::getInstance()->getShopURL();
        $company_name  = "<a class='company-name' href='{$shop_url}'>" . static::t('default-site-title') . '</a>';
        $company_descr = "<span class='company-descr'>" . static::t('default-meta-description') . '</span>';
        $company_keywords = "<span class='company-keywords'>" . static::t('default-meta-keywords') . '</span>';

        return "<span class='company-url'>$shop_url</span><br />$company_name<br />$company_descr<br />$company_keywords";
    }
}
