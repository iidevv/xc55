<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Resolver;


use XLite\Core\CommonCell;
use XLite\Core\Database;
use CSI\MakeAnOffer\Model\Repo\MakeAnOffer;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Mapper\Offer;

trait OfferTrait {

    /**
     * Search offers
     *
     * @param integer $from Request from
     * @param integer $size Request size
     * @param array  $filters
     *
     * @return array
     */
    protected function getOffers($from, $size, $filters)
    {
        $result = [];
        $cnd = new CommonCell();

        if (isset($filters['name'])) {
            $cnd->{MakeAnOffer::SEARCH_NAME} = $filters['name'];
        }

        if (isset($filters['email'])) {
            $cnd->{MakeAnOffer::SEARCH_EMAIL} = $filters['email'];
        }

        if (isset($filters['status'])) {
            $cnd->{MakeAnOffer::SEARCH_STATUS} = $filters['status'];
        }

        if (isset($filters['orderBy'])) {
            $cnd->{MakeAnOffer::P_ORDER_BY} = array(
                $filters['orderBy']['name'],
                $filters['orderBy']['order'],
            );
        }

        $range = [];

        if (isset($filters['dateRangeFrom'])) {
            $range[] = $filters['dateRangeFrom'];
        }

        if (isset($filters['dateRangeTo'])) {
            $range[] = $filters['dateRangeTo'];
        }

        if (count($range) > 0) {
            $cnd->{MakeAnOffer::SEARCH_DATE_RANGE} = $range;
        }

        $cnd->{MakeAnOffer::P_LIMIT} = [$from, $size];

        $this->processConditions($cnd);

        /** @var \CSI\MakeAnOffer\Model\MakeAnOffer[] $offers */
        $offers = Database::getRepo(\CSI\MakeAnOffer\Model\MakeAnOffer::class)
            ->search($cnd);

        if ($offers) {
            foreach ($offers as $offer) {
                $result[] = $this->mapOffer($offer);
            }
        }

        return $result;
    }

    /**
     * @param \CSI\MakeAnOffer\Model\MakeAnOffer $offer
     *
     * @return array
     */
    protected function mapOffer($offer)
    {
        $mapper = new Offer();

        return $mapper->mapOffer($offer);
    }

}
