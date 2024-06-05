<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\Qualiteam\SkinActContactUsPage\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActContactUsPage\Helper\GoogleConfiguration;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActContactUsPage")
 */
class ContactUsPage extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\ContactUsPage
{
    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \GraphQL\Error\UserError
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $config = Config::getInstance()->Qualiteam->SkinActContactUsPage;
        $helper = new GoogleConfiguration();
        $coordinate1 = $config->showroom1_coordinate;
        $coordinate2 = $config->showroom2_coordinate;

        return [
            'about_us_content' => $config->about_us_content,
            'showrooms_content' => $config->showrooms_content,
            'about_us_title' => $config->about_us_title,
            'about_us_subtitle' => $config->about_us_subtitle,
            'about_us_working_days' => $config->about_us_working_days,
            'about_us_working_hours' => $config->about_us_working_hours,
            'toll_free_phone' => $config->toll_free_phone,
            'international_phone' => $config->international_phone,
            'fax' => $config->fax,
            'showrooms_info' => [
                [
                    'title' => $config->showroom1_title,
                    'subtitle' => $config->showroom1_subtitle,
                    'working_hours' => $config->showroom1_working_hours,
                    'address' => $config->showroom1_address,
                    'latitude' => $helper->getLatitude($coordinate1),
                    'longitude' => $helper->getLongitude($coordinate1),
                ],
                [
                    'title' => $config->showroom2_title,
                    'subtitle' => $config->showroom2_subtitle,
                    'working_hours' => $config->showroom2_working_hours,
                    'address' => $config->showroom2_address,
                    'latitude' => $helper->getLatitude($coordinate2),
                    'longitude' => $helper->getLongitude($coordinate2),
                ],
            ],
        ];
    }
}
