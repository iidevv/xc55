<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\Module\CSI\MakeAnOffer;

/**
 * Class MakeAnOffer
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\Module\CSI\MakeAnOffer
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend ("CSI\MakeAnOffer")
 *
 */

class MakeAnOffer extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\Fields
{
    protected function getSchema()
    {
        return array_merge(parent::getSchema(), [
            'offer'    => new \CSI\MakeAnOffer\View\Model\MakeAnOffer()
        ]);
    }
}