<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\System\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use Includes\Utils\Module\Manager;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Types\Model\AppData\HomePageWidgetType;
use XLite\Core\Translation;

/**
 * Class HomePageWidgets
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\System\Resolver
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("Qualiteam\SkinActSkin")
 *
 */

class HomePageWidgets extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System\HomePageWidgets
{
    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return [
            $this->getFeaturedProductsWidget(),
            $this->getBestsellersWidget(),
            $this->getSaleWidget(),
            $this->getNewArrivalsWidget(),
       //     $this->getMostPopularWidget()
        ];
    }

    /**
     * @return mixed
     */
    protected function getMostPopularWidgetCategories()
    {
        $widget = new \Qualiteam\ConsignItAwaySkin\View\Modules\Bestsellers\MostPopular();

        return array_map(static function ($item) {
            $mapper = new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Category();
            return $mapper->mapToDto($item);
        }, $widget->getMostPopularCategories());
    }

    /**
     * @return array
     */
    protected function getMostPopularWidget()
    {
        return [
            'display_name' => (string) Translation::lbl('Most Popular in the Catalog'),
            'service_name' => 'most_popular',
            'enabled'      => true,
            'type'         => HomePageWidgetType::TYPE_CATEGORY_LIST,
            'categories'   => $this->getMostPopularWidgetCategories(),
            'params'       => [
                'filters' => json_encode([
                    'featured' => true,
                ])
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getFeaturedProductsWidget()
    {
        return [
            'display_name' => (string) Translation::lbl('Featured products'),
            'service_name' => 'featured_products',
            'enabled'      => static::isModuleEnabled('CDev\FeaturedProducts'),
            'type'         => HomePageWidgetType::TYPE_PRODUCT_LIST,
            'categories'   => [],
            'params'       => [
                'filters' => json_encode([
                    'featured' => true,
                ])
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getBestsellersWidget()
    {
        return [
            'display_name' => (string) Translation::lbl('Bestsellers'),
            'service_name' => 'bestsellers',
            'enabled'      => static::isModuleEnabled('CDev\Bestsellers'),
            'type'         => HomePageWidgetType::TYPE_PRODUCT_LIST,
            'categories'   => [],
            'params'       => [
                'filters' => json_encode([
                    'bestsellers' => true,
                ])
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getSaleWidget()
    {
        return [
            'display_name' => (string) Translation::lbl('Sale'),
            'service_name' => 'sale',
            'enabled'      => static::isModuleEnabled('CDev\Sale'),
            'type'         => HomePageWidgetType::TYPE_PRODUCT_LIST,
            'categories'   => [],
            'params'       => [
                'filters' => json_encode([
                    'sale' => true
                ])
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getNewArrivalsWidget()
    {
        return [
            'display_name' => (string) Translation::lbl('New arrivals'),
            'service_name' => 'new_arrivals',
            'enabled'      => static::isModuleEnabled('CDev\ProductAdvisor'),
            'type'         => HomePageWidgetType::TYPE_PRODUCT_LIST,
            'categories'   => [],
            'params'       => [
                'filters' => json_encode([
                    'new_arrivals' => true
                ])
            ],
        ];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected static function isModuleEnabled($name)
    {
        return Manager::getRegistry()->isModuleEnabled($name);
    }
}