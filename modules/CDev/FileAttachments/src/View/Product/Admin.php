<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\View\Product;

use XLite\Core\Request;

/**
 * Product attachments tab
 */
class Admin extends \XLite\View\Tabs\ATabs
{
    /**
     * Common widget parameter names
     */
    public const PARAM_PRODUCT = 'product';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tabs2.twig';
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'attachments' => [
                'weight'     => 100,
                'title'      => static::t('Attached Files'),
                'url_params' => [
                    'target'     => 'product',
                    'page'       => 'attachments',
                    'product_id' => $this->getProduct()->getProductId(),
                    'subpage'    => 'attachments',
                ],
                'template'   => 'modules/CDev/FileAttachments/product.twig',
            ],
        ];
    }

    protected function getTabs()
    {
        $tabs = parent::getTabs();

        if (Request::getInstance()->subpage === null) {
            $tabs['attachments']['selected'] = true;
        }

        return $tabs;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/FileAttachments/admin.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/FileAttachments/admin.less';

        return $list;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, 'XLite\Model\Product'),
        ];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getProduct()
            && $this->getProduct()->getProductId();
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }
}
