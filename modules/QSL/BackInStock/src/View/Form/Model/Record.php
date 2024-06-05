<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Form\Model;

/**
 * Records list search form
 */
class Record extends \XLite\View\Form\AForm
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/BackInStock/record/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'back_in_stock_record';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' validationEngine record');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        return [
            'id' => \XLite\Core\Request::getInstance()->id,
        ];
    }
}
