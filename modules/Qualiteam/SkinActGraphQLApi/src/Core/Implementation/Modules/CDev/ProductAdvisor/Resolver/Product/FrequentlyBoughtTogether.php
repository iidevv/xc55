<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\ProductAdvisor\Resolver\Product;

use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Config;
use XLite\Model\OrderItem;
use XLite\Model\Product;

use XCart\Extender\Mapping\Extender;
use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend({"Qualiteam\SkinActFrequentlyBoughtTogether", "CDev\ProductAdvisor"})
 *
 */
class FrequentlyBoughtTogether extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\FrequentlyBoughtTogether
{
    use FreqBoughtTogetherTrait;

    /**
     * @var Mapper\Product
     */
    protected $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\Product $mapper
     */
    public function __construct(Mapper\Product $mapper)
    {
        $this->mapper = $mapper;
    }

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
        $productId = (int) $args['id'];
        
        $cnd = new \XLite\Core\CommonCell();

        $orderIds = $this->getOrderIds($productId);

        $products = [];

        if (count($orderIds) > 0) {
            $cnd->{\Qualiteam\SkinActFrequentlyBoughtTogether\Model\Repo\Product::P_FREQ_BOUGHT_ORDER_ITEMS}
                = $orderIds;

            $cnd->{\XLite\Model\Repo\Product::P_EXCL_PRODUCT_ID} = $this->getProMembershipProductsIds();
            $cnd->limit = [0, Config::getInstance()->CDev->ProductAdvisor->cbb_max_count_in_block];
            $cnd->orderBy = ['p.product_id', 'DESC'];

            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd);

            $products = $this->correctExcludeFreqBoughtTogether($products);
            $products = $this->correctFreqBoughtTogetherProductsPosition($products, $productId);
        }

        return array_map(
            function ($product) {
                return $this->mapper->mapToDto($product);
            },
            $products
        );
    }

    protected function getOrderIds($productId)
    {
        return \XLite\Core\Database::getRepo(OrderItem::class)
            ->findFreqBoughtProductsOrderIds($productId);
    }

    protected function getProMembershipProducts()
    {
        return \XLite\Core\Database::getRepo(Product::class)
            ->getProMembershipProducts();
    }

    protected function getProMembershipProductsIds(): array
    {
        $items = $this->getProMembershipProducts();
        $result = [];

        foreach ($items as $item) {
            $result[] = $item->getProductId();
        }

        return $result;
    }
}
