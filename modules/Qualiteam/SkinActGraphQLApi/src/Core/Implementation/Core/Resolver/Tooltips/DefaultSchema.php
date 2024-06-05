<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips;

class DefaultSchema
{
    public function getTooltipsSchema()
    {
        return $this->getDefaultFieldClass()->getTooltipFields();
    }

    protected function getDefaultFieldClass()
    {
        return new \XLite\View\FormModel\Product\Info(['object' => new \XLite\Model\DTO\Product\Info(new \XLite\Model\Product)]);
    }
}