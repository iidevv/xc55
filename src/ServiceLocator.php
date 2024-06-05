<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Twig\Environment;

class ServiceLocator
{
    private Environment $twig;

    private SessionInterface $session;

    public function __construct(
        Environment $twig,
        SessionInterface $session
    ) {
        $this->twig = $twig;
        $this->session = $session;
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * @param Environment $twig
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }
}
