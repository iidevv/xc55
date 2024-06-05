<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Security\Http\Authenticator\TokenRepository;

use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;

final class ConfigTokenRepository implements TokenRepositoryInterface
{
    private $readOnlyToken;
    private $readWriteToken;

    private \XLite\Model\Repo\Config $configRepository;

    public function __construct(\XLite\Model\Repo\Config $configRepository)
    {
        $this->configRepository = $configRepository;
        $this->readOnlyToken = $this->getApiParam('token_read');
        $this->readWriteToken = $this->getApiParam('token_all');
    }

    private function getApiParam(string $name): ?string
    {
        /** @var \XLite\Model\Config $param */
        $param = $this->configRepository->findOneBy(['category' => 'API', 'name' => $name]);

        return $param ? $param->getValue() : null;
    }

    public function getUserByToken(string $token): ?UserInterface
    {
        if (!$token) {
            return null;
        }

        if ($token === $this->readOnlyToken) {
            return new InMemoryUser(
                'read_only',
                null,
                ['ROLE_USER'],
            );
        }

        if ($token === $this->readWriteToken) {
            return new InMemoryUser(
                'read_only',
                null,
                ['ROLE_ADMIN'],
            );
        }

        return null;
    }
}
