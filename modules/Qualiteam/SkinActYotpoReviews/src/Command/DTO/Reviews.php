<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Command\DTO;

use XC\Reviews\Model\Review;
use XLite\Core\Config;

class Reviews implements IDTO
{
    private string $product_id;
    private string $product_title;
    private string $product_url;
    private string $date;
    private string $review_content;
    private string $review_score;
    private string $display_name;
    private string $email;
    private string $md_customer_country;

    /**
     * @param \XC\Reviews\Model\Review $review
     */
    public function __construct(Review $review)
    {
        $this->product_id          = $review->getProduct()->getSku();
        $this->product_title       = $review->getProduct()->getName();
        $this->product_url         = $review->getProduct()->getURL();
        $this->date                = $this->getDate($review);
        $this->review_content      = $review->getReview();
        $this->review_score        = $review->getRating();
        $this->display_name        = $review->getReviewerName();
        $this->email               = $this->getEmail($review);
        $this->md_customer_country = $this->getCustomerCountry($review);
    }

    private function getDate(Review $review): string
    {
        return date($this->getDateFormat(), $review->getAdditionDate());
    }

    private function getDateFormat(): string
    {
        return 'Y-m-d';
    }

    private function getEmail(Review $review): string
    {
        $emails = @unserialize(Config::getInstance()->Company->users_department, ['allowed_classes' => false]);
        $email  = is_array($emails) && count($emails) > 0 ? $emails[0] : $this->getDefaultEmail();

        return $review->getEmail() ?? $email;
    }

    private function getDefaultEmail(): string
    {
        return 'anonymous@spaandequipment.com';
    }

    private function getCustomerCountry(Review $review): string
    {
        return $review->getProfile() && $review->getProfile()->getBillingAddress()
            ? $review->getProfile()->getBillingAddress()->getCountryCode()
            : "US";
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return [
            $this->product_id,
            $this->product_title,
            $this->product_url,
            $this->date,
            $this->review_content,
            $this->review_score,
            $this->display_name,
            $this->email,
            $this->md_customer_country,
        ];
    }
}
