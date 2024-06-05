<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\View\ItemsList\Model;

class Rate extends \XLite\View\ItemsList\Model\Table
{
    protected $sortByModes = [
        'r.zone'       => 'Zone',
        'r.membership' => 'User membership',
        'r.taxClass'   => 'Tax class',
        'r.value'      => 'Value',
    ];

    /**
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'zone'        => [
                static::COLUMN_NAME      => static::t('Zone'),
                static::COLUMN_CLASS     => 'XLite\View\Taxes\Inline\Zone',
                static::COLUMN_ORDERBY   => 100,
                static::COLUMN_HEAD_HELP => '<a href="' . static::buildURL('zones') . '" target="_blank">' . static::t('Manage Zones') . '</a>',
                static::COLUMN_SORT      => 'r.zone',
            ],
            'taxClass'    => [
                static::COLUMN_NAME      => static::t('Tax Class'),
                static::COLUMN_CLASS     => 'CDev\SalesTax\View\FormField\Inline\TaxClass',
                static::COLUMN_ORDERBY   => 200,
                static::COLUMN_HEAD_HELP => '<a href="' . static::buildURL('tax_classes') . '" target="_blank">' . static::t('Manage Classes') . '</a>',
                static::COLUMN_SORT      => 'r.taxClass',
            ],
            'membership'  => [
                static::COLUMN_NAME      => static::t('Membership Level'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Select\Membership',
                static::COLUMN_ORDERBY   => 300,
                static::COLUMN_HEAD_HELP => '<a href="' . static::buildURL('memberships') . '" target="_blank">' . static::t('Manage Memberships') . '</a>',
                static::COLUMN_SORT      => 'r.membership',
            ],
            'taxableBase' => [
                static::COLUMN_NAME    => static::t('Taxable base'),
                static::COLUMN_CLASS   => 'CDev\SalesTax\View\FormField\Inline\RateTaxableBase',
                static::COLUMN_ORDERBY => 350,
            ],
            'value'       => [
                static::COLUMN_NAME      => static::t('Rate') . ', (%)',
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Text\FloatInput',
                static::COLUMN_PARAMS    => [
                    \XLite\View\FormField\Input\Text\FloatInput::PARAM_E => 4,
                ],
                static::COLUMN_ORDERBY   => 400,
                static::COLUMN_HEAD_HELP => static::t('Calculated against taxable base.'),
                static::COLUMN_SORT      => 'r.value',
            ],
        ];

        return $columns;
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'CDev\SalesTax\Model\Tax\Rate';
    }

    /**
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('vat_sales_rate');
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
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' rates';
    }

    /**
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }

    /**
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = new \XLite\Core\CommonCell();

        $result->{\CDev\SalesTax\Model\Repo\Tax\Rate::PARAM_EXCL_TAXABLE_BASE}
            = \CDev\SalesTax\Model\Tax\Rate::TAXBASE_SHIPPING;

        if ($this->getOrderBy()) {
            $result->{\XLite\Model\Repo\Order::P_ORDER_BY} = $this->getOrderBy();
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getEmptyListDir()
    {
        return 'modules/CDev/SalesTax/items_list';
    }

    /**
     * @return string
     */
    protected function getEmptyListFile()
    {
        return 'empty.twig';
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add New';
    }
}
