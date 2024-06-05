<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class video tour translation
 * @ORM\Entity
 *
 * @ORM\Table (name="video_tours_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"})
 *         }
 * )
 */
class VideoToursTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Category description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $description = '';

    /**
     * @var VideoTours
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActVideoTour\Model\VideoTours", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}