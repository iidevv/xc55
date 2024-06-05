<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Mapper;


class Offer
{
    /**
     * @param \CSI\MakeAnOffer\Model\MakeAnOffer $offer
     *
     * @return array
     */
    public function mapOffer($offer)
    {
        return [
            'id'       => $offer->getId(),
            'customer_notes'        => $offer->getCustomerNotes(),
            'admin_notes_visible'   => $offer->getAdminNotesCust(),
            'admin_notes'    => $offer->getAdminNotes(),
            'status'         => $offer->getStatus(),
            'status_name'    => $this->getOfferStatusName(
                $offer->getStatus()
            ),
            'product_id'     => $offer->getProduct()->getProductId(),
            'product_name'   => $offer->getProductName(),
            'product_price'  => $offer->getProductPrice(),
            'offer_price'    => $offer->getOfferPrice(),
            'offer_amount'   => $offer->getOfferQty(),
            'date'           => \XLite\Core\Converter::formatTime($offer->getDate()),
        ];
    }

    /**
     * Get status name
     *
     * @param string $status
     *
     * @return string
     */
    public function getOfferStatusName($status)
    {
        $statusNames = array(
            'A' => (string)static::t('Accepted'),
            'D' => (string)static::t('Declined'),
            'P' => (string)static::t('Pending'),
        );

        return $statusNames[$status];
    }

    /**
     * @param $name
     * @param $args
     * @param $code
     *
     * @return string
     */
    protected static function t($name, array $args = [], $code = null)
    {
        return \XLite\Core\Translation::getInstance()->translate($name, $args, $code);
    }
}
