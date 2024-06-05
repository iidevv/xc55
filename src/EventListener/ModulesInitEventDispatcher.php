<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\EventListener;

use Symfony\Contracts\EventDispatcher\Event;
use XCart\Domain\HookManagerDomain;
use XCart\Domain\ModuleManagerDomain;

final class ModulesInitEventDispatcher
{
    public const NAME = 'xcart.modules.init';

    private HookManagerDomain $hookManager;

    private ModuleManagerDomain $moduleManager;

    public function __construct(
        HookManagerDomain $hookManager,
        ModuleManagerDomain $moduleManager
    ) {
        $this->hookManager   = $hookManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @throws \XCart\Exception\HookManagerException
     */
    public function dispatchModulesInitEvent(Event $event)
    {
        foreach ($this->moduleManager->getEnabledModuleIds() as $moduleId) {
            $this->hookManager->runHook([
                'moduleId' => $moduleId,
                'hookType' => HookManagerDomain::HOOK_TYPE_INIT,
                'event'    => $event,
            ]);
        }
    }
}
