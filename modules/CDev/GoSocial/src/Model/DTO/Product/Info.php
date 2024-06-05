<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;
use CDev\GoSocial\Logic\OgMeta;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    protected function init($object)
    {
        parent::init($object);

        $this->marketing->og_tags_type = (string)(int)$object->getUseCustomOG();
        $this->marketing->og_tags = $object->getOpenGraphMetaTags();
    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $object->setUseCustomOG((bool)$this->marketing->og_tags_type);
        if ($this->marketing->og_tags_type) {
            $object->setOgMeta(OgMeta::prepareOgMeta($rawData['marketing']['og_tags']));
        } else {
            $object->setOgMeta($object->getOpenGraphMetaTags(false));
        }
    }
}
