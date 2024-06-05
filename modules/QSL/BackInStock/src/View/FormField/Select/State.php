<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\FormField\Select;

/**
 * State selector
 */
class State extends \XLite\View\FormField\Select\Regular
{
    /**
     * Display all options parameter name
     */
    public const PARAM_DISPLAY_ALL = 'displayAll';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_DISPLAY_ALL => new \XLite\Model\WidgetParam\TypeBool(
                'Display All options',
                false,
                false
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return [
            \QSL\BackInStock\Model\Record::STATE_STANDBY => static::t('Stand-by'),
            \QSL\BackInStock\Model\Record::STATE_READY   => static::t('Ready for send'),
            \QSL\BackInStock\Model\Record::STATE_SENT    => static::t('Sent'),
            \QSL\BackInStock\Model\Record::STATE_BOUNCED => static::t('Bounced'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getOptions()
    {
        $list = [];

        if ($this->getParam(static::PARAM_DISPLAY_ALL)) {
            $list[0] = static::t('All');
        }

        return $list + parent::getOptions();
    }
}
