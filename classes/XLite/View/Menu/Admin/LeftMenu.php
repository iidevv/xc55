<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use XCart\Domain\ModuleManagerDomain;
use XCart\Extender\Mapping\ListChild;
use XLite\Controller\TitleFromController;
use XLite\Core\Auth;
use XLite\Core\Cache\ExecuteCached;
use XLite\View\Menu\Admin\LeftMenu\Communications;

/**
 * Left side menu widget
 *
 * @ListChild (list="admin.main.page.content.left", weight="100", zone="admin")
 */
class LeftMenu extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @var array
     */
    protected $bottomItems;

    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->moduleManagerDomain = \XCart\Container::getContainer()->get(ModuleManagerDomain::class);
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Get menu items
     *
     * @return array
     */
    protected function getItems()
    {
        if (!isset($this->items)) {
            $items = $this->getItemsLeftMenuForDecider();

            $this->setSelectedDecider(
                $this->createSelectedDecider('getItemsLeftMenuForDecider')
            );

            $this->items = $this->prepareItems($items);
        }

        return $this->items;
    }

    /**
     * @return array
     */
    public function getItemsLeftMenuForDecider()
    {
        $cacheParams = [
            'getItemsLeftMenuForDecider',
            get_class($this),
        ];

        return ExecuteCached::executeCachedRuntime(function () {
            $bottomItems = array_map(static function ($item) {
                $item['weight'] += 10000;

                return $item;
            }, $this->defineBottomItems());

            return array_merge(
                $this->defineItems(),
                $bottomItems
            );
        }, $cacheParams);
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = [
            'sales'           => [
                static::ITEM_TITLE      => static::t('Orders'),
                static::ITEM_ICON_SVG   => 'images/left_menu/orders.svg',
                static::ITEM_WEIGHT     => 100,
                static::ITEM_TARGET     => 'order_list',
                static::ITEM_WIDGET      => 'XLite\View\Menu\Admin\LeftMenu\Sales',
                static::ITEM_CHILDREN   => [
                    'order_list'           => [
                        static::ITEM_TITLE       => new TitleFromController('order_list'),
                        static::ITEM_TARGET      => 'order_list',
                        static::ITEM_PERMISSION  => 'manage orders',
                        static::ITEM_WIDGET      => 'XLite\View\Menu\Admin\LeftMenu\Orders',
                        static::ITEM_LABEL_TITLE => static::t('Orders awaiting processing'),
                        static::ITEM_LABEL_LINK  => $this->buildURL('order_list', 'search', ['filter_id' => 'recent']),
                        static::ITEM_WEIGHT      => 100,
                    ],
                    'payment_transactions' => [
                        static::ITEM_TITLE      => static::t('Payments'),
                        static::ITEM_TARGET     => 'payment_transactions',
                        static::ITEM_PERMISSION => 'manage orders',
                        static::ITEM_WEIGHT     => 200,
                    ],
                    'accounting'           => [
                        static::ITEM_TITLE  => new TitleFromController('accounting'),
                        static::ITEM_TARGET => 'accounting',
                        static::ITEM_WEIGHT => 300,
                    ],
                ],
            ],
            'catalog'         => [
                static::ITEM_TITLE    => static::t('Catalog'),
                static::ITEM_ICON_SVG => 'images/left_menu/catalog.svg',
                static::ITEM_TARGET   => 'product_list',
                static::ITEM_WEIGHT   => 200,
                static::ITEM_CHILDREN => [
                    'product_list'      => [
                        static::ITEM_TITLE      => new TitleFromController('product_list'),
                        static::ITEM_TARGET     => 'product_list',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 100,
                    ],
                    'categories'        => [
                        static::ITEM_TITLE      => static::t('Categories'),
                        static::ITEM_TARGET     => 'root_categories',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 200,
                    ],
                    'global_attributes' => [
                        static::ITEM_TITLE      => static::t('Classes & attributes'),
                        static::ITEM_TARGET     => 'global_attributes',
                        static::ITEM_PERMISSION => 'manage catalog',
                        static::ITEM_WEIGHT     => 300,
                    ],
                    'import_export'     => [
                        static::ITEM_TITLE      => static::t('Import & export'),
                        static::ITEM_WEIGHT     => 900,
                        static::ITEM_TARGET     =>
                            Auth::getInstance()->isPermissionAllowed('manage import') ? 'import' : 'export',
                        static::ITEM_PERMISSION => ['manage import', 'manage export'],
                    ]
                ],
            ],
            'communications'  => [
                static::ITEM_TITLE    => static::t('Communication'),
                static::ITEM_ICON_SVG => 'images/left_menu/communications.svg',
                static::ITEM_WIDGET   => Communications::class,
                static::ITEM_WEIGHT   => 300,
                static::ITEM_CHILDREN => [
                    'customer_profiles' => [
                        static::ITEM_TITLE      => static::t('Customers'),
                        static::ITEM_TARGET     => 'customer_profiles',
                        static::ITEM_PERMISSION => 'manage users',
                        static::ITEM_WEIGHT     => 0,
                    ],
                ],
            ],
            'promotions'      => [
                static::ITEM_TITLE    => static::t('Promotions'),
                static::ITEM_ICON_SVG => 'images/left_menu/promotions.svg',
                static::ITEM_WEIGHT   => 300,
                static::ITEM_CHILDREN => [],
            ],
            'store_design'    => [
                static::ITEM_TITLE    => static::t('Design'),
                static::ITEM_ICON_SVG => 'images/left_menu/design.svg',
                static::ITEM_TARGET   => 'layout',
                static::ITEM_WEIGHT   => 450,
                static::ITEM_CHILDREN => [
                    'layout' => [
                        static::ITEM_TITLE  => static::t('Themes'),
                        static::ITEM_TARGET => 'layout',
                        static::ITEM_WEIGHT => 100,
                    ],
                    'front_page' => [
                        static::ITEM_TITLE => static::t('Front page'),
                        static::ITEM_TARGET => 'front_page',
                        static::ITEM_WEIGHT => 200,
                        static::ITEM_PERMISSION => ['manage front page']
                    ],
                    'images' => [
                        static::ITEM_TITLE  => static::t('Images settings'),
                        static::ITEM_TARGET => 'images',
                        static::ITEM_WEIGHT => 300,
                    ],
                ],
            ],
            'store_setup'     => [
                static::ITEM_TITLE    => static::t('Store'),
                static::ITEM_ICON_SVG => 'images/left_menu/store.svg',
                static::ITEM_WEIGHT   => 500,
                static::ITEM_TARGET   => 'settings',
                static::ITEM_EXTRA    => ['page' => 'Company'],
                static::ITEM_CHILDREN => [
                    'store_info'       => [
                        static::ITEM_TITLE  => static::t('Store info'),
                        static::ITEM_TARGET => 'settings',
                        static::ITEM_EXTRA  => ['page' => 'Company'],
                        static::ITEM_WEIGHT => 100,
                    ],
                    'general'          => [
                        static::ITEM_TITLE  => static::t('Cart & checkout'),
                        static::ITEM_TARGET => 'general_settings',
                        static::ITEM_WEIGHT => 300,
                    ],
                    'payment_settings' => [
                        static::ITEM_TITLE  => static::t('Payment Methods'),
                        static::ITEM_TARGET => 'payment_settings',
                        static::ITEM_WEIGHT => 400,
                    ],
                    'shipping_methods' => [
                        static::ITEM_TITLE  => static::t('Shipping'),
                        static::ITEM_TARGET => 'shipping_methods',
                        static::ITEM_WEIGHT => 500,
                    ],
                    'tax_classes'      => [
                        static::ITEM_TITLE  => static::t('Taxes'),
                        static::ITEM_TARGET => 'tax_classes',
                        static::ITEM_WEIGHT => 600,
                    ],
                    'localization'     => [
                        static::ITEM_TITLE  => static::t('Localization'),
                        static::ITEM_TARGET => 'units_formats',
                        static::ITEM_WEIGHT => 700,
                    ],
                    'profile_list'     => [
                        static::ITEM_TITLE      => static::t('Users'),
                        static::ITEM_TARGET     => 'profile_list',
                        static::ITEM_PERMISSION => ['manage admins', 'manage users'],
                        static::ITEM_WEIGHT     => 800,
                    ],
                ],
            ],
            'extensions'      => [
                static::ITEM_TITLE    => static::t('Apps'),
                static::ITEM_ICON_SVG => 'images/left_menu/apps.svg',
                static::ITEM_LINK     => \XLite::getInstance()->getServiceURL('#/installed-addons'),
                static::ITEM_WEIGHT   => 400,
                static::ITEM_TARGET   => 'apps',
                static::ITEM_CHILDREN => [
                    /*'marketplace'      => [
                        static::ITEM_TITLE  => static::t('App Store'),
                        static::ITEM_LINK   => \XLite::getInstance()->getServiceURL('#/marketplace'),
                        static::ITEM_WEIGHT => 100,
                    ],
                    'templates'        => [
                        static::ITEM_TITLE  => static::t('Theme Store'),
                        static::ITEM_LINK   => \XLite::getInstance()->getServiceURL('#/templates'),
                        static::ITEM_WEIGHT => 200,
                    ],*/
                    'installed_addons' => [
                        static::ITEM_TITLE  => static::t('My Apps'),
                        static::ITEM_LINK   => \XLite::getInstance()->getServiceURL('#/installed-addons'),
                        static::ITEM_WEIGHT => 300,
                    ],
                    /*'my_purchases'     => [
                        static::ITEM_TITLE  => static::t('Purchases'),
                        static::ITEM_LINK   => \XLite::getInstance()->getServiceURL('#/my-purchases'),
                        static::ITEM_WEIGHT => 400,
                    ],*/
                ],
            ],
            'marketing'       => [
                static::ITEM_TITLE    => static::t('Channels'),
                static::ITEM_ICON_SVG => 'images/left_menu/channels.svg',
                static::ITEM_WEIGHT   => 350,
                static::ITEM_CHILDREN => [],
            ],
            'reports' => [
                static::ITEM_TITLE     => static::t('Reports'),
                static::ITEM_ICON_SVG  => 'images/left_menu/reports.svg',
                static::ITEM_WEIGHT    => 380,
                static::ITEM_CHILDREN  => [
                    'top_sellers' => [
                        static::ITEM_TITLE      => static::t('Bestsellers'),
                        static::ITEM_TARGET     => 'top_sellers',
                        static::ITEM_WEIGHT     => 100,
                        static::ITEM_PERMISSION => 'manage orders',
                    ],
                    'orders_stats' => [
                        static::ITEM_TITLE      => static::t('Sale statistics'),
                        static::ITEM_TARGET     => 'orders_stats',
                        static::ITEM_WEIGHT     => 200,
                        static::ITEM_PERMISSION => 'manage orders',
                    ],
                ],
            ],
            'common_settings' => [
                static::ITEM_TITLE    => static::t('Settings'),
                static::ITEM_ICON_SVG => 'images/left_menu/settings.svg',
                static::ITEM_WEIGHT   => 800,
                static::ITEM_CHILDREN => [
                    'email_notifications' => [
                        static::ITEM_TITLE  => static::t('Email notifications'),
                        static::ITEM_TARGET => 'notifications',
                        static::ITEM_WEIGHT => 100,
                    ],
                    'email_transfer'      => [
                        static::ITEM_TITLE  => static::t('Email transfer'),
                        static::ITEM_TARGET => 'email_settings',
                        static::ITEM_WEIGHT => 200,
                    ],
                    'api'                 => [
                        static::ITEM_TITLE      => static::t('API'),
                        static::ITEM_TARGET     => 'settings',
                        static::ITEM_EXTRA      => ['page' => 'API'],
                        static::ITEM_PERMISSION => \XLite\Model\Role\Permission::ROOT_ACCESS,
                        static::ITEM_WEIGHT     => 500,
                    ],
                    'seo'                 => [
                        static::ITEM_TITLE  => static::t('SEO'),
                        static::ITEM_TARGET => 'settings',
                        static::ITEM_EXTRA  => ['page' => 'CleanURL'],
                        static::ITEM_WEIGHT => 600,
                    ],
                ],
            ],
            'system_settings' => [
                static::ITEM_TITLE    => static::t('System'),
                static::ITEM_ICON_SVG => 'images/left_menu/system.svg',
                static::ITEM_WEIGHT   => 800,
                static::ITEM_CHILDREN => [
                    'rebuild_cache'     => [
                        static::ITEM_TITLE  => static::t('Cache management'),
                        static::ITEM_TARGET => 'cache_management',
                        static::ITEM_CLASS  => 'rebuild-cache',
                        static::ITEM_WEIGHT => 200,
                    ],
                    'consistency_check' => [
                        static::ITEM_TITLE  => static::t('Consistency check'),
                        static::ITEM_TARGET => 'consistency_check',
                        static::ITEM_WEIGHT => 450,
                    ],
                    'remove_data'       => [
                        static::ITEM_TITLE  => static::t('Remove data'),
                        static::ITEM_TARGET => 'remove_data',
                        static::ITEM_WEIGHT => 700,
                    ],
                ],
            ],
        ];

        if (!\XLite::isTrial()) {
            $items['common_settings'][static::ITEM_CHILDREN] = array_merge(
                $items['common_settings'][static::ITEM_CHILDREN],
                [
                    'css_js_performance' => [
                        static::ITEM_TITLE  => static::t('Performance'),
                        static::ITEM_TARGET => 'css_js_performance',
                        static::ITEM_WEIGHT => 300,
                    ],
                    'security_settings'  => [
                        static::ITEM_TITLE  => static::t('Security'),
                        static::ITEM_TARGET => 'https_settings',
                        static::ITEM_WEIGHT => 400,
                    ],
                ]
            );
            $items['system_settings'][static::ITEM_CHILDREN] = array_merge(
                $items['system_settings'][static::ITEM_CHILDREN],
                [
                    'environment'   => [
                        static::ITEM_TITLE  => static::t('Environment'),
                        static::ITEM_TARGET => 'settings',
                        static::ITEM_EXTRA  => ['page' => 'Environment'],
                        static::ITEM_WEIGHT => 100,
                    ],
                    'view_log_file' => [
                        static::ITEM_TITLE  => static::t('System logs'),
                        static::ITEM_TARGET => 'logs',
                        static::ITEM_WEIGHT => 500,
                    ],
                ]
            );
        }

        if (\XLite::areUpdateNotificationsEnabled()) {
            $items['extensions'][static::ITEM_CHILDREN]['upgrade'] = [
                static::ITEM_TITLE  => static::t('Updates'),
                static::ITEM_LINK   => \XLite::getInstance()->getServiceURL('#/upgrade'),
                static::ITEM_WEIGHT => 500,
            ];
        }

        if ($this->moduleManagerDomain->isEnabled('Kliken-GoogleAds')) {
            $items['marketing'][static::ITEM_CHILDREN]['google_shopping_by_kliken'] = [
                static::ITEM_TITLE  => static::t('Google ads by kliken'),
                static::ITEM_TARGET => 'kga_settings',
                static::ITEM_WEIGHT => 100,
            ];
        }

        $pagesStatic = \XLite\Controller\Admin\Promotions::getPagesStatic();
        if ($pagesStatic) {
            foreach ($pagesStatic as $k => $v) {
                $items['promotions'][static::ITEM_CHILDREN][$k] = [
                    static::ITEM_TITLE      => $v['name'],
                    static::ITEM_TARGET     => 'promotions',
                    static::ITEM_EXTRA      => ['page' => $k],
                    static::ITEM_PERMISSION => !empty($v['permission']) ? $v['permission'] : null,
                    static::ITEM_WEIGHT     => $v['weight'] ?? 0,
                ];

                $items['promotions'][static::ITEM_EXTRA] = ['page' => $k];
            }
        }

        if (!$items['promotions'][static::ITEM_CHILDREN]) {
            $items['promotions'][static::ITEM_TARGET] = 'promotions';
        }

        return $items;
    }

    /**
     * Bottom items
     *
     * @return array
     */
    protected function defineBottomItems()
    {
        return [];
    }

    /**
     * Get content of the dynamic widget that renders 'product-added' css class if product was added to cart.
     *
     * @return int
     */
    public function getRecentOrdersCount()
    {
        $widget = $this->getChildWidget('XLite\View\Menu\Admin\RecentOrdersCount');

        return (int) $widget->getOrdersCount();
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'left_menu';
    }

    /**
     * Get default widget
     *
     * @return string
     */
    protected function getDefaultWidget()
    {
        return 'XLite\View\Menu\Admin\LeftMenu\Node';
    }

    /**
     * Get content of the dynamic widget that renders css classes 'expanded or compressed' for left menu
     *
     * @return string
     */
    public function getClassesForLeftMenu()
    {
        $widget = $this->getChildWidget(LeftMenuState::class);

        return $widget->getContent();
    }

    /**
     * Get container tag attributes
     *
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return [
            'id'       => 'leftMenu',
            'data-spy' => 'affix',
            'class'    => $this->getClassesForLeftMenu(),
        ];
    }

    /**
     * @return bool
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [
                Auth::getInstance()->getProfile()
                    ? Auth::getInstance()->getProfile()->getProfileId()
                    : 'no_profile',
            ]
        );
    }
}
