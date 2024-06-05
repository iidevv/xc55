<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Core\Mail;

use XLite\Core\Mailer;

class WishlistMessage extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return 'modules/QSL/MyWishlist/send_wishlist';
    }

    public function __construct($mails, $wishlist)
    {
        parent::__construct();

        $products   = $this->getWishlistProducts($wishlist);
        $this->setTo($mails);
        $this->setFrom(Mailer::getSiteAdministratorMail());
        $this->appendData([
            'products'  => $products,
            'customer'  => $wishlist->getCustomer(),
        ]);
        $this->addReplyTo([
            'address' => $wishlist->getCustomer()->getLogin(),
            'name'    => $wishlist->getCustomer()->getName(),
        ]);
    }

    /**
     * Define the wishlist products
     *
     * @param \QSL\MyWishlist\Model\Wishlist $wishlist Wishlist model
     *
     * @return array
     */
    protected function getWishlistProducts($wishlist)
    {
        $links = $wishlist->getWishlistLinks();
        $links = is_null($links) ? new \Doctrine\Common\Collections\ArrayCollection() : $links;
        $result = [];

        foreach ($links as $link) {
            if ($link->getParentProduct()) {
                $result[] = $link->getParentProduct();
            }
        }

        return $result;
    }
}
