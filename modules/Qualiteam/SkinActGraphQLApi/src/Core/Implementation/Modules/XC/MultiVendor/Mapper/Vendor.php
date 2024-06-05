<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\MultiVendor\Mapper;

class Vendor
{
    /**
     * @param \XC\MultiVendor\Model\Profile $vendor
     *
     * @return array
     */
    public function mapToArray(\XLite\Model\Profile $vendor)
    {
        return [
            'id' => $vendor->getProfileId(),
            'email' => $vendor->getLogin(),
            'company_name' => $vendor->getVendorCompanyName(),
            'image' => ($vendor->getVendorImage() ? $vendor->getVendorImage()->getFrontURL() : ""),
            'location' => $vendor->getVendorLocation(),
            'description_html' => $vendor->getVendorDescription(),
            'description' => strip_tags($vendor->getVendorDescription()),
        ];
    }
}