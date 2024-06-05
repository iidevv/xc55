<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * NewsMessage
 *
 * @ORM\Entity
 * @ORM\Table (name="news_message_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class NewsMessageTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name;

    /**
     * Content
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $body = '';

    /**
     * Brief description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $brief_description = '';

    /**
     * News meta keywords
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $metaTags = '';

    /**
     * News meta description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $metaDesc = '';

    /**
     * Value of the title HTML-tag for news page
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $metaTitle = '';

    /**
     * @var \XC\News\Model\NewsMessage
     *
     * @ORM\ManyToOne (targetEntity="XC\News\Model\NewsMessage", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return NewsMessageTranslation
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
     * Set body
     *
     * @param string $body
     * @return NewsMessageTranslation
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set brief_description
     *
     * @param string $briefDescription
     * @return NewsMessageTranslation
     */
    public function setBriefDescription($briefDescription)
    {
        $this->brief_description = $briefDescription;
        return $this;
    }

    /**
     * Get brief_description
     *
     * @return string
     */
    public function getBriefDescription()
    {
        return $this->brief_description;
    }

    /**
     * Set metaTags
     *
     * @param string $metaTags
     * @return NewsMessageTranslation
     */
    public function setMetaTags($metaTags)
    {
        $this->metaTags = $metaTags;
        return $this;
    }

    /**
     * Get metaTags
     *
     * @return string
     */
    public function getMetaTags()
    {
        return $this->metaTags;
    }

    /**
     * Set metaDesc
     *
     * @param string $metaDesc
     * @return NewsMessageTranslation
     */
    public function setMetaDesc($metaDesc)
    {
        $this->metaDesc = $metaDesc;
        return $this;
    }

    /**
     * Get metaDesc
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return $this->metaDesc;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return NewsMessageTranslation
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * Get metaTitle
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
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
     * @return NewsMessageTranslation
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
