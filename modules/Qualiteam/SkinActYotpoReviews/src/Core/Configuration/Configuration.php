<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Configuration;

class Configuration
{
    /**
     * @var string
     */
    protected string $appKey;
    protected string $secretKey;
    protected string $product_prefix;
    protected bool   $dev_mode;
    protected bool   $show_review_widget;
    protected bool   $show_star_rating;
    protected string $review_widget_id;
    protected string $star_widget_id;
    protected bool   $conversion_tracking;

    /**
     * Constructor
     *
     * @param string $app_key
     * @param string $secret_key
     */
    public function __construct(
        string $app_key,
        string $secret_key,
        bool   $dev_mode,
        string $product_prefix,
        bool   $show_review_widget,
        bool   $show_star_rating,
        string $review_widget_id,
        string $star_widget_id,
        bool   $conversion_tracking
    ) {
        $this->appKey              = $app_key;
        $this->secretKey           = $secret_key;
        $this->dev_mode            = $dev_mode;
        $this->product_prefix      = $product_prefix;
        $this->show_review_widget  = $show_review_widget;
        $this->show_star_rating    = $show_star_rating;
        $this->review_widget_id    = $review_widget_id;
        $this->star_widget_id      = $star_widget_id;
        $this->conversion_tracking = $conversion_tracking;
    }

    /**
     * Return 'Yotpo app key' value
     *
     * @return string
     */
    public function getAppKey(): string
    {
        return $this->appKey;
    }

    /**
     * Return 'Yotpo secret key' value
     *
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * Return 'Yotpo dev mode' value
     *
     * @return bool
     */
    public function isDevMode(): bool
    {
        return $this->dev_mode;
    }

    /**
     * Return 'Yotpo product prefix' value
     *
     * @return string
     */
    public function getProductDevPrefix(): string
    {
        return $this->product_prefix;
    }

    /**
     * Return 'Yotpo show review widget' value
     *
     * @return bool
     */
    public function isShowReviewWidget(): bool
    {
        return $this->show_review_widget;
    }

    /**
     * Return 'Yotpo show star rating' value
     *
     * @return bool
     */
    public function isShowStarRating(): bool
    {
        return $this->show_star_rating;
    }

    /**
     * Return 'Yotpo review widget id' value
     *
     * @return string
     */
    public function getReviewWidgetId(): string
    {
        return $this->review_widget_id;
    }

    /**
     * Return 'Yotpo star widget id' value
     *
     * @return string
     */
    public function getStarWidgetId(): string
    {
        return $this->star_widget_id;
    }

    /**
     * Return 'Yotpo conversion tracking enable' value
     *
     * @return bool
     */
    public function isConversionTrackingEnable(): bool
    {
        return $this->conversion_tracking;
    }
}