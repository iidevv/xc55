<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\BulkEditing\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * The Product model repository
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Flag to exporting entities (no need setters and getters)
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $xcPendingBulkEdit = false;
}
