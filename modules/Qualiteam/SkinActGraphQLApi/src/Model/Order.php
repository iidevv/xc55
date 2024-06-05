<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model;

use Doctrine\ORM\Mapping as ORM;



/**
 * Class represents an order
 * @ORM\Table(
 *      indexes={
 *          @ORM\Index (name="api_cart_token", columns={"apiCartUniqueId"})
 *      }
 * )
 *
 * @ORM\MappedSuperclass
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * 

 */

abstract class Order extends \XLite\Model\Order
{
    /**
     * JSON API cart token
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, options={"default" : ""})
     */
    protected $apiCartUniqueId = '';

    /**
     * Get cart token for JSON API
     *
     * @return string
     */
    public function getApiCartUniqueId()
    {
        return $this->apiCartUniqueId;
    }

    /**
     * Get cart token for JSON API
     *
     * @param string $token
     */
    public function setApiCartUniqueId($token)
    {
        $this->apiCartUniqueId = $token;
    }

    /**
     * Check if order is placed using JSON API
     *
     * @return boolean
     */
    public function isApiCart()
    {
        return !empty($this->apiCartUniqueId);
    }

    /**
     * @return \XLite\Model\Order
     */
    public function cloneEntity()
    {
        $return = parent::cloneEntity();

        $this->setApiCartUniqueId('');

        return $return;
    }
}
