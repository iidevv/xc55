<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * Custom global tab translation model class
 *
 * @ORM\Entity
 * @ORM\Table  (name="custom_global_tab_translation")
 */
class CustomGlobalTabTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Tab name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name = '';

    /**
     * Tab brief info
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $brief_info = '';

    /**
     * Tab Content
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $content = '';

    /**
     * @var \XC\CustomProductTabs\Model\Product\CustomGlobalTab
     *
     * @ORM\ManyToOne (targetEntity="XC\CustomProductTabs\Model\Product\CustomGlobalTab", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return $this
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
     * Return BriefInfo
     *
     * @return string
     */
    public function getBriefInfo()
    {
        return $this->brief_info;
    }

    /**
     * Set BriefInfo
     *
     * @param string $brief_info
     *
     * @return $this
     */
    public function setBriefInfo($brief_info)
    {
        $this->brief_info = $brief_info;
        return $this;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
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
     * @return $this
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
