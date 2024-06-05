<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\CDev\ContactUs\Resolver;

use CDev\ContactUs\Model\Contact;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class Products
 *
 *
 */
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;
use XLite\Core\Mailer;

/**
 * @Extender\Mixin
 * [t-converted]
 * @Extender\Depend("CDev\ContactUs")
 *
 */
class ContactUs extends \Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Modules\ContactUs
{
    /**
     * @param                                    $val
     * @param                                    $args
     * @param \XcartGraphqlApi\ContextInterface  $context
     * @param ResolveInfo                        $info
     *
     * @return bool
     * @throws \RuntimeException
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        Mailer::sendContactUsMessage(
            (new Contact())
                ->setEmail($args['email'])
                //->setName($args['name'])
                ->setCompany($args['company'])
                ->setFirstname($args['firstname'])
                ->setLastname($args['lastname'])
                ->setStreet($args['address'])
                ->setStreet2($args['address2'])
                ->setCity($args['city'])
                ->setCountry($args['country'])
                ->setState($args['state'])
                ->setZipcode($args['zipcode'])
                ->setPhone($args['phone'])
                ->setFax($args['fax'])
                ->setSite($args['url'])
                ->setDepartment($args['department'])
                ->setSubject($args['subject'])
                ->setMessage($args['message']),
            Config::getInstance()->CDev->ContactUs->email
                ?: Mailer::getSupportDepartmentMails()
        );

        return true;
    }
}
