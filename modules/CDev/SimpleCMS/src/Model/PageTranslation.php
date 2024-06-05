<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table (name="page_translations",
 *      indexes={
 *          @ORM\Index (name="ci", columns={"code","id"}),
 *          @ORM\Index (name="id", columns={"id"})
 *      }
 * )
 */
class PageTranslation extends \XLite\Model\Base\Translation
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
     * Teaser
     * todo: rename to $metaDesc
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $teaser;

    /**
     * Content
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $body;

    /**
     * Meta keywords
     * todo: rename to $metaTags, set column type: (type="string", length=255)
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $metaKeywords = '';

    /**
     * Value of the title HTML-tag for category page
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $metaTitle = '';

    /**
     * @var \CDev\SimpleCMS\Model\Page
     *
     * @ORM\ManyToOne (targetEntity="CDev\SimpleCMS\Model\Page", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Set name
     *
     * @param string $name
     * @return PageTranslation
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
     * Set teaser
     *
     * @param string $teaser
     * @return PageTranslation
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
        return $this;
    }

    /**
     * Get teaser
     *
     * @return string
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return PageTranslation
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
     * Set metaKeywords
     *
     * @param string $metaKeywords
     * @return PageTranslation
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }

    /**
     * Get metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set metaTitle
     *
     * @param string $metaTitle
     * @return PageTranslation
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
     * @return PageTranslation
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
