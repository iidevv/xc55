<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Core\Tabs;

use XCart\Container;

class TabsFactory
{
    protected array $tabs;

    public function __construct()
    {
        $this->tabs = [
            'main'        => Container::getContainer()->get('shipstation.tabs.advanced'),
            'statuses'    => Container::getContainer()->get('shipstation.tabs.statuses'),
        ];
    }

    public static function getTabsData(): Tabs
    {
        return new Tabs(
            (new TabsFactory)->prepareTabs(),
            (new TabsFactory)->prepareAllowedTargets(),
        );
    }

    protected function prepareTabs(): array
    {
        $list = [];

        foreach ($this->getTabs() as $tab) {
            $list = array_merge($list, $tab->defineTabs());
        }

        return $list;
    }

    protected function getTabs(): array
    {
        return $this->tabs;
    }

    protected function prepareAllowedTargets(): array
    {
        $list = [];

        foreach ($this->getTabs() as $tab) {
            $list = array_merge($list, [
                $tab->getAllowedTarget(),
            ]);
        }

        return $list;
    }
}