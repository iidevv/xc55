<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name="product_stickers_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *      }
 * )
 *
 */

class ProductStickerTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name;

    /**
     * @var \QSL\ProductStickers\Model\ProductSticker
     *
     * @ORM\ManyToOne (targetEntity="QSL\ProductStickers\Model\ProductSticker", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="sticker_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
