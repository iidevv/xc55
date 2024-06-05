<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Magic360 image
 *
 * @ORM\Entity (repositoryClass="\Qualiteam\SkinActMagicImages\Model\Repo\Image")
 * @ORM\Table  (name="magic360_images")
 */
class Image extends \XLite\Model\Base\Image
{

    /**
     * @ORM\ManyToOne  (targetEntity="\Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet", inversedBy="images")
     * @ORM\JoinColumn (name="magic_swatches_set_id", referencedColumnName="id")
     */
    protected $magicSwatchesSet;

    /**
     * Image position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $orderby = 0;

    /**
     * Get orderby
     *
     * @return integer
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set orderby
     *
     * @param integer $orderby
     *
     * @return Image
     */
    public function setOrderby($orderby)
    {
        $this->orderby = $orderby;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMagicSwatchesSet()
    {
        return $this->magicSwatchesSet;
    }

    /**
     * @param mixed $magicSwatchesSet
     */
    public function setMagicSwatchesSet($magicSwatchesSet): void
    {
        $this->magicSwatchesSet = $magicSwatchesSet;
    }
}
