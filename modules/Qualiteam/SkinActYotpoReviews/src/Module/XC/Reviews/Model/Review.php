<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\Reviews\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\Model\Review
{
    /**
     * @ORM\Column(type="boolean", options={ "default": "1", "unsigned": true })
     */
    protected $yotpoSync = true;

    /**
     * @ORM\Column (type="bigint", nullable=true, options={ "unsigned": true })
     */
    protected $yotpo_id;

    public function getYotpoSync(): bool
    {
        return $this->yotpoSync;
    }

    public function setYotpoSync(bool $yotpoSync): void
    {
        $this->yotpoSync = $yotpoSync;
    }

    public function getYotpoId(): int
    {
        return $this->yotpo_id;
    }

    public function setYotpoId(?int $yotpo_id): void
    {
        $this->yotpo_id = $yotpo_id;
    }
}