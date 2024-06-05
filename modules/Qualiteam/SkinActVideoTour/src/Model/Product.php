<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * Class product
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Attributes
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVideoTour\Model\VideoTours", mappedBy="product", cascade={"all"})
     * @ORM\OrderBy   ({"position" = "ASC"})
     */
    protected $video_tours;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->video_tours = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add attributes
     *
     * @param VideoTours $videoTours
     *
     * @return \XLite\Model\Product
     */
    public function addVideoTours(VideoTours $videoTours)
    {
        $this->video_tours[] = $videoTours;

        return $this;
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideoTours()
    {
        return $this->video_tours;
    }
}