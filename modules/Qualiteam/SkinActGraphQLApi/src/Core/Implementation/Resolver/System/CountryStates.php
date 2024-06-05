<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use Doctrine\Common\Collections\Collection;
use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class CountryStates
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class CountryStates implements ResolverInterface
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
        $country = $val['model'];

        $states = [];

        /** @var \XLite\Model\Country $country */
        if ($country) {
            $states = $country->getStates(); // TODO Should be mapped
        }

        if ($states instanceof Collection) {
            $states = $states->toArray();
        }

        return array_map(
            function($state) {
                return $this->mapToDto($state);
            },
            $states
        );
    }

    /**
     * @param \XLite\Model\State $model
     *
     * @return array
     */
    protected function mapToDto($model)
    {
        return [
            'code'    => $model->getCode(),
            'name'    => $model->getState(),
            'country' => $model->getCountry(), // TODO Should be mapped
        ];
    }
}
