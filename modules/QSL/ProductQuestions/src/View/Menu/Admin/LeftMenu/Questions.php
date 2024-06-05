<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Menu\Admin\LeftMenu;

use XLite\Core\View\DynamicWidgetInterface;

/**
 * Questions
 */
class Questions extends \XLite\View\Menu\Admin\LeftMenu\ANode implements DynamicWidgetInterface
{
    protected function getLabel()
    {
        return $this->getCountUnansweredQuestions()
            ?: null;
    }

    protected function getLabelLink()
    {
        return $this->buildURL('product_questions');
    }

    protected function getLabelTitle()
    {
        return static::t('You have X unanswered questions', ['count' => $this->getCountUnansweredQuestions()]);
    }

    /**
     * Counts the number of unanswered questions.
     *
     * @return integer
     */
    protected function getCountUnansweredQuestions()
    {
        return (int) \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question')->searchUnansweredQuestions(true);
    }

    protected function getCacheParameters()
    {
        return array_merge(parent::getCacheParameters(), [
            \XLite\Core\Database::getRepo('QSL\ProductQuestions\Model\Question')->getVersion()
        ]);
    }
}
