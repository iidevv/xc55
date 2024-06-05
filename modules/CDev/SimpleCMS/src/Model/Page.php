<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Layout;

/**
 * @ORM\Entity
 * @ORM\Table  (name="pages",
 *      indexes={
 *          @ORM\Index (name="enabled", columns={"enabled"}),
 *      }
 * )
 */
class Page extends \XLite\Model\Base\Catalog
{
    public const TYPE_PRIMARY = 'primary';
    public const TYPE_DEFAULT = 'content';
    public const TYPE_SERVICE = 'service';

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
     * One-to-one relation with page_images table
     *
     * @var \CDev\SimpleCMS\Model\Image\Page\Image
     *
     * @ORM\OneToOne  (targetEntity="CDev\SimpleCMS\Model\Image\Page\Image", mappedBy="page", cascade={"all"})
     */
    protected $image;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="page", cascade={"all"})
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
     * Tab position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=10, options={"default": "content"})
     */
    protected $type = self::TYPE_DEFAULT;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=10, options={"default": "default"})
     */
    protected $layoutGroup = Layout::LAYOUT_GROUP_DEFAULT;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable = true)
     */
    protected $adminUrl;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable = true)
     */
    protected $frontUrl;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=255, nullable = true)
     */
    protected $tooltipText;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable = true)
     */
    protected $module;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\SimpleCMS\Model\PageTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Returns meta description
     * todo: rename to getMetaDesc()
     *
     * @return string
     */
    public function getTeaser()
    {
        return $this->getMetaDescType() === 'A' || !$this->getSoftTranslation()->getTeaser()
            ? static::postprocessMetaDescription($this->getBody())
            : $this->getSoftTranslation()->getTeaser();
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
     * @return Page
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
     * Set metaDescType
     *
     * @param string $metaDescType
     * @return Page
     */
    public function setMetaDescType($metaDescType)
    {
        $this->metaDescType = $metaDescType;
        return $this;
    }

    /**
     * Set image
     *
     * @param \CDev\SimpleCMS\Model\Image\Page\Image $image
     * @return Page
     */
    public function setImage(\CDev\SimpleCMS\Model\Image\Page\Image $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return \CDev\SimpleCMS\Model\Image\Page\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add cleanURLs
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     * @return Page
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

    /**
     * Return Position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set Position
     *
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Page
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getLayoutGroup()
    {
        return $this->layoutGroup;
    }

    /**
     * @param string $layoutGroup
     * @return Page
     */
    public function setLayoutGroup($layoutGroup)
    {
        $this->layoutGroup = $layoutGroup;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrontUrl()
    {
        return $this->frontUrl;
    }

    /**
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->adminUrl;
    }

    /**
     * @return string|null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return bool
     */
    public function isPrimaryPage()
    {
        return $this->getType() === self::TYPE_PRIMARY;
    }

    /**
     * @return bool
     */
    public function isServicePage()
    {
        return $this->getType() === self::TYPE_SERVICE;
    }

    /**
     * @return string
     */
    public function getLayoutType()
    {
        return Layout::getInstance()->getLayoutTypeByGroup($this->getLayoutGroup());
    }

    /**
     * @return string
     */
    public function getLayoutTypeImageUrl()
    {
        return Layout::getInstance()->getResourceWebPath('modules/CDev/SimpleCMS/items_list/cells/layout/images/' . $this->getLayoutType() . '.svg');
    }

    /**
     * @return string
     */
    public function getTooltipText()
    {
        return $this->tooltipText;
    }

    /**
     * @return bool
     */
    public function isCategoryPage()
    {
        return strpos($this->getFrontUrl(), 'target=category') !== false;
    }

    /**
     * @return bool
     */
    public function isProductPage()
    {
        return strpos($this->getFrontUrl(), 'target=product') !== false;
    }

    /**
     * @return bool
     */
    public function isFrontPage()
    {
        return $this->getFrontUrl() === '/';
    }

    /**
     * @return bool
     */
    public function isBrandPage()
    {
        return strpos($this->getFrontUrl(), 'target=brand') !== false;
    }

    // {{{ Translation Getters / setters

    /**
     * @param string $teaser
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setTeaser($teaser)
    {
        return $this->setTranslationField(__FUNCTION__, $teaser);
    }

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
    public function getMetaKeywords()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaKeywords
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setTranslationField(__FUNCTION__, $metaKeywords);
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

    /**
     * @return bool
     */
    public function isOneItemPage()
    {
        return $this->isCategoryPage()
            || $this->isProductPage()
            || $this->isBrandPage();
    }

    /**
     * @return string
     */
    public function getCleanURLbyFrontURL()
    {
        $result = '';

        $frontUrl = $this->getFrontUrl();
        if (
            $frontUrl
            && !$this->isOneItemPage()
            && !$this->isFrontPage()
        ) {
            $urlParams = [];
            $urlData = explode('&', str_replace('?', '', $frontUrl));
            if (is_string($urlData)) {
                $urlData = [$urlData];
            }
            foreach ($urlData as $keyval) {
                $keyval = explode('=', $keyval);
                if (count($keyval) == 2) {
                    $name = $keyval[0];
                    $value = $keyval[1];
                    $urlParams[$name] = $value;
                }
            }

            if ($urlParams && isset($urlParams['target'])) {
                $target = $urlParams['target'];
                $action = $urlParams['action'] ?? '';
                unset($urlParams['target'], $urlParams['action']);
                $result = \XLite\Core\Converter::buildCleanURL($target, $action, $urlParams);
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isIncludeToSitemap()
    {
        return !$this->isOneItemPage()
            && !$this->isFrontPage()
            && !$this->isServicePage();
    }

    // }}}
}
