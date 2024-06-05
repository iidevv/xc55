<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute option multilingual data
 *
 * @ORM\Entity
 * @ORM\Table (
 *     name="attribute_option_translations",
 *     indexes={
 *         @ORM\Index (name="ci", columns={"code","id"}),
 *         @ORM\Index (name="id", columns={"id"})
 *     }
 * )
 */
class AttributeOptionTranslation extends \XLite\Model\Base\Translation
{
    /**
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * @var \XLite\Model\AttributeOption
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\AttributeOption", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return AttributeOptionTranslation
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
     * @return AttributeOptionTranslation
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
