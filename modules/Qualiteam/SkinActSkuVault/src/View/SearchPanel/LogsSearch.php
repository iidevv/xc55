<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\View\SearchPanel;

use Qualiteam\SkinActSkuVault\View\FormField\Select\Directions;
use Qualiteam\SkinActSkuVault\View\FormField\Select\OperationTypes;
use Qualiteam\SkinActSkuVault\View\FormField\Select\SyncStatuses;
use Qualiteam\SkinActSkuVault\View\ItemsList\Logs;
use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\Text\DateRange;
use XLite\View\SearchPanel\ASearchPanel;

class LogsSearch extends ASearchPanel
{
    protected function getFormClass()
    {
        return \Qualiteam\SkinActSkuVault\View\Form\LogsSearch::class;
    }

    /**
     * Get itemsList
     *
     * @return \XLite\View\ItemsList\Model\Table
     */
    protected function getItemsList()
    {
        if (!$this->itemsList) {
            $this->itemsList = parent::getItemsList()
                ?: new Logs();
        }

        return $this->itemsList;
    }

    /**
     * Define the items list CSS class with which the search panel must be linked
     *
     * @return string
     */
    protected function getLinkedItemsList()
    {
        return parent::getLinkedItemsList() . '.widget.items-list.qualiteam-skinactskuvault-itemslist-logs';
    }

    /**
     * Return true if search panel should use filters
     *
     * @return bool
     */
    protected function isUseFilter()
    {
        return true;
    }

    /**
     * Define conditions
     *
     * @return array
     */
    protected function defineConditions()
    {
        return parent::defineConditions() + [

                'operation' => [
                    ASearchPanel::CONDITION_CLASS => OperationTypes::class,
                    AFormField::PARAM_FIELD_ONLY  => false,
                    AFormField::PARAM_LABEL       => static::t('Operation'),
                ],

                'dateRange' => [
                    ASearchPanel::CONDITION_CLASS => DateRange::class,
                    AFormField::PARAM_FIELD_ONLY  => false,
                    AFormField::PARAM_LABEL       => static::t('Date range'),
                ],

                'direction' => [
                    ASearchPanel::CONDITION_CLASS => Directions::class,
                    AFormField::PARAM_FIELD_ONLY  => false,
                    AFormField::PARAM_LABEL       => static::t('Direction'),
                ],

                'status' => [
                    ASearchPanel::CONDITION_CLASS => SyncStatuses::class,
                    AFormField::PARAM_FIELD_ONLY  => false,
                    AFormField::PARAM_LABEL       => static::t('Status'),
                ],
            ];
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActSkuVault/search_panel/style.css';

        return $list;
    }
}
