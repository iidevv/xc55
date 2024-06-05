<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\Module\XC\MultiVendor;

/**
 * Class Register
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\Module\XC\MultiVendor
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend ("XC\MultiVendor")
 *
 */

class Register extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\Fields
{
    protected function getSchema()
    {
        return array_merge(parent::getSchema(), [
            'register' => new \XC\MultiVendor\View\Model\Profile\Register()
        ]);
    }
}