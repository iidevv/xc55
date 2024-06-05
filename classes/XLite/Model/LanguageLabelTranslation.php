<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language label translations
 *
 * @ORM\Entity
 * @ORM\Table (name="language_label_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class LanguageLabelTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Label
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $label;

    /**
     * @var \XLite\Model\LanguageLabel
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\LanguageLabel", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="label_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set label
     *
     * @param string $label
     * @return LanguageLabelTranslation
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
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
     * @return LanguageLabelTranslation
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
