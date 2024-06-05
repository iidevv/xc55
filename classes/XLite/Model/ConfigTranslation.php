<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="config_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class ConfigTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Human-readable option name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $option_name;

    /**
     * Option comment
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $option_comment = '';

    /**
     * @var \XLite\Model\Config
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Config", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="config_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set option_name
     *
     * @param string $optionName
     * @return ConfigTranslation
     */
    public function setOptionName($optionName)
    {
        $this->option_name = $optionName;
        return $this;
    }

    /**
     * Get option_name
     *
     * @return string
     */
    public function getOptionName()
    {
        return $this->option_name;
    }

    /**
     * Set option_comment
     *
     * @param string $optionComment
     * @return ConfigTranslation
     */
    public function setOptionComment($optionComment)
    {
        $this->option_comment = $optionComment;
        return $this;
    }

    /**
     * Get option_comment
     *
     * @return string
     */
    public function getOptionComment()
    {
        return $this->option_comment;
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
     * @return ConfigTranslation
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
