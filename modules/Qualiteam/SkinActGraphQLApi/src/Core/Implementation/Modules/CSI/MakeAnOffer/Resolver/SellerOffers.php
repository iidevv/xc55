<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Resolver;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Types;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use CSI\MakeAnOffer\Model\Repo\MakeAnOffer;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Mapper\Offer;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class Offers
 *
 * 
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]
 * @Extender\Depend("CSI\MakeAnOffer")
 *
 */

class SellerOffers extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\SellerOffers
{
    use OfferTrait;

    protected $context;

    /**
     * @param                                    $val
     * @param                                    $args
     * @param XCartContext                       $context
     * @param ResolveInfo                        $info
     *
     * @return array|mixed
     * @throws \Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if (!$context->isAuthenticated()) {
            throw new AccessDenied();
        }

        $from = $args['from'] ?? 0;
        $size = $args['size'] ?? 0;
        $filters = $args['filters'] ?? [];

        $this->context = $context;

        return $this->getOffers($from, $size, $filters);
    }

    /**
     * @param $cnd
     */
    protected function processConditions($cnd)
    {
        $profile = $this->context->getLoggedProfile();
        $cnd->{\Qualiteam\ConsignItAwayMakeAnOfferModification\Module\CSI\MakeAnOffer\Model\Repo\MakeAnOffer::SEARCH_VENDOR} = $profile;
    }
}
