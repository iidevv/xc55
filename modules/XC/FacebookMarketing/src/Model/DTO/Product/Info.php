<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Model\DTO\Product;

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

        $this->marketing->facebookMarketingEnabled = $object->getFacebookMarketingEnabled();
    }

    /**
     * @inheritdoc
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setFacebookMarketingEnabled((bool) $this->marketing->facebookMarketingEnabled);
    }
}
