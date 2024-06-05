<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\ItemsList\Model\Product\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Model\SearchCondition\RepositoryHandler;
use XPay\XPaymentsCloud\View\FormField\Select\Product\IsSubscription;
use XLite\View\FormField\AFormField;
use XLite\View\SearchPanel\ASearchPanel;
use XLite\View\SearchPanel\SimpleSearchPanel;

/**
 * This class adds extra search field for filtering products that have subscription plans
 *
 * @Extender\Mixin
 */
abstract class Search extends \XLite\View\ItemsList\Model\Product\Admin\Search implements \XLite\Base\IDecorator
{
    const IS_SUBSCRIPTION = 'is_subscription';

    /**
     * Get search params
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return array_merge(
            parent::getSearchParams(),
            array(
                self::IS_SUBSCRIPTION => array(
                    'condition' => new RepositoryHandler(self::IS_SUBSCRIPTION),
                    'widget'    => array(
                        SimpleSearchPanel::CONDITION_TYPE => SimpleSearchPanel::CONDITION_TYPE_HIDDEN,
                        ASearchPanel::CONDITION_CLASS     => IsSubscription::class,
                        AFormField::PARAM_LABEL           => static::t('Display subscription products'),
                    ),
                ),
            )
        );
    }

}
