<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

use Doctrine\ORM\Mapping as ORM;

/**
 * Shipping status multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (name="order_shipping_status_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class ShippingTranslation extends \XLite\Model\Order\Status\AStatusTranslation
{
    /**
     * @var \XLite\Model\Order\Status\Shipping
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Order\Status\Shipping", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;
}
