<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Controller\Admin;

/**
 * Product tab controller
 */
class ProductTab extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'id'];

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $id = intval(\XLite\Core\Request::getInstance()->id);
        $model = $id
            ? \XLite\Core\Database::getRepo('XC\CustomProductTabs\Model\Product\Tab')->find($id)
            : null;

        return ($model && $model->getId())
            ? $model->getName()
            : static::t('Tab');
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('product_tabs'));
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XC\CustomProductTabs\View\Model\Product\Tab';
    }
}
