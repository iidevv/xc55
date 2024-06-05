<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @inheritdoc
     */
    protected function init($object)
    {
        parent::init($object);

        $this->marketing->googleFeedEnabled = $object->getGoogleFeedEnabled();
    }

    /**
     * @inheritdoc
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setGoogleFeedEnabled((bool) $this->marketing->googleFeedEnabled);
    }
}
