<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;

/**
 * Class States
 * @package \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\System
 */
class States implements ResolverInterface
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
        $repo = \XLite\Core\Database::getRepo('XLite\Model\State');

        $qb = $repo->createPureQueryBuilder();
        $qb->leftJoin('s.country', 'country');
        $qb->andWhere('country.enabled = :enabled');
        $qb->setParameter('enabled', true);
        $qb->addOrderBy('s.state_id', 'ASC');

        $models  = $qb->execute();

        $mapped = array_map(function ($model) {
            return $this->mapToDto($model);
        }, $models);

        return $mapped;
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
