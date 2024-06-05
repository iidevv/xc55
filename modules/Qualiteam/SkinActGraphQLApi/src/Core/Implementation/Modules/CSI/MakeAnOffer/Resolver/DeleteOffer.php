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

class DeleteOffer  extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\DeleteOffer
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
        return $this->deleteOffer(
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
    public function deleteOffer($currency, $profile, $params, $ip)
    {
        $offer = Database::getRepo(MakeAnOffer::class)->find($params['id']);

        if ($this->allowToDeleteOffer($offer, $profile)) {

            $data = $this->mapOffer($offer);
            $offer->delete();

            Database::getEM()->flush();

            return $data;
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
    protected function allowToDeleteOffer($offer, $profile)
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
