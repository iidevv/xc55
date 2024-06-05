<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Model\DTO;

use CDev\Wholesale\Model\Base\AWholesalePrice;

class WholesalePrice extends \XLite\Model\DTO\Base\CommonCell
{
    public function init(AWholesalePrice $entity)
    {
        $this['displayPrice'] = $entity->getClearDisplayPrice();
        $this['quantityRangeBegin'] = $entity->getQuantityRangeBegin();
        $this['quantityRangeEnd'] = $entity->getQuantityRangeEnd();
        $this['savePriceValue'] = $entity->getSavePriceValue();
    }
}
