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
 * @Extender\Depend ("CDev\Sale")
 */
class SaleDiscount extends \CDev\Sale\Model\SaleDiscount
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
     * @inheritdoc
     */
    protected function isUseOpenGraphImage()
    {
        return false;
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
        return 'product.group';
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
        return strip_tags($this->getMetaDesc());
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
        $saleDiscountUrl = $this->getId()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'sale_discount',
                        '',
                        ['id' => $this->getId()],
                        \XLite::getCustomerScript(),
                        true
                    )
                )
            ) : \XLite::getInstance()->getShopURL();

        return str_replace(
            [
                '[PAGE_URL]',
            ],
            [
                htmlentities($saleDiscountUrl, ENT_COMPAT, 'UTF-8'),
            ],
            $tags
        );
    }

    /**
     * @return string
     */
    public function getOgMeta()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $ogMeta
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setOgMeta($ogMeta)
    {
        return $this->setTranslationField(__FUNCTION__, $ogMeta);
    }
}
