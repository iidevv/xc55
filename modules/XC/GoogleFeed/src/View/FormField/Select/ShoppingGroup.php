<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\View\FormField\Select;

use XC\GoogleFeed\Model\Attribute;
use XC\GoogleFeed\Model\SearchCondition\Expression\TypeSearchGroup;

class ShoppingGroup extends \XLite\View\FormField\Select\Regular
{
    public const PARAM_NON_SELECTED_LABEL = 'nonSelected';
    public const PARAM_EMPTY_SELECTED_LABEL = 'emptySelected';

    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return array_reduce(
            Attribute::getGoogleShoppingGroups(),
            static function ($options, $key) {
                $options[$key] = static::t($key);
                return $options;
            },
            []
        );
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $options = ['' => $this->getNonSelectedLabel()];
        if ($this->getParam(static::PARAM_EMPTY_SELECTED_LABEL)) {
            $options[TypeSearchGroup::EMPTY_VALUE] = static::t('No group');
        }
        return array_merge($options, parent::getOptions());
    }

    /**
     * @return string
     */
    protected function getNonSelectedLabel()
    {
        return $this->getParam(static::PARAM_NON_SELECTED_LABEL);
    }

    /**
     * @return string
     */
    protected function getDefaultNonSelectedLabel()
    {
        return '-- ' . mb_strtolower((string)static::t('No group')) . ' --';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_NON_SELECTED_LABEL => new \XLite\Model\WidgetParam\TypeString('Non selected label option', $this->getDefaultNonSelectedLabel()),
            static::PARAM_EMPTY_SELECTED_LABEL => new \XLite\Model\WidgetParam\TypeString('Empty selected label option', false),
        ];
    }
}
