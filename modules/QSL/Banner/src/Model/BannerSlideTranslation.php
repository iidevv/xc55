<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table  (name="banner_slide_translations",
 *    indexes={
 *      @ORM\Index (name="ci", columns={"code","id"}),
 *      @ORM\Index (name="id", columns={"id"})
 *   }
 * )
 */
class BannerSlideTranslation extends \XLite\Model\Base\Translation
{
    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $maintext = '';

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $addtext = '';

    /**
     * @var string
     *
     * @ORM\Column (type="string", nullable=true)
     */
    protected $actionButton = 'Find out more';

    /**
     * @var \QSL\Banner\Model\BannerSlide
     *
     * @ORM\ManyToOne (targetEntity="QSL\Banner\Model\BannerSlide", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @param $maintext
     */
    public function setMaintext($maintext)
    {
        $this->maintext = $maintext;
    }

    /**
     * @return string
     */
    public function getMaintext()
    {
        return $this->maintext;
    }

    /**
     * @param $maintext
     */
    public function setAddtext($addtext)
    {
        $this->addtext = $addtext;
    }

    /**
     * @return string
     */
    public function getAddtext()
    {
        return $this->addtext;
    }

    /**
     * @param $actionButton
     */
    public function setActionButton($actionButton)
    {
        $this->actionButton = $actionButton;
    }

    /**
     * @return string
     */
    public function getActionButton()
    {
        return $this->actionButton;
    }
}
