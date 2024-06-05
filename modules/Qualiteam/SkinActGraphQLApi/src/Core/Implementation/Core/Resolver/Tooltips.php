<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver\Tooltips\Fields;

/**
 * Class Tooltips
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Core\Resolver
 */

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Tooltips extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Tooltips
{
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
        $fields = $this->getFields($args['page'] ?? 'default');
        return $this->prepareTooltipsResult($fields);
    }

    protected function getFields(string $page)
    {
        $fields = new Fields($page);
        $schema = $fields->getTooltipsSchema();

        if ($fields->checkDefaultSchema()) {
            return $this->getPreparedTooltipFields($schema);
        }

        return $this->getTooltipSectionFields($schema);
    }
}
