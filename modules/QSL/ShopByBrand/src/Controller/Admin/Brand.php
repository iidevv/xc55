<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Controller\Admin;

/**
 * Brand controller
 */
class Brand extends \QSL\ShopByBrand\Controller\Admin\ABrand
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'brand_id'];

    /**
     * Whether the brand list is editable.
     *
     * @var boolean
     */
    protected $isBrandListEditable;

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $id    = (int) \XLite\Core\Request::getInstance()->brand_id;
        $model = $id
            ? \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')->find($id)
            : null;

        return ($model && $model->getId())
            ? $model->getName()
            : \XLite\Core\Translation::getInstance()->lbl('New brand');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            static::t('Brands'),
            $this->buildURL('brands')
        );
    }

    /**
     * Update model
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('brands'));
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'QSL\ShopByBrand\View\Model\Brand';
    }
}
