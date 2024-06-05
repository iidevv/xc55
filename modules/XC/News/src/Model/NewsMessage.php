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
 * @ORM\Table  (name="news",
 *      indexes={
 *          @ORM\Index (name="enabled", columns={"enabled"}),
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class NewsMessage extends \XLite\Model\Base\Catalog
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Is menu enabled or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Date add news message
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="newsMessage", cascade={"all"})
     * @ORM\OrderBy   ({"id" = "ASC"})
     */
    protected $cleanURLs;

    /**
     * Meta description type
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1)
     */
    protected $metaDescType = 'A';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\News\Model\NewsMessageTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Prepare order before save data operation
     *
     * @return void
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prepareBeforeSave()
    {
        parent::prepareBeforeSave();

        if (!is_numeric($this->date) || !is_int($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Check - news is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getEnabled()
            && $this->getDate() < \XLite\Core\Converter::time();
    }

    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURL()
    {
        $url = null;
        if ($this->getId()) {
            $url = \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'newsMessage',
                        '',
                        ['id' => $this->getId()],
                        \XLite::getCustomerScript(),
                        true
                    )
                )
            );
        }

        return $url;
    }

    /**
     * Returns meta description
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return $this->getMetaDescType() === 'A' || !$this->getSoftTranslation()->getMetaDesc()
            ? static::postprocessMetaDescription($this->getBody())
            : $this->getSoftTranslation()->getMetaDesc();
    }

    /**
     * Returns meta description type
     *
     * @return string
     */
    public function getMetaDescType()
    {
        $result = $this->metaDescType;

        if (!$result) {
            $metaDescPresent = array_reduce($this->getTranslations()->toArray(), static function ($carry, $item) {
                return $carry ?: (bool) $item->getMetaDesc();
            }, false);

            $result = $metaDescPresent ? 'C' : 'A';
        }

        return $result;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return NewsMessage
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return NewsMessage
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set metaDescType
     *
     * @param string $metaDescType
     * @return NewsMessage
     */
    public function setMetaDescType($metaDescType)
    {
        $this->metaDescType = $metaDescType;
        return $this;
    }

    /**
     * Add cleanURLs
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     * @return NewsMessage
     */
    public function addCleanURLs(\XLite\Model\CleanURL $cleanURLs)
    {
        $this->cleanURLs[] = $cleanURLs;
        return $this;
    }

    /**
     * Get cleanURLs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCleanURLs()
    {
        return $this->cleanURLs;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $body
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setBody($body)
    {
        return $this->setTranslationField(__FUNCTION__, $body);
    }

    /**
     * @return string
     */
    public function getBriefDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $briefDescription
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setBriefDescription($briefDescription)
    {
        return $this->setTranslationField(__FUNCTION__, $briefDescription);
    }

    /**
     * @return string
     */
    public function getMetaTags()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaTags
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaTags($metaTags)
    {
        return $this->setTranslationField(__FUNCTION__, $metaTags);
    }

    /**
     * @param string $country
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaDesc($country)
    {
        return $this->setTranslationField(__FUNCTION__, $country);
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaTitle
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setTranslationField(__FUNCTION__, $metaTitle);
    }

    // }}}
}
