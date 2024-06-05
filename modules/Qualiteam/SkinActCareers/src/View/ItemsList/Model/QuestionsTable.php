<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCareers\View\ItemsList\Model;


use Qualiteam\SkinActCareers\Model\InterviewQuestion;

class QuestionsTable extends \XLite\View\ItemsList\Model\Table
{

    protected function getFormTarget()
    {
        return 'interview_questions';
    }

    protected function getSortByModeDefault()
    {
        return 'i.position';
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
        return '\Qualiteam\SkinActCareers\View\StickyPanel\QuestionsTableStickyPanel';
    }

    protected function defineColumns()
    {
        return [
            'question' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActCareers question'),
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_LINK => 'career_question',
            ],
            'type' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActCareers question type table'),
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_MAIN => true,
            ],
            'mandatory' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('SkinActCareers mandatory'),
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_LINK => 'question',
                static::COLUMN_CLASS => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff',
            ],
        ];
    }

    protected function getTypeColumnValue(\XLite\Model\AEntity $entity)
    {
        $map = [
            InterviewQuestion::TYPE_PLAIN => static::t('SkinActCareers TYPE_PLAIN'),
            InterviewQuestion::TYPE_SELECT => static::t('SkinActCareers TYPE_SELECT'),
            InterviewQuestion::TYPE_TEXTAREA => static::t('SkinActCareers TYPE_TEXTAREA'),
        ];

        return $map[$entity->getType()] ?? '';
    }

    protected function defineRepositoryName()
    {
        return '\Qualiteam\SkinActCareers\Model\InterviewQuestion';
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