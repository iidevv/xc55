<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;


use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XLite\Core\Converter;
use XLite\Model\Product;
use XLite\Model\Order;

class IconLinks implements \XcartGraphqlApi\Resolver\ResolverInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $pages = [
            'profile_transactions' => [
                'target' => 'profile_transactions',
                'params' => [
                    'vendor_product_editor' => 1,
                ],
            ],
            'shipping_methods'    => [
                'target' => 'shipping_methods',
                'params' => [
                    'vendor_product_editor' => 1,
                ],
            ],
            'saved_cards'         => 'xpayments_cards',
            'add_product'         => [
                'target' => 'product',
                'params' => [
                    'vendor_product_editor' => 1,
                ],
            ],
            'vendor_product_list' => [
                'target' => 'product_list',
                'params' => [
                    'vendor_product_editor' => 1,
                ],
            ],
            //'vendor_order'        => 'order',
            'order_messages'      => 'order',
            'vendor_order_list'   => [
                'order_list',
                'params' => ['vendor_product_editor' => 1],
            ],
            'customer_order_list' => 'order_list',
            'vendor_messages'     => [
                'target' => 'messages',
                'params' => ['vendor_product_editor' => 1]
            ]
        ];
        $links = [];
        foreach ($pages as $name => $page) {
            switch ($name) {
                case 'profile_transactions':
                case 'shipping_methods':
                case 'vendor_order_list':
                case 'vendor_product_list':
                case 'vendor_messages':
                    $links[] = [
                        'url'  => $this->buildUrl($page['target'] ?? $page, $page['params'] ?? []),
                        'page' => $name,
                    ];
                    break;
                case 'saved_cards':
                    $links[] = [
                        'url'  => $this->buildUrl($page['target'] ?? $page, $page['params'] ?? [], false),
                        'page' => $name,
                    ];
                    break;
                case 'add_product':
                    $links[] = [
                        'url'  => $this->getProductUrl($args['product_id'] ?? null),
                        'page' => $name,
                    ];
                    break;
                case 'vendor_order':
                    $url = $this->getOrderUrl($args['order_id'] ?? null, ['vendor_product_editor' => 1]);
                    if ($url) {
                        $links[] = [
                            'url'  => $url,
                            'page' => $name,
                        ];
                    }
                    break;
                case 'customer_order':
                    $url = $this->getOrderUrl($args['order_id'] ?? null, [], false);
                    if ($url) {
                        $links[] = [
                            'url'  => $url,
                            'page' => $name,
                        ];
                    }
                    break;
                case 'order_messages':
                    $url = $this->getOrderUrl($args['order_id'] ?? null, ['page' => 'messages', 'vendor_product_editor' => 1]);
                    if ($url) {
                        $links[] = [
                            'url'  => $url,
                            'page' => $name,
                        ];
                    }
                    break;
                case 'customer_order_list':
                    $links[] = [
                        'url'  => $this->buildUrl($page, [], false),
                        'page' => $name,
                    ];
                    break;
            }
        }

        return $links;
    }

    protected function getProductUrl($productId)
    {
        $params = [];

        if ($productId) {
            $repo = \XLite\Core\Database::getRepo(Product::class);

            $model = $repo->find($productId);

            $model && $params = [
                'product_id' => $productId,
                'vendor_product_editor' => 1,
            ];
        }

        return $this->buildUrl('product', $params);
    }

    protected function getOrderUrl($orderId, $params = [], $onAdmin = true)
    {
        $url = '';
        if ($orderId) {
            $repo = \XLite\Core\Database::getRepo(Order::class);

            $order = $repo->find($orderId);

            $order && $params = array_merge(['order_number' => $order->getOrderNumber()], $params);
            $order && $url = $this->buildUrl('order', $params, $onAdmin);
        }
        return $url;
    }

    protected function buildUrl($target, $params = [], $admin = true)
    {
        return Converter::buildFullURL($target, '', $params, $admin ? \XLite::getAdminScript() : \XLite::getCustomerScript(), false);
    }
}