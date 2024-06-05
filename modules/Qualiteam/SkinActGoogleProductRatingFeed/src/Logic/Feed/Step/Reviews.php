<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\Logic\Feed\Step;

use Qualiteam\SkinActGoogleProductRatingFeed\Main;
use XC\Reviews\Model\Review;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Model\Product;

/**
 * Products step
 */
class Reviews extends AFeedStep
{
    use ExecuteCachedTrait;

    /**
     * Current language
     *
     * @var \XLite\Model\Language
     */
    protected $sessionLanguage;

    protected Review  $review;
    protected Product $product;

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return Database::getRepo(Review::class);
    }

    /**
     * Process item
     *
     * @param Review $model
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        if (!empty($model->getReview())) {
            $this->applyTranslationSettings();
            if ($model instanceof Review) {
                $this->setReview($model);
                $this->setProduct($model->getProduct());
                $this->generator->addToRecord($this->getReviewRecord());
            }
            $this->unapplyTranslationSettings();
        }
    }

    protected function getReview(): Review
    {
        return $this->review;
    }

    protected function setReview(Review $review): void
    {
        $this->review = $review;
    }

    /**
     * Apply generator language for translation
     *
     * @return void
     */
    protected function applyTranslationSettings()
    {
        $this->sessionLanguage = \XLite\Core\Session::getInstance()->getLanguage();
        if ($this->languageCode) {
            \XLite\Core\Translation::setTmpTranslationCode($this->languageCode);
            \XLite\Core\Session::getInstance()->setLanguage($this->languageCode);
        } else {
            \XLite\Core\Router::getInstance()->disableLanguageUrlsTmp();
            \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);
        }
    }

    protected function getProduct(): Product
    {
        return $this->product;
    }

    protected function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return array
     */
    protected function getReviewRecord(): array
    {
        return [
            'review_id'        => $this->getRecordId(),
            'reviewer'         => $this->getReviewerTag(),
            'review_timestamp' => $this->getReviewTimestamp(),
            'content'          => $this->getContent(),
            'review_url'       => $this->getReviewUrlTag(),
            'ratings'          => $this->getRatingsTag(),
            'is_spam'          => $this->isSpam(),
        ];
    }

    protected function getRecordId(): int
    {
        return $this->getReview()->getId();
    }

    protected function getReviewerTag(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }

    protected function getName(): string
    {
        return sprintf(
            '<name is_anonymous="%s">%s</name>',
            $this->stringParamIsAnonymous(),
            $this->getReview()->getReviewerName()
        );
    }

    protected function stringParamIsAnonymous(): string
    {
        return $this->getReview()->getProfile() ? 'false' : 'true';
    }

    protected function getReviewTimestamp(): string
    {
        return date('Y-m-d', $this->getReview()->getAdditionDate()) . 'T' . date('H:i:s', $this->getReview()->getAdditionDate()) . 'Z';
    }

    protected function getContent(): string
    {
        return $this->getReview()->getReview();
    }

    protected function getReviewUrlTag(): string
    {
        return sprintf(
            '<review_url type="group">%s</review_url>',
            $this->prepareReviewUrl()
        );
    }

    protected function prepareReviewUrl(): string
    {
        return Main::getShopURL(
            Converter::buildCleanURL(
                'product',
                '',
                ['product_id' => $this->getProduct()->getProductId()],
            )
        );
    }

    protected function getRatingsTag(): array
    {
        return [
            'overall' => $this->getOverall(),
        ];
    }

    protected function getOverall(): string
    {
        return sprintf(
            '<overall min="1" max="5">%s</overall>',
            $this->getReview()->getRating()
        );
    }

    protected function isSpam(): string
    {
        return 'false';
    }

    /**
     * Unapply generator language for translation
     *
     * @return void
     */
    protected function unapplyTranslationSettings()
    {
        if ($this->languageCode) {
            \XLite\Core\Session::getInstance()->setLanguage($this->sessionLanguage->getCode());
            $this->sessionLanguage = null;
        } else {
            \XLite\Core\Router::getInstance()->releaseLanguageUrlsTmp();
        }
        \XLite\Core\Translation::setTmpTranslationCode(null);
    }
}
