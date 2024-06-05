<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;
use QSL\SpecialOffersBase\Model\SpecialOffer as SpecialOfferModel;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Database;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

class SpecialOffer implements ResolverInterface
{
    /**
     * @var Mapper\SpecialOffer
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\SpecialOffer $mapper
     */
    public function __construct(Mapper\SpecialOffer $mapper)
    {
        $this->mapper = $mapper;
    }

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $repo = Database::getRepo(SpecialOfferModel::class);

        /** @var \QSL\SpecialOffersBase\Model\SpecialOffer $offer */
        $offer = $repo->findOneBy(['offer_id' => $args['id'], 'enabled' => true]);

        if (!$offer) {
            throw new UserError("There is no model with {$args['id']} id");
        }

        return $this->mapper->mapToDto($offer);
    }
}