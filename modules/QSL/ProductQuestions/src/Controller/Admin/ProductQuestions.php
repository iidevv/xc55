<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Controller\Admin;

/**
 * Questions controller
 */
class ProductQuestions extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Questions');
    }

    /**
     * Update list
     */
    protected function doActionUpdate()
    {
        $list = new \QSL\ProductQuestions\View\ItemsList\Model\Question();
        $list->processQuick();
    }

    // {{{ Search

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return $searchParams[$paramName] ?? null;
    }

    /**
     * Save search conditions
     */
    protected function doActionSearch()
    {
        $cellName = \QSL\ProductQuestions\View\ItemsList\Model\Question::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            \QSL\ProductQuestions\View\ItemsList\Model\Question::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = \QSL\ProductQuestions\View\ItemsList\Model\Question::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];
        }

        return $searchParams;
    }

    // }}}
}
