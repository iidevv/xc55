<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\API\ProfileAddress;

use InvalidArgumentException;
use XLite\Model\Address;
use XLite\Model\Repo\Profile as ProfileRepo;

final class Post
{
    protected ProfileRepo $repository;

    public function __construct(
        ProfileRepo $repository
    ) {
        $this->repository = $repository;
    }

    public function __invoke(Address $data, int $profile_id): Address
    {
        $profile = $this->repository->find($profile_id);
        if (!$profile) {
            throw new InvalidArgumentException(sprintf('Profile with ID %d not found', $profile_id));
        }

        $data->setProfile($profile);

        return $data;
    }
}
