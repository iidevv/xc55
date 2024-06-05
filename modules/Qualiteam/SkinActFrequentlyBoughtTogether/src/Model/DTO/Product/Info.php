<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Model\DTO\Product;

use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    use FreqBoughtTogetherTrait;

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $default = $this->default;
        $object->setExcludeFreqBoughtTogether($default->{$this->getExcludeFreqBoughtTogetherParamName()});
    }

    protected function init($object)
    {
        parent::init($object);

        $default                                                   = $this->default;
        $default->{$this->getExcludeFreqBoughtTogetherParamName()} = $object->getExcludeFreqBoughtTogether();
    }
}