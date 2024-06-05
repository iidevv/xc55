<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\DTO;

class ProductDTO
{
    public $id;
    public $product_code;
    public $product_name;
    public $makeAnOfferState;
    public $short_description;
    public $short_description_html;
    public $description;
    public $description_html;
    public $small_image_url;
    public $image_url;
    public $images;
    public $product_url;
    public $weight;
    public $options;
    public $inventory_enabled;
    public $amount;
    public $price;
    public $display_price;
    public $on_sale;
    public $sale_value;
    public $sale_type;
    public $review_rate;
    public $votes_count;
    public $enabled;
    public $available;
    public $coming_soon;
    public $expected_date;
    public $bookable;
    public $attributes;
    public $stickers;
    public $tags;
    public $brand;
    public $reviews;
    public $questions;
    public $vendor;
    public $is_wishlisted;

    public $productModel;
    public $unreadQuestions;
    public $condition;
    public $conditionCode;
    public $showFreeShippingLabel;
    public $marketPrice;
    public $freeShippingForProMember;
    public $newArrival;
    public $colorSwatches;
    public $video_tabs_info;

    public $reorder_attributes;
    public $review_list_url;
    public $video_tour_url;
}
