<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XcartGraphqlApi\Resolver\ResolverInterface;

class DealBlock implements ResolverInterface
{
    private ModuleManagerDomain $moduleManagerDomain;

    public function __construct()
    {
        $this->moduleManagerDomain = Container::getContainer()->get(ModuleManagerDomain::class);
    }

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return $this->isModuleEnabled()
            ? $this->getDealBlockInfo()
            : $this->getDefaultBlockInfo();
    }

    private function isModuleEnabled(): bool
    {
        return $this->moduleManagerDomain->isEnabled('Qualiteam-SkinActTodaysDeal');
    }

    private function getDefaultBlockInfo(): array
    {
        return [
            'sectionName'   => null,
            'categoryId'    => null,
            'productsCount' => null,
        ];
    }

    private function getDealBlockInfo(): array
    {
        $configurationContainer = Container::getContainer()->get('qualiteam.skinacttodaysdeal.configuration');

        return [
            'sectionName'   => $configurationContainer->getName(),
            'categoryId'    => $configurationContainer->getCategoryId(),
            'productsCount' => $configurationContainer->getLimit(),
        ];
    }
}