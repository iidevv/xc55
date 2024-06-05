<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Admin;

/**
 * Back in stock record controller
 */
class BackInStockRecord extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target', 'id'];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        $id = (int) \XLite\Core\Request::getInstance()->id;
        $model = $id
            ? \XLite\Core\Database::getRepo('QSL\BackInStock\Model\Record')->find($id)
            : null;

        return ($model && $model->getId() && $model->getProduct())
            ? $model->getProduct()->getName()
            : \XLite\Core\Translation::getInstance()->lbl('Record');
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('records'));
        }
    }

    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'QSL\BackInStock\View\Model\Record';
    }
}
