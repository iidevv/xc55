<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attribute value (hidden) multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="attribute_values_hidden_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"})
 *         }
 * )
 */
class AttributeValueHiddenTranslation extends \XLite\Model\Base\Translation
{
    /**
     * @var \XLite\Model\AttributeValue\AttributeValueHidden
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\AttributeValue\AttributeValueHidden", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

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
     * @return AttributeValueHiddenTranslation
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
