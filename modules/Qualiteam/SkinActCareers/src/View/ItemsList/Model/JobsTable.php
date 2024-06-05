<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCareers\View\ItemsList\Model;


class JobsTable extends \XLite\View\ItemsList\Model\Table
{

    protected function getFormTarget()
    {
        return 'jobs';
    }

    protected function getSortByModeDefault()
    {
        return 'j.position';
    }

    protected function isSwitchable()
    {
        return true;
    }

    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    protected function isRemoved()
    {
        return true;
    }

    protected function isSelectable()
    {
        return true;
    }

    protected function getLeftActions()
    {
        $list = [];
        $list[] = $this->getSelectorActionTemplate();
        $list[] = $this->getMoveActionTemplate();
        $list[] = $this->getSwitcherActionTemplate();

        return $list;
    }

    protected function getPanelClass()
    {
        return '\Qualiteam\SkinActCareers\View\StickyPanel\JobsTableStickyPanel';
    }

    protected function defineColumns()
    {
        return [
            'title' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActCareers title'),
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_LINK => 'job',
            ],
        ];
    }

    protected function defineRepositoryName()
    {
        return '\Qualiteam\SkinActCareers\Model\Job';
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActCareers/jobs_tab.css';
        return $list;
    }
}