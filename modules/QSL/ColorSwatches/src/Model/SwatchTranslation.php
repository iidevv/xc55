<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Color swatch multilingual data
 *
 * @ORM\Entity
 *
 * @ORM\Table (name="qsl_color_swatch_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"}),
 *              @ORM\Index (name="name", columns={"name"})
 *         }
 * )
 */
class SwatchTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Swatch name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * @var \QSL\ColorSwatches\Model\Swatch
     *
     * @ORM\ManyToOne (targetEntity="QSL\ColorSwatches\Model\Swatch", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return SwatchTranslation
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
     * @return SwatchTranslation
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
