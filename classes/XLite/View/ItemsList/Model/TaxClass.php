<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

class TaxClass extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'tax_classes/style.css';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\Input\Text\TaxClass',
                static::COLUMN_PARAMS       => ['required' => true],
                static::COLUMN_MAIN         => true,
                static::COLUMN_ORDERBY      => 100,
            ]
        ];
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\TaxClass';
    }

    /**
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('tax_class');
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add New Class';
    }

    /**
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' tax_classes';
    }

    /**
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    /**
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if ($countOnly) {
            $result = 1 + parent::getData($cnd, $countOnly);
        } else {
            $class = new \XLite\Model\TaxClass();
            $class->setName(static::t('Default tax class'));

            $result = array_merge([$class], parent::getData($cnd, $countOnly));
        }

        return $result;
    }
}
