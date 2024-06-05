<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\CleanUrls;

class Result404PagePreview extends \XLite\View\FormField\AFormField
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
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/clean_urls/result_404_page_preview.less';

        return $list;
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

    protected function getDefaultTemplate()
    {
        return 'form_field/clean_urls/result_404_page_preview.twig';
    }

    protected function getResultPreview404PageUrl()
    {
        return \XLite::getInstance()->getShopURL(\XLite\Core\Converter::buildURL(
            'page_not_found',
            '',
            [],
            \XLite::getCustomerScript()
        ));
    }
}
