<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Mutations\User;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Model\Address;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\AccessDenied;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\CartServiceException;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Exception\Service\AddressDoesNotExist;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\XCartContext;
use Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
class DeleteUserAddress implements ResolverInterface
{
    /**
     * @var Mapper\User
     */
    private $mapper;

    /**
     * Product constructor.
     *
     * @param Mapper\User $mapper
     */
    public function __construct(Mapper\User $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param              $val
     * @param              $args
     * @param XCartContext $context
     * @param ResolveInfo  $info
     *
     * @return mixed
     * @throws \Exception
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        if (!$context->isAuthenticated() || !$context->getLoggedProfile()) {
            throw new AccessDenied();
        }

        $profile = $context->getLoggedProfile();
        $addressId = $args['address_id'];

        $addressToDelete = null;
        /** @var Address $address */
        foreach ($profile->getAddresses() as $address) {
            if ($address->getAddressId() === (int) $addressId) {
                $addressToDelete = $address;
                break;
            }
        }

        if (!$addressToDelete) {
            throw new AddressDoesNotExist();
        }

        $this->deleteAddress($addressToDelete);

        \XLite\Core\Database::getEM()->flush();

        return $this->mapper->mapToDto($profile, $context);
    }

    /**
     * @param Address $address
     */
    protected function deleteAddress($address)
    {
        $profile = $address->getProfile();
        if ($profile) {
            if ($address->getIsBilling() || $address->getIsShipping()) {
                foreach ($profile->getAddresses() as $profileAddress) {
                    if ($address->getAddressId() != $profileAddress->getAddressId()) {
                        $profileAddress->setIsBilling($profileAddress->getIsBilling() || $address->getIsBilling());
                        $profileAddress->setIsShipping($profileAddress->getIsShipping() || $address->getIsShipping());

                        break;
                    }
                }
            }

            $profile->getAddresses()->removeElement($address);
        }

        $address->delete();
    }
}