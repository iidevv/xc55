<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language translations
 *
 * @ORM\Entity
 * @ORM\Table (name="language_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"})
 *      }
 * )
 */
class LanguageTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Language name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=64, nullable=false)
     */
    protected $name;

    /**
     * @var \XLite\Model\Language
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Language", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="lng_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return LanguageTranslation
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
     * @return LanguageTranslation
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
