<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use XcartGraphqlApi\DTO\SpecialOfferDTO;
use XLite\Core\View\RenderingContextInterface;

class SpecialOffer
{
    /**
     * @var RenderingContextInterface
     */
    protected $renderingContext;

    /**
     * @param \QSL\SpecialOffersBase\Model\SpecialOffer $offer
     * @param array                                     $fields
     *
     * @return \XcartGraphqlApi\DTO\SpecialOfferDTO
     */
    public function mapToDto(\QSL\SpecialOffersBase\Model\SpecialOffer $offer, array $fields = [])
    {
        $offerDTO = new SpecialOfferDTO();

        $offerDTO->id = $offer->getOfferId();
        $offerDTO->title = $offer->getTitle();
        $offerDTO->image_url = $offer->getImage() ? $offer->getImage()->getFrontURL() : '';
        $offerDTO->description = $offer->getDescription();
        $offerDTO->short_promo_text = $offer->getShortPromoText();

        return $offerDTO;
    }
}