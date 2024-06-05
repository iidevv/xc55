<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
abstract class AttributeOption extends \XLite\Model\AttributeOption
{
    /**
     * Relation to a swatch entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne (targetEntity="QSL\ColorSwatches\Model\Swatch")
     * @ORM\JoinColumn (name="swatch_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $swatch;

    /**
     * Set swatch
     *
     * @param \QSL\ColorSwatches\Model\Swatch $swatch
     * @return AttributeOption
     */
    public function setSwatch(\QSL\ColorSwatches\Model\Swatch $swatch = null)
    {
        $this->swatch = $swatch;
        return $this;
    }

    /**
     * Get swatch
     *
     * @return \QSL\ColorSwatches\Model\Swatch
     */
    public function getSwatch()
    {
        return $this->swatch;
    }

    /**
     * Clone
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $newEntity */
        $newEntity = parent::cloneEntity();

        if ($this->getSwatch()) {
            $swatch = $this->getSwatch();
            $newEntity->setSwatch($swatch);
        }

        return $newEntity;
    }
}
