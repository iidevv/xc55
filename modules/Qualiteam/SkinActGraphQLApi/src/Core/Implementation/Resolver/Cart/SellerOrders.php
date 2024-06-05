<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\CommonCell;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper\Order;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class Order
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Cart
 */
class SellerOrders implements ResolverInterface
{
    /**
     * @var Order
     */
    protected $mapper;

    /**
     * CustomerOrders constructor.
     * @param Order $mapper
     */
    public function __construct(Order $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param \XLite\Core\CommonCell $cnd
     * @param array                  $filters
     */
    protected function prepareFilters(\XLite\Core\CommonCell $cnd, array $filters)
    {
        if (isset($filters['searchFilter'])) {
            $cnd->{\XLite\Model\Repo\Order::SEARCH_SUBSTRING} = $filters['searchFilter'];
        }

        if (isset($filters['paymentStatus'])) {
            $cnd->{\XLite\Model\Repo\Order::P_PAYMENT_STATUS} = $filters['paymentStatus'];
        }

        if (isset($filters['shippingStatus'])) {
            $cnd->{\XLite\Model\Repo\Order::P_SHIPPING_STATUS} = $filters['shippingStatus'];
        }

        if (isset($filters['shippingStatus'])) {
            $cnd->{\XLite\Model\Repo\Order::SEARCH_DATE_RANGE} = [$filters['dateRangeFrom'], $filters['dateRangeTo']];
        }

        $date = [];

        if (isset($filters['dateRangeFrom'])) {
            $date[0] = $filters['dateRangeFrom'];
        }

        if (isset($filters['dateRangeTo'])) {
            $date[1] = $filters['dateRangeTo'];
        }

        if (!empty($date)) {
            $cnd->{\XLite\Model\Repo\Order::SEARCH_DATE_RANGE} = $date;
        }
    }

    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $cnd = new CommonCell();

        $cnd->{\XLite\Model\Repo\Order::P_VENDOR_ID} = $context->getLoggedProfile()->getProfileId();
        $cnd->{\XLite\Model\Repo\Order::P_LIMIT} = [$args['start'] ?? 0, $args['limit'] ?? 10];

        $this->prepareFilters($cnd, $args['filters'] ?? []);

        $orders = \XLite\Core\Database::getRepo(\XLite\Model\Order::class)->search($cnd);

        return array_map(
            function ($item) {
                return $this->mapper->mapToDto($item);
            },
            $orders
        );
    }
}
