<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\ItemsList;

/**
 * Attributes properties items list
 */
class ColorSwatchesSettings extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ColorSwatches/attributes/style.css';

        return $list;
    }
        /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name'    => [
                static::COLUMN_NAME    =>  static::t('Attribute'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Label',
                static::COLUMN_MAIN    => true,
                static::COLUMN_ORDERBY => 100,
            ],
            'show_selector' => [
                static::COLUMN_NAME    =>  static::t('Show selector'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\YesNo',
                static::COLUMN_MAIN    => false,
                static::COLUMN_ORDERBY => 200,
            ],
//            'show_on_list' => [
//                static::COLUMN_NAME    =>  static::t('Show on list'),
//                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\YesNo',
//                static::COLUMN_MAIN    => false,
//                static::COLUMN_ORDERBY => 300,
//            ],
        ];
    }

    /**
     * The columns are ordered according the static::COLUMN_ORDERBY values
     *
     * @return array
     */
    protected function prepareColumns()
    {
        $columns = parent::prepareColumns();

        $columns['show_selector'][static::COLUMN_PARAMS]['product'] = $this->getProduct();
        //$columns['show_on_list'][static::COLUMN_PARAMS]['product'] = $this->getProduct();

        return $columns;
    }


    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Attribute';
    }

    // {{{ Behaviors

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Infinity';
    }

    /**
     * Return entities list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $editableAttributes = $this->getProduct()->getEditableAttributes();
        $colorSwatches = new \Doctrine\Common\Collections\ArrayCollection();
        if ($editableAttributes) {
            foreach ($editableAttributes as $attribute) {
                if ($attribute->isColorSwatchesAttribute()) {
                    $colorSwatches->add($attribute);
                }
            }
        }

        return $colorSwatches ? $colorSwatches : null;
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return \XLite::getController()->getProduct();
    }

    /**
     * @param array                                       $column
     * @param \XLite\Model\Attribute|\XLite\Model\AEntity $entity
     *
     * @return array
     */
    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        $list = parent::preprocessFieldParams($column, $entity);

        if ($column['code'] === 'show_selector') {
            $list['value'] = $entity->isShowSelector($this->getProduct());
        }

        return $list;
    }
}
