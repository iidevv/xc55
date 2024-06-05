<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CSI\MakeAnOffer\Resolver;
use GraphQL\Error\UserError;
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

class PutOffer  extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\PutOffer
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
        return $this->createOffer(
            $context->getCurrency(),
            $context->getLoggedProfile(),
            $args,
            isset($_SERVER['REMOTE_ADDR'])
                ? $_SERVER['REMOTE_ADDR']
                : ''
        );
    }


    /**
     * @param string $emailStr
     *
     * @return boolean
     */
    protected function validateEmail($emailStr)
    {
        return (bool)filter_var($emailStr, FILTER_VALIDATE_EMAIL);
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
    public function createOffer($currency, $profile, $params, $ip)
    {
        $data = $params;

        // check if email is correct format
        if (!$this->validateEmail($params['email'])) {
            throw new UserError('Email has incorrect format');
        }

        /** @var \CSI\MakeAnOffer\Model\Product $product */
        $product = Database::getRepo('XLite\Model\Product')
            ->find($params['product_id']);

        if (!$product) {
            throw new UserError("Can't find product with {$params['product_id']} id");
        }

        // set status based on settings and offer
        $status = 'P';
        $percent = (($product->getNetPrice() - $params['offer_price']) / $product->getNetPrice()) * 100;

        if (0 < $percent
            && 0 < $this->getConfig()->auto_decline_percent
            && $percent >= $this->getConfig()->auto_decline_percent
        ) {
            $status = 'D';
        }

        $offer = new MakeAnOffer();

        Database::getEM()->persist($offer);

        $data['product_name'] = $product->getName();
        $data['product_price'] = $product->getNetPrice();
        $data['ip'] = ip2long($ip) ?: 0;
        $data['date'] = \XLite\Core\Converter::time();
        $data['status'] = $status;
        $data['phone'] = $params['phone'];
        $data['customer_notes'] = $params['comments'];
        $data['offer_qty'] = $params['offer_qty'];

        $offer->map($data);

        if ($profile) {
            $offer->setProfile($profile);
            $profile->addMakeAnOffer($offer);
        }

        if ($product) {
            $offer->setProduct($product);
            $product->addMakeAnOffer($offer);
        }

        Database::getEM()->flush();

        $data['ip'] = long2ip($data['ip']);
        $data['product_price'] = \XLite\View\AView::formatPrice($data['product_price'], $currency);
        $data['offer_price'] = \XLite\View\AView::formatPrice($data['offer_price'], $currency);

        $errorMessage = $this->sendEmailNotifications(
            $params['email'],
            $status,
            $data
        );

        if ($errorMessage) {
            throw new UserError($errorMessage);
        }

        return $this->mapOffer($offer);
    }

    /**
     * @return mixed
     */
    protected function getConfig()
    {
        return \XLite\Core\Config::getInstance()->CSI->MakeAnOffer;
    }

    /**
     * @param $email
     * @param $status
     * @param $data
     *
     * @return null|string
     */
    protected function sendEmailNotifications($email, $status, $data)
    {
        $errorMessage = null;

        // send 'offer received' email to admin
        if (!empty(\XLite\Core\Config::getInstance()->CSI->MakeAnOffer->admin_email)) {
            $errorMessage = Mailer::sendNewMakeAnOfferMessage(
                $data
            );
        }

        Mailer::sendReceivedMakeAnOfferMessage(
            $data
        );

        // send 'auto declined' email to customer
        if ('D' === $status) {
            $data['admin_notes_cust'] = \XLite\Core\Config::getInstance()->CSI->MakeAnOffer->auto_decline_message;

            Mailer::sendDeclinedMakeAnOfferMessage(
                $data
            );
        }

        return $errorMessage;
    }

    /**
     * @param MakeAnOffer $offer
     *
     * @return array
     */
    protected function mapOffer($offer)
    {
        $mapper = new Offer();

        return $mapper->mapOffer($offer);
    }
}
