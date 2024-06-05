<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Resolver;

use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use XLite\Core\Database;
use XLite\Core\Mailer;
use CSI\MakeAnOffer\Model\MakeAnOffer;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Mapper\Offer;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;

/**
 * Class PutOffer
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

class UpdateOffer  extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\UpdateOffer
{
    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \GraphQL\Error\UserError
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return $this->updateOffer(
            $context->getCurrency(),
            $context->getLoggedProfile(),
            $args,
            isset($_SERVER['REMOTE_ADDR'])
                ? $_SERVER['REMOTE_ADDR']
                : ''
        );
    }

    /**
     * @param $currency
     * @param $profile
     * @param $params
     * @param $ip
     *
     * @return mixed
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \GraphQL\Error\UserError
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOffer($currency, $profile, $params, $ip)
    {
        /**
         * @var MakeAnOffer $offer
         */
        $offer = Database::getRepo(MakeAnOffer::class)->find($params['id']);

        if ($this->allowToUpdateOffer($offer, $profile)) {

            $offer->setStatus($params['status']);

            if ($params['not_visible_notes']) {
                $offer->setAdminNotes($params['not_visible_notes']);
            }

            if (isset($params['visible_notes'])) {
                $offer->setAdminNotesCust($params['visible_notes']);
            }

            if ($params['send_changes_email']) {
                if ('A' === $params['status']) {
                    Mailer::sendAcceptedMakeAnOfferMessage($offer);
                } elseif ('D' === $params['status']) {
                    Mailer::sendDeclinedMakeAnOfferMessage($offer);
                }
            }

            Database::getEM()->flush();

            return $this->mapOffer($offer);
        } else {
            throw new Error('No such offer');
        }
    }

    /**
     * @param MakeAnOffer $offer
     * @param \XLite\Model\Profile $profile
     *
     * @return bool
     */
    protected function allowToUpdateOffer($offer, $profile)
    {
        return $offer
            && $profile
            && $offer->getProduct()->getVendor()
            && $offer->getProduct()->getVendor()->getProfileId() === $profile->getProfileId();
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
