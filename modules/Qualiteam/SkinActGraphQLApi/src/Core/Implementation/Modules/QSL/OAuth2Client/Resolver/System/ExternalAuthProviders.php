<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\OAuth2Client\Resolver\System;

use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use Includes\Utils\Module\Manager;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XcartGraphqlApi\Types\Model\AppData\HomePageWidgetType;
use XLite\Core\CommonCell;
use XLite\Core\Translation;
use QSL\OAuth2Client\Model\Provider;


use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("QSL\OAuth2Client")
 *
 */

class ExternalAuthProviders extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System\ExternalAuthProviders
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
        $mapper = new \Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\QSL\OAuth2Client\Mapper\Provider();
        return array_map(function($item) use ($mapper) {
            return $mapper->mapToArray($item);
        }, $this->getAvailableProviders());
    }

    protected function getAvailableProviders()
    {
        $providers = \XLite\Core\Database::getRepo("\QSL\OAuth2Client\Model\Provider")->findActive();

        return array_filter($providers, function ($item) {
            /** @var Provider $item */
            return $item->getWrapper() && $item->getWrapper()->isConfigured();
        });
    }
}
