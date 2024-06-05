<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\Model\AttributeValue;

use QSL\ColorSwatches\Model\Swatch;
use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute value (select)
 * @Extender\Mixin
 */
abstract class AttributeValueSelect extends \XLite\Model\AttributeValue\AttributeValueSelect
{
    /**
     * @ORM\Column(name="shipdate", type="text", nullable=true)
     */
    protected $shipdate;

    /**
     * @return mixed
     */
    public function getShipdate()
    {
        return $this->shipdate;
    }

    /**
     * @param mixed $shipdate
     */
    public function setShipdate($shipdate): void
    {
        $this->shipdate = $shipdate;
    }

    /**
     * Detect swatch
     *
     * @return \QSL\ColorSwatches\Model\Swatch
     */
    public function detectSwatch()
    {
        $model = parent::detectSwatch();

        return $model instanceof Swatch ? ($model->getId() ? $model : null) : null;
    }
}
