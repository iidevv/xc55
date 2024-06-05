<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use QSL\SpecialOffersBase\Model\Repo\SpecialOffer as SpecialOfferRepo;
use QSL\SpecialOffersBase\Model\SpecialOffer as SpecialOfferModel;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Database;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

class SpecialOffers implements ResolverInterface
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
        $cnd = $this->prepareSearchCaseBySearchParams($args, $context);

        $offers = Database::getRepo(SpecialOfferModel::class)->search($cnd);

        return array_map(
            function ($offer) {
                return $this->mapper->mapToDto($offer);
            },
            $offers
        );
    }

    /**
     * @param $args
     * @param $context
     *
     * @return \XLite\Core\CommonCell
     */
    protected function prepareSearchCaseBySearchParams($args, $context)
    {
        $profile = $context->getLoggedProfile();

        $cnd = Database::getRepo(SpecialOfferModel::class)->getActiveOffersConditions($profile);
        $cnd->{SpecialOfferRepo::SEARCH_VISIBLE_OFFERS} = true;

        $from = isset($args['from'])
            ? (int) $args['from']
            : 0;
        $size = isset($args['size'])
            ? (int) $args['size']
            : 0;

        // $size = 0 means without limit
        if ($from || $size) {
            $cnd->{SpecialOfferRepo::P_LIMIT} = [$from, $size];
        }

        return $cnd;
    }
}