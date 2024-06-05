<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Controller\Admin;

use Qualiteam\SkinActFreeGifts\Model\FreeGiftItem;
use Qualiteam\SkinActFreeGifts\Model\FreeGift;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\TopMessage;
use XLite\Model\Product;

class GiftTier extends \XLite\Controller\Admin\AAdmin
{
    /**
     * params
     *
     * @var string
     */
    protected $params = ['target', 'gift_tier_id'];


    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t(
            'SkinActFreeGifts Gift tier X', [
                'name' => $this->getGiftTierValue()?->getTierName(),
            ]
        );
    }

    protected function isVisible()
    {
        return (bool)$this->getGiftTierValue();
    }

    protected function addBaseLocation()
    {
        $this->addLocationNode(
            static::t('SkinActFreeGifts Free Gifts'),
            $this->buildURL('free_gifts')
        );
    }

    /**
     * Get gift tier products list
     *
     * @return array(FreeGiftItem) Objects
     */
    public function getGiftTierProductsList()
    {
        return Database::getRepo(FreeGiftItem::class)
            ->getGiftTierProducts($this->gift_tier_id);
    }

    /**
     * @return FreeGift|null|bool
     */
    public function getGiftTierValue()
    {
        static $giftTier;

        $giftTier = ($giftTier ?: Database::getRepo(FreeGift::class)
            ->find((int)$this->gift_tier_id));

        return $giftTier;
    }

    /**
     * doActionAdd
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionAdd()
    {
        if (isset(Request::getInstance()->select)) {
            $pids     = Request::getInstance()->select;
            $products = Database::getRepo(Product::class)
                ->findByIds($pids);

            $this->id = Request::getInstance()->gift_tier_id;
            $free_gift = Database::getRepo(FreeGift::class)
                ->findByIds([$this->id]);

            $existingLinksIds = [];
            $existingLinks    = $this->getGiftTierProductsList();

            if ($existingLinks) {
                foreach ($existingLinks as $k => $v) {
                    $existingLinksIds[] = $v->getProduct()->getProductId();
                }
            }

            if ($products) {
                foreach ($products as $product) {
                    if (in_array($product->getProductId(), $existingLinksIds, true)) {
                        TopMessage::addWarning(
                            'The product SKU#"X" is already set for the gift tier',
                            ['SKU' => $product->getSku()]
                        );
                    } else {
                        $gt = new FreeGiftItem();
                        $gt->setProduct($product);
                        $gt->setFreeGift($free_gift[0]);

                        Database::getEM()->persist($gt);
                    }
                }
            }

            Database::getEM()->flush();
        }

        $this->setReturnURL($this->buildURL(
            'gift_tier',
            '',
            Request::getInstance()->gift_tier_id
                ? ['gift_tier_id' => $this->id]
                : ''
        ));
    }
}
