<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="video_category_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class VideoCategoryTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Category name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Category description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $description = '';

    /**
     * @var VideoCategory
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActVideoFeature\Model\VideoCategory", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return VideoCategoryTranslation
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return VideoCategoryTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get label_id
     *
     * @return integer
     */
    public function getLabelId()
    {
        return $this->label_id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return VideoCategoryTranslation
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}