<?php


namespace Qualiteam\SkinActCreateOrder\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\After("CDev\Wholesale")
 *
 */
class Product extends \XLite\Model\Product
{
    /**
     * @var mixed
     */
    public $orderProfileMembership;

    public function getCurrentMembership()
    {
        if ($this->orderProfileMembership) {
            return $this->orderProfileMembership;
        }
        return parent::getCurrentMembership();
    }
}