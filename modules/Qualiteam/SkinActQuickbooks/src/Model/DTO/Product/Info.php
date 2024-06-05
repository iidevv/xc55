<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\DTO\Product;

use XLite\Core\Database;
use Qualiteam\SkinActQuickbooks\Model\QuickbooksProducts;
use XCart\Extender\Mapping\Extender;

/**
 * DTO Product model
 * 
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);
        
        $this->default->quickbooks_fullname = Database::getRepo(QuickbooksProducts::class)
            ->getQuickbooksFullname($object->getProductId(), 0);
    }
    
    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        Database::getRepo(QuickbooksProducts::class)
            ->setQuickbooksFullname(
                $object->getProductId(),
                0,
                $this->default->quickbooks_fullname
            );
        
        parent::populateTo($object, $rawData);
    }
}