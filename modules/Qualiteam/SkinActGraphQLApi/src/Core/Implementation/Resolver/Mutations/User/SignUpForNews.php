<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User;


use GraphQL\Type\Definition\ResolveInfo;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CommonError;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Core\Validator\String\Email;

class SignUpForNews implements ResolverInterface
{

    protected function isSubscribedAlready($email)
    {
        return (bool)\XLite\Core\Database::getRepo('XC\NewsletterSubscriptions\Model\Subscriber')
            ->findOneByEmail($email);
    }

    protected function doSubscribe($email, $context)
    {
        $subscriber = new \XC\NewsletterSubscriptions\Model\Subscriber();
        $subscriber->setEmail($email);

        if ($context->isAuthenticated() && $context->getLoggedProfile()) {
            $subscriber->setProfile(
                $context->getLoggedProfile()
            );
        }

        \XLite\Core\Database::getEM()->persist($subscriber);
        \XLite\Core\Database::getEM()->flush($subscriber);
    }

    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        $email = $args['email'];

        try {

            (new Email(true))->validate($email);

            if (!$this->isSubscribedAlready($email)) {
                $this->doSubscribe($email, $context);
            }

        } catch (\XLite\Core\Validator\Exception $e) {

            throw new CommonError($e->getMessage());

        }

        return true;
    }
}