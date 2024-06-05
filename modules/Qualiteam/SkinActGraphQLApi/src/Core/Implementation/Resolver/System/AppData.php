<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Category;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Product;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Category\CategoriesAppData;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product\ProMembershipProduct;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Service\CartService;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Config;

/**
 * Class AppData
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class AppData implements ResolverInterface
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @param                  $val
     * @param                  $args
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $mapper = new Category();
        $productMapper = new Product();

        return [
            'languages'                     => new Languages(),
            'countries'                     => new Countries(),
            'currencies'                    => new Currencies(),
            'states'                        => new States(),
            'profile_fields'                => new ProfileFields(),
            'memberships'                   => new Memberships(),
            'modules'                       => new Modules(),
            'home_page_widgets'             => new HomePageWidgets(),
            'external_auth_providers'       => new ExternalAuthProviders(),
            'mobileAppCategories'           => new CategoriesAppData($mapper),
            'proMembershipProduct'          => new ProMembershipProduct($productMapper),
            'savedCardsUrl'                 => new SavedCardsUrl($this->cartService),
            'departmentsList'               => $this->getDepartmentsList(),
            'request_catalog_url'           => $this->getRequestCatalogUrl(),
            'request_catalog_image_url'     => $this->getRequestCatalogImageUrl(),
            'google_map_api_key'            => $this->getGoogleMapApiKey(),
            'cloud_search_api_key'          => $this->getCloudSearchApiKey(),
        ];
    }

    protected function getCloudSearchApiKey()
    {
        $config = Config::getInstance()->QSL->CloudSearch;

        if (Container::getContainer()->get(ModuleManagerDomain::class)->isEnabled('QSL-CloudSearch')) {
            return $config->api_key;
        }

        return '';
    }

    protected function getDepartmentsList()
    {
        if (Container::getContainer()->get(ModuleManagerDomain::class)->isEnabled('Qualiteam-SkinActContactUsPage')) {
            return \Qualiteam\SkinActContactUsPage\View\FormField\Select\DepartmentSelect::getDepartments();
        }

        return [];
    }

    protected function getRequestCatalogUrl()
    {
        $config = Config::getInstance()->Qualiteam->SkinActContactUsPage;

        if (Container::getContainer()->get(ModuleManagerDomain::class)->isEnabled('Qualiteam-SkinActContactUsPage')) {
            return $config->request_catalog_url;
        }

        return '';
    }

    protected function getRequestCatalogImageUrl()
    {
        $config = Config::getInstance()->Qualiteam->SkinActContactUsPage;

        if (Container::getContainer()->get(ModuleManagerDomain::class)->isEnabled('Qualiteam-SkinActContactUsPage')) {
            return $config->request_catalog_image;
        }

        return '';
    }

    protected function getGoogleMapApiKey()
    {
        $config = Config::getInstance()->Qualiteam->SkinActContactUsPage;

        if (Container::getContainer()->get(ModuleManagerDomain::class)->isEnabled('Qualiteam-SkinActContactUsPage')) {
            return $config->gmap_api_key;
        }

        return '';
    }
}
