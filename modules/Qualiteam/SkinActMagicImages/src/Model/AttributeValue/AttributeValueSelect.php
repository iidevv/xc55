<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\Model\AttributeValue;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class AttributeValueSelect extends \XLite\Model\AttributeValue\AttributeValueSelect
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet", mappedBy="attributeValue",
     *                                                                                    cascade={"all"})
     */
    protected $magicSwatchesSet;

    public function __construct(array $data = [])
    {
        $this->magicSwatchesSet = new ArrayCollection();

        parent::__construct($data);
    }
}