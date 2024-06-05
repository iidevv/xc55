<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\ItemsList\Model;

/**
 * Abstract products items list
 */
abstract class AProduct extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/BackInStock/products/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getCreateURL()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function getLeftActions()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getRightActions()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    protected function isRemoved()
    {
        return false;
    }
}
