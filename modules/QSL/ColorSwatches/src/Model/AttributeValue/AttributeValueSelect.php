<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model\AttributeValue;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute value (select)
 * @Extender\Mixin
 */
abstract class AttributeValueSelect extends \XLite\Model\AttributeValue\AttributeValueSelect
{
    /**
     * Relation to a swatch entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="QSL\ColorSwatches\Model\Swatch")
     * @ORM\JoinColumn (name="swatch_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $swatch;

    /**
     * @inheritdoc
     */
    public function asString()
    {
        $value = null;

        if (\XLite\Core\Config::getInstance()->QSL->ColorSwatches->use_swatch_name) {
            $swatch = $this->detectSwatch();
            if ($swatch) {
                $value = $swatch->getName();
            }
        }

        return $value ?: parent::asString();
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

    /**
     * Detect swatch
     *
     * @return \QSL\ColorSwatches\Model\Swatch
     */
    public function detectSwatch()
    {
        return $this->getSwatch() ?: ($this->getAttributeOption() ? $this->getAttributeOption()->getSwatch() : null);
    }

    /**
     * Set swatch
     *
     * @param \QSL\ColorSwatches\Model\Swatch $swatch
     * @return AttributeValueSelect
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
}
