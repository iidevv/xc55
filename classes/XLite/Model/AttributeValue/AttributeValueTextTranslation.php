<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute value (text) multilingual data
 *
 * @ORM\Entity
 *
 * @ORM\Table  (name="attribute_values_text_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"})
 *         }
 * )
 */
class AttributeValueTextTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Value
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $value;

    /**
     * @var \XLite\Model\AttributeValue\AttributeValueText
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\AttributeValue\AttributeValueText", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set value
     *
     * @param string $value
     * @return AttributeValueTextTranslation
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
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
     * @return AttributeValueTextTranslation
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
