<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeString;
use CDev\GoogleAnalytics\Logic\Action;
use CDev\GoogleAnalytics\Logic\Action\Base\AAction;

/**
 * @Extender\Mixin
 *
 * Class ListItem
 */
class ListItem extends \XLite\View\Product\ListItem
{
    public const PARAM_LIST_READABLE_NAME  = 'itemListReadableName';
    public const PARAM_GA_POSITION_ON_LIST = 'gaPositionOnList';

    /**
     * Get impression GA event data
     */
    protected function getImpressionData()
    {
        $listName = $this->getReadableListName() ?: $this->getItemListWidgetTarget();
        $position = $this->getGaPositionInList() ?: '';

        $action = new Action\Impression(
            $this->getProduct(),
            $listName,
            $position
        );

        return $action->getActionData(AAction::FORMAT_JSON);
    }

    /**
     * Readable list name for GoogleAnalytics
     *
     * @return string
     */
    protected function getReadableListName(): ?string
    {
        return $this->getParam(static::PARAM_LIST_READABLE_NAME);
    }

    /**
     * @return mixed
     */
    protected function getGaPositionInList()
    {
        return $this->getParam(static::PARAM_GA_POSITION_ON_LIST);
    }

    /**
     * Define widget parameters
     *
     * @return void
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_LIST_READABLE_NAME  => new TypeString('Item list readable name'),
            static::PARAM_GA_POSITION_ON_LIST => new TypeInt('Item list position on list'),
        ];
    }
}
