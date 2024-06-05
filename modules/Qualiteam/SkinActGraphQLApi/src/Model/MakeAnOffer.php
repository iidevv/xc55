<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * 

 */

class MakeAnOffer extends \CSI\MakeAnOffer\View\Model\MakeAnOffer
{
    public function getTooltipsSchema()
    {
        return $this->schemaDefault;
    }
}