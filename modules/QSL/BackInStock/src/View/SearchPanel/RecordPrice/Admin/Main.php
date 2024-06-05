<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\SearchPanel\RecordPrice\Admin;

/**
 * Main admin records search panel
 */
class Main extends \XLite\View\SearchPanel\SimpleSearchPanel
{
    /**
     * @inheritdoc
     */
    protected function getFormClass()
    {
        return 'QSL\BackInStock\View\Form\ItemsList\RecordPrice\Search';
    }

    /**
     * @inheritdoc
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.records';
    }

    /**
     * Get 'including' subcondition
     *
     * @param string $name Subcondition name
     *
     * @return boolean
     */
    protected function getIncludingCondition($name)
    {
        $conditions = $this->getConditions();

        return empty($conditions[\QSL\BackInStock\Model\Repo\Record::SEARCH_INCLUDING])
            || !empty($conditions[\QSL\BackInStock\Model\Repo\Record::SEARCH_INCLUDING][$name]);
    }
}
