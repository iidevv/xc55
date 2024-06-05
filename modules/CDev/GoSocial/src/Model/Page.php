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
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Page extends \CDev\SimpleCMS\Model\Page
{
    use \CDev\GoSocial\Core\OpenGraphTrait;

    /**
     * User Open graph meta tags generator flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $useCustomOG = false;

    /**
     * Custom Open graph meta tags
     *
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $ogMeta = '';

    /**
     * Show Social share buttons or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $showSocialButtons = false;

    /**
     * @inheritdoc
     */
    protected function isUseOpenGraphImage()
    {
        return (bool)$this->getImage();
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageWidth()
    {
        return $this->getImage()
            ? $this->getImage()->getWidth()
            : 0;
    }

    /**
     * @inheritdoc
     */
    protected function getOpenGraphImageHeight()
    {
        return $this->getImage()
            ? $this->getImage()->getHeight()
            : 0;
    }

    /**
     * Set showSocialButtons
     *
     * @param boolean $showSocialButtons Show social buttons
     *
     * @return \CDev\GoSocial\Model\Page
     */
    public function setShowSocialButtons($showSocialButtons)
    {
        $this->showSocialButtons = $showSocialButtons ? 1 : 0;

        return $this;
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
    protected function getOpenGraphDescription()
    {
        return strip_tags($this->getTeaser());
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
     * @inheritdoc
     */
    protected function preprocessOpenGraphMetaTags($tags)
    {
        return str_replace(
            [
                '[PAGE_URL]',
                '[IMAGE_URL]',
            ],
            [
                htmlentities($this->getFrontURL(), ENT_COMPAT, 'UTF-8'),
                htmlentities($this->getImage() ? $this->getImage()->getFrontURL() : '', ENT_COMPAT, 'UTF-8'),
            ],
            $tags
        );
    }

    /**
     * Get showSocialButtons
     *
     * @return boolean
     */
    public function getShowSocialButtons()
    {
        return $this->showSocialButtons;
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
