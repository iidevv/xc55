<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActWishlistUserExport\Model;


use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Wishlist extends \QSL\MyWishlist\Model\Wishlist
{

    /**
     * Flag to exporting entities
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default":"0"}, nullable=true)
     */
    protected $xcPendingExport = false;


    public function setXcPendingExport($xcPendingExport)
    {
        $this->xcPendingExport = $xcPendingExport;
    }

    public function getXcPendingExport()
    {
        return $this->xcPendingExport;
    }
}