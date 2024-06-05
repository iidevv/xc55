<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Module\QSL\ShopByBrand\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\ShopByBrand")
 */
class Brand extends \QSL\ShopByBrand\Model\Brand
{
    /**
     * Whether to skip products of this brand from syncing to SkuVault
     *
     * @var boolean
     *
     * @ORM\Column (name="skip_sync_to_skuvault", type="boolean", nullable=true)
     */
    protected $skipSyncToSkuvault = false;

    /**
     * @return bool
     */
    public function getSkipSyncToSkuvault()
    {
        return (bool)$this->skipSyncToSkuvault;
    }

    /**
     * @param bool $skipSyncToSkuvault
     * @return \QSL\ShopByBrand\Model\Brand
     */
    public function setSkipSyncToSkuvault($skipSyncToSkuvault)
    {
        $this->skipSyncToSkuvault = $skipSyncToSkuvault;
        return $this;
    }
}
