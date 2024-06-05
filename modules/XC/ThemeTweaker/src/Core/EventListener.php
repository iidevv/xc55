<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core;

use Doctrine\ORM\EntityManagerInterface;
use XC\ThemeTweaker\Model\Template;
use XCart\Event\Service\ViewListEvent;
use XCart\Extender\Mapping\ListChild;
use XCart\Operation\Service\ViewList\Utils\ViewListDocParserInterface;

class EventListener
{
    private EntityManagerInterface $entityManager;

    private ViewListDocParserInterface $docParser;

    public function __construct(
        EntityManagerInterface $entityManager,
        ViewListDocParserInterface $docParser
    ) {
        $this->entityManager = $entityManager;
        $this->docParser     = $docParser;
    }

    public function onViewListsReadBefore(ViewListEvent $event): void
    {
        $lists = [];
        /** @var \XC\ThemeTweaker\Model\Repo\Template $repo */
        $repo = $this->entityManager->getRepository(Template::class);

        /** @var Template[]|null $templates */
        $templates = $repo->findBy([
            'enabled' => true,
        ]);

        foreach ($templates as $template) {
            /** @var ListChild[] $listChildBlocks */
            $listChildBlocks = $this->docParser->parseContent($template->getBody() ?? '');
            $tpl = str_replace(\XLite::INTERFACE_WEB . '/' . \XLite::ZONE_CUSTOMER . '/', '', $template->getTemplate());

            foreach ($listChildBlocks as $listChild) {
                if (empty($listChild->list)) {
                    continue;
                }

                $lists[$this->generateListKey($tpl, $listChild->list)] = [
                    'tpl'       => $tpl,
                    'list'      => $listChild->list,
                    'interface' => \XLite::INTERFACE_WEB,
                    'zone'      => \XLite::ZONE_CUSTOMER,
                    'weight'    =>  $listChild->weight ?: 0,
                ];
            }
        }

        if ($lists) {
            $event->setList(array_merge($event->getList(), $lists));
        }
    }

    private function generateListKey(string $tpl, string $list): string
    {
        return md5(\XLite::INTERFACE_WEB . \XLite::ZONE_CUSTOMER . $tpl . $list);
    }
}
