<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\Model\OrderItem;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class AttributeValue extends \XLite\Model\OrderItem\AttributeValue
{
    /**
     * Linked order item
     *
     * @var \XLite\Model\OrderItem
     *
     * @ORM\OneToOne  (targetEntity="XLite\Model\OrderItem", mappedBy="linkedAttributeValue", cascade={"persist"})
     */
    protected $linkedOrderItem;

    public function getLinkedOrderItem()
    {
        return $this->linkedOrderItem;
    }

    public function setLinkedOrderItem($linkedOrderItem)
    {
        $this->linkedOrderItem = $linkedOrderItem;
    }


}