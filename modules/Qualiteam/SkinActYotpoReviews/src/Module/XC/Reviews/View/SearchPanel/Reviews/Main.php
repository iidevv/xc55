<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\View\SearchPanel\Reviews;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Main extends \XC\Reviews\View\SearchPanel\Review\Main
{
    protected function defineConditions()
    {
        $list = parent::defineConditions();
        return $this->getChangedConditions($list);
    }

    protected function getChangedConditions(array $conditions): array
    {
        $result = [];

        foreach ($conditions as $name => $condition) {
            if (!in_array($name, $this->getRemovedConditionNames(), true)) {
                $result[$name] = $condition;
            }
        }

        return $result;
    }

    protected function getRemovedConditionNames(): array
    {
        return [
            'type',
            'status',
        ];
    }
}