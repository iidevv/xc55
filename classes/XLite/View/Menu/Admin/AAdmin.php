<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use XLite\Core\Cache\ExecuteCached;

/**
 * Abstract admin menu
 */
abstract class AAdmin extends \XLite\View\Menu\AMenu
{
    /**
     * Item parameter names
     */
    public const ITEM_TITLE         = 'title';
    public const ITEM_TOOLTIP       = 'tooltip';
    public const ITEM_LINK          = 'link';
    public const ITEM_BLOCK         = 'block';
    public const ITEM_LIST          = 'list';
    public const ITEM_CLASS         = 'className';
    public const ITEM_TARGET        = 'linkTarget';
    public const ITEM_EXTRA         = 'extra';
    public const ITEM_PERMISSION    = 'permission';
    public const ITEM_PUBLIC_ACCESS = 'publicAccess';
    public const ITEM_CHILDREN      = 'children';
    public const ITEM_WEIGHT        = 'weight';
    public const ITEM_WIDGET        = 'widget';
    public const ITEM_BLANK_PAGE    = 'blankPage';
    public const ITEM_ICON_FONT     = 'iconFont';
    public const ITEM_ICON_SVG      = 'iconSVG';
    public const ITEM_ICON_HTML     = 'iconHTML';
    public const ITEM_ICON_IMG      = 'iconIMG';
    public const ITEM_LABEL         = 'label';
    public const ITEM_LABEL_LINK    = 'labelLink';
    public const ITEM_LABEL_TITLE   = 'labelTitle';
    public const ITEM_DESCRIPTION   = 'description';

    /**
     * Array of targets related to the same menu link
     *
     * @var array
     */
    protected $relatedTargets = [
        'orders_stats'         => [
            'top_sellers',
        ],
        'order_list'           => [
            'order',
        ],
        'import'               => [
            'export',
        ],
        'payment_transactions' => [
            'payment_transaction',
        ],
        'product_list'         => [
            'product',
            'cloned_products'
        ],
        'root_categories'      => [
            'category',
            'categories',
            'category_products',
        ],
        'front_page'           => [
            'banner_rotation',
        ],
        'profile_list'         => [
            'profile',
            'address_book',
            'memberships',
        ],
        'shipping_methods'     => [
            'shipping_sorting',
            'shipping_address',
            'shipping_rates',
            'shipping_test',
            'origin_address',
            'automate_shipping_routine',
        ],
        'countries'            => [
            'zones',
            'states',
        ],
        'payment_settings'     => [
            'payment_method',
            'payment_appearance',
        ],
        'consistency_check'    => [],
        'global_attributes'    => [
            'product_classes',
            'product_class',
            'attributes',
        ],
        'tax_classes'          => [
            'tax_class',
        ],
        'units_formats'        => [
            'currency',
            'countries',
            'states',
            'zones',
            'languages',
            'labels',
        ],
        'theme_tweaker_templates'   => [
        ],
        'general_settings'          => [
            'shipping_settings',
            'address_fields',
        ],
        'notifications'             => [
            'notification',
            'notification_common',
            'notification_attachments',
        ],
        'email_settings'            => [
            'email_settings',
            'test_email'
        ],
    ];

    protected $relatedExtraTargets = [
        ['volume_discount', 'promotions', [], ['page' => 'volume_discounts']],
        ['seo_homepage_settings', 'settings', ['page' => 'SeoHomepage'], ['page' => 'CleanURL']],
        ['seo_page404_settings', 'settings', ['page' => 'CleanURL'], ['page' => 'CleanURL']],
        ['profile', 'customer_profiles', ['profile_type' => 'C'], []],
        ['profile', 'profile_list', ['profile_type' => 'A'], []],
        ['address_book', 'customer_profiles', ['profile_type' => 'C'], []],
        ['address_book', 'profile_list', ['profile_type' => 'A'], []],
    ];

    /**
     * Selected item
     *
     * @var array
     */
    protected $selectedItem = [];

    /**
     * @var SelectedDecider
     */
    protected $selectedDecider;

    /**
     * Return widget directory
     *
     * @return string
     */
    abstract protected function getDir();

    /**
     * Get default widget
     *
     * @return string
     */
    abstract protected function getDefaultWidget();

    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (\XLite\Core\Request::getInstance()->section === 'Communications') {
            // show Memberships tab on the 'Communications / Customers' page as well on the  'Store management / Users' page
            $this->relatedTargets['customer_profiles'][] = 'memberships';
        }

        parent::__construct($params);
    }

    /**
     * Get target weight
     *
     * @param string $currentTarget
     * @param string $target
     * @param array  $extra
     *
     * @return mixed
     */
    public function getTargetWeight(string $currentTarget, string $target, array $extra = [])
    {
        $weights = [0];
        $request = \XLite\Core\Request::getInstance();

        // find in related targets
        $relatedTargets = $this->getRelatedTargets($target);

        if (in_array($currentTarget, $relatedTargets, true)) {
            $weight = 1;

            foreach ($extra as $key => $value) {
                if ($request->$key !== $value) {
                    $weight = 0;
                    continue;
                }

                $weight++;
            }

            $weights[] = $weight;
        }

        // find related targets with extra
        foreach ($this->relatedExtraTargets as [$relatedTarget, $originTarget, $relatedExtra, $originExtra]) {
            if (
                $currentTarget !== $relatedTarget
                || $target !== $originTarget
                || array_diff($extra, $originExtra)
            ) {
                continue;
            }

            $weights[] = 1;

            foreach ($relatedExtra as $key => $value) {
                if ($request->$key !== $value) {
                    continue 2;
                }
            }

            $weights[] = count($relatedExtra) + 1;
        }

        return max($weights);
    }

    /**
     * Sort items
     *
     * @param array $item1 Item 1
     * @param array $item2 Item 2
     *
     * @return int
     */
    protected function sortItems($item1, $item2)
    {
        $weight1 = isset($item1[static::ITEM_WEIGHT]) ? (int) $item1[static::ITEM_WEIGHT] : 0;
        $weight2 = isset($item2[static::ITEM_WEIGHT]) ? (int) $item2[static::ITEM_WEIGHT] : 0;

        return $weight1 > $weight2 ? 1 : -1;
    }

    /**
     * Mark selected
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function markSelected($items)
    {
        if (
            !empty($this->selectedItem)
            && $items
        ) {
            foreach ($items as $index => $item) {
                if ($index == $this->selectedItem['index']) {
                    $item->setWidgetParams(
                        [
                            \XLite\View\Menu\Admin\LeftMenu\Node::PARAM_SELECTED => true,
                        ]
                    );
                    break;
                } elseif ($item->getParam(static::ITEM_CHILDREN)) {
                    $items[$index]->setWidgetParams(
                        [
                            static::ITEM_CHILDREN => $this->markSelected($item->getParam(static::ITEM_CHILDREN)),
                        ]
                    );

                    $result = false;
                    foreach ($item->getParam(static::ITEM_CHILDREN) as $child) {
                        if (
                            $child->getParam(\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_SELECTED)
                            || $child->getParam(\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_EXPANDED)
                        ) {
                            $result = true;
                            break;
                        }
                    }

                    if ($result) {
                        $item->setWidgetParams(
                            [
                                \XLite\View\Menu\Admin\LeftMenu\Node::PARAM_EXPANDED => true,
                            ]
                        );
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Get menu items
     *
     * @return array
     */
    protected function getItems()
    {
        if (!isset($this->items)) {
            $items = $this->defineItems();

            $this->setSelectedDecider(
                $this->createSelectedDecider('getItemsForDecider')
            );

            $this->items = $this->prepareItems($items);
        }

        return $this->items;
    }

    /**
     * Get menu items
     *
     * @return array
     */
    public function getItemsForDecider()
    {
        $cacheParams = [
            'getItemsForDecider',
            get_class($this),
        ];

        return ExecuteCached::executeCachedRuntime(function () {
            return $this->defineItems();
        }, $cacheParams);
    }

    /**
     * @param $getter
     *
     * @return SelectedDecider
     */
    protected function createSelectedDecider($getter)
    {
        return new SelectedDecider(get_class($this), $getter);
    }

    /**
     * Prepare items
     *
     * @param array $items Items
     *
     * @return array
     */
    protected function prepareItems($items)
    {
        $selectedDecider = $this->getSelectedDecider();

        uasort($items, [$this, 'sortItems']);
        foreach ($items as $index => $item) {
            $permissions = [$this->getItemPermission($item)];

            if (
                isset($item[static::ITEM_CHILDREN])
                && is_array($item[static::ITEM_CHILDREN])
                && !empty($item[static::ITEM_CHILDREN])
            ) {
                foreach ($item[static::ITEM_CHILDREN] as $child) {
                    $permissions[] = $this->getItemPermission($child);
                }

                $item[static::ITEM_CHILDREN]                            = $this->prepareItems($item[static::ITEM_CHILDREN]);
                $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_LIST] = $index;
            } elseif (isset($item[static::ITEM_CHILDREN])) {
                $item[static::ITEM_CHILDREN] = [];
            }

            $item[static::ITEM_PERMISSION] = array_merge(...$permissions);

            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_TITLE]   = empty($item[static::ITEM_TITLE])
                ? ''
                : $item[static::ITEM_TITLE];
            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_TOOLTIP] = empty($item[static::ITEM_TOOLTIP])
                ? ''
                : $item[static::ITEM_TOOLTIP];

            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_SELECTED_DECIDER] = $selectedDecider;
            $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_NAME]             = $index;

            if (empty($item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_CLASS]) && is_string($index)) {
                $item[\XLite\View\Menu\Admin\LeftMenu\Node::PARAM_CLASS] = str_replace('_', '-', $index);
            }

            $items[$index] = $this->getWidget(
                $item,
                $item[static::ITEM_WIDGET] ?? $this->getDefaultWidget()
            );

            if (
                !$items[$index]->checkACL()
                || !$items[$index]->isVisible()
            ) {
                unset($items[$index]);
            }
        }

        return $items;
    }

    /**
     * Check if widget is visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        return \XLite\Core\Auth::getInstance()->isAdmin()
            && parent::isVisible();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Returns the list of related targets
     *
     * @param string $target Target name
     *
     * @return array
     */
    public function getRelatedTargets($target)
    {
        return isset($this->relatedTargets[$target])
            ? array_merge([$target], $this->relatedTargets[$target])
            : [$target];
    }

    /**
     * Add related target for selected menu item
     *
     * @param string $target
     * @param string $destTarget
     * @param array  $extra
     * @param array  $destExtra
     *
     * @return $this
     */
    public function addRelatedTarget(string $target, string $destTarget, array $extra = [], array $destExtra = [])
    {
        $this->relatedExtraTargets[] = [
            $target,
            $destTarget,
            $extra,
            $destExtra,
        ];

        return $this;
    }

    /**
     * @param SelectedDecider $selectedDecider
     */
    public function setSelectedDecider($selectedDecider)
    {
        $this->selectedDecider = $selectedDecider;
    }

    /**
     * @return SelectedDecider
     */
    public function getSelectedDecider()
    {
        return $this->selectedDecider;
    }

    /**
     * @param array  $item       Menu item to add permission to
     * @param string $permission Permission code
     *
     * @return array
     */
    protected function addItemPermission($item, $permission)
    {
        if (empty($item[static::ITEM_PERMISSION])) {
            $item[static::ITEM_PERMISSION] = [$permission];
        } else {
            $item[static::ITEM_PERMISSION] = is_array($item[static::ITEM_PERMISSION])
                ? array_merge($item[static::ITEM_PERMISSION], [$permission])
                : [$item[static::ITEM_PERMISSION], $permission];
        }

        return $item;
    }

    /**
     * @param array $item Menu item to add permission to
     *
     * @return array
     */
    protected function getItemPermission($item)
    {
        if (empty($item[static::ITEM_PERMISSION])) {
            return [];
        }

        return is_array($item[static::ITEM_PERMISSION]) ? $item[static::ITEM_PERMISSION] : [$item[static::ITEM_PERMISSION]];
    }
}
