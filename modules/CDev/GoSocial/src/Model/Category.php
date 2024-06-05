<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    use \CDev\GoSocial\Core\OpenGraphTrait;

    /**
     * Custom Open graph meta tags
     *
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $ogMeta = '';

    /**
     * User Open graph meta tags generator flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $useCustomOG = false;

    /**
     * @inheritdoc
     */
    protected function isUseOpenGraphImage()
    {
        return (bool)$this->getImage() || $this->isRootCategory();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageWidth()
    {
        return $this->getImage()
            ? $this->getImage()->getWidth()
            : null;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageHeight()
    {
        return $this->getImage()
            ? $this->getImage()->getHeight()
            : null;
    }

    /**
     * @inheritdoc
     */
    public function getOpenGraphMetaTags($preprocessed = true)
    {
        $tags = $this->getUseCustomOG()
            ? $this->getOgMeta()
            : $this->generateOpenGraphMetaTags();

        return $preprocessed ? $this->preprocessOpenGraphMetaTags($tags) : $tags;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphTitle()
    {
        return $this->getName();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphType()
    {
        return 'website';
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphDescription()
    {
        return strip_tags($this->getDescription());
    }

    /**
     * @inheritdoc
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        $categoryURL = $this->getParent()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL('category', '', ['category_id' => $this->getCategoryId()], \XLite::getCustomerScript())
                )
            )
            : \XLite::getInstance()->getShopURL();

        $imageURL = '';

        if ($this->getImage()) {
            $imageURL = $this->getImage()->getFrontURL();
        } elseif ($this->isRootCategory()) {
            $imageURL = \XLite::getInstance()->getShopURL(\XLite\Core\Layout::getInstance()->getLogo());
        }

        return strtr(
            $tags,
            [
                '[PAGE_URL]' => $categoryURL,
                '[IMAGE_URL]' => $imageURL,
            ]
        );
    }

    /**
     * Set useCustomOG
     *
     * @param boolean $useCustomOG
     * @return static
     */
    public function setUseCustomOG($useCustomOG)
    {
        $this->useCustomOG = $useCustomOG;
        return $this;
    }

    /**
     * Get useCustomOG
     *
     * @return boolean
     */
    public function getUseCustomOG()
    {
        return $this->useCustomOG;
    }

    /**
     * Get OgMeta data
     *
     * @return string
     */
    public function getOgMeta()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * Set OgMeta data
     *
     * @param $ogMeta
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setOgMeta($ogMeta)
    {
        return $this->setTranslationField(__FUNCTION__, $ogMeta);
    }
}
