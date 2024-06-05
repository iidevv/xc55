<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi\DTO;

class CategoryDTO
{
    public $id;
    public $category_name;
    public $description;
    public $category_url;
    public $image_url;
    public $banner_url;
    public $parent_id;
    public $products_count;
    public $subcategories_count;

    public $products;
    public $subcategories;

    public $categoryModel;
}
