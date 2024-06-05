<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Operation\Service\ViewList;

use Doctrine\Common\Annotations\AnnotationException;
use ReflectionException;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use XCart\Domain\ModuleManagerDomain;
use XCart\Event\Service\ViewListEvent;
use XCart\Extender\Exception\EntityException;
use XCart\Extender\Exception\LogicException;
use XCart\Extender\Extender;
use XCart\Operation\Service\ViewList\Utils\ViewListDocParserInterface;

final class ViewListRead
{
    private string $sourcePath;

    /**
     * [interface] => path
     * [interface] => { [sub-interface] => path } (for mail and pdf)
     *
     * @var array|string[]
     */
    private array $skinModel;

    private ModuleManagerDomain $moduleManagerDomain;

    private EventDispatcherInterface $eventDispatcher;

    private ViewListDocParserInterface $docParser;

    public function __construct(
        string $sourcePath,
        array $skinModel,
        ModuleManagerDomain $moduleManagerDomain,
        EventDispatcherInterface $eventDispatcher,
        ViewListDocParserInterface $docParser
    ) {
        $this->sourcePath          = rtrim($sourcePath, '/') . '/';
        $this->skinModel           = $skinModel;
        $this->moduleManagerDomain = $moduleManagerDomain;
        $this->eventDispatcher     = $eventDispatcher;
        $this->docParser           = $docParser;
    }

    public function __invoke(): array
    {
        $event = new ViewListEvent([]);

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-lists.read.before');

        $event->setList(array_merge($event->getList(), $this->getClassesListData(), $this->getTemplatesListData()));

        $this->eventDispatcher->dispatch($event, 'xcart.service.view-lists.read.after');

        return $event->getList();
    }

    /**
     * Get view data from classes
     *
     * @return array
     * @throws EntityException
     * @throws LogicException
     */
    public function getClassesListData(): array
    {
        $sources = [];

        $moduleIds = $this->moduleManagerDomain->getEnabledModuleIds();
        foreach ($moduleIds as $moduleId) {
            [$author, $name] = explode('-', $moduleId);

            if (is_dir("{$this->sourcePath}modules/{$author}/{$name}/src")) {
                $sources["{$author}\\{$name}"] = "{$this->sourcePath}modules/{$author}/{$name}/src";
            }
        }

        $sources['XLite'] = "{$this->sourcePath}classes/XLite";

        $extender = new Extender();

        $extender
            ->setSources($sources)
            ->setModules($moduleIds)
            ->addSubscribers();

        $sourceMap      = $extender->getSourceMap();
        $viewListReader = $extender->getViewListReader();

        $result = [];
        foreach ($sourceMap->getFiles() as $file) {
            if ($list = $viewListReader->readByPath($file)) {
                $result[] = $list;
            }
        }

        return $this->prepareAnnotationPresets(array_merge(...$result));
    }

    /**
     * Get view lists data from templates
     *
     * @return array
     */
    public function getTemplatesListData(): array
    {
        $result    = [];
        $moduleIds = $this->moduleManagerDomain->getEnabledModuleIds();

        foreach ($this->getAllTemplates() as $template) {
            try {
                if ($annotations = $this->getTemplateAnnotations($template)) {
                    $path = str_replace($this->sourcePath, '', $template);

                    foreach ($annotations as $annotation) {
                        foreach ($this->skinModel as $interface => $zones) {
                            foreach ($zones as $zone => $skinPaths) {
                                foreach ($skinPaths as $skinPath) {
                                    $skinPath = $this->replacePathPattern($skinPath);

                                    if (strpos($path, $skinPath . '/') === 0) {
                                        $tpl = substr($path, strlen($skinPath) + 1);

                                        /** check nested paths */
                                        if (strpos($tpl, 'modules') === 0) {
                                            [, $author, $name] = explode('/', $tpl);

                                            if (!in_array($author . '-' . $name, $moduleIds, true)) {
                                                continue;
                                            }
                                        }

                                        $result[] = [
                                            'tpl'       => $tpl,
                                            'list'      => $annotation->list,
                                            'interface' => $interface,
                                            'zone'      => $zone,
                                            'weight'    => $annotation->weight,
                                        ];

                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (AnnotationException | ReflectionException $e) {
            }
        }

        return $this->prepareAnnotationPresets($result);
    }

    /**
     * Get all enabled templates
     *
     * @return array
     */
    public function getAllTemplates(): array
    {
        $result = [];
        $paths = array_map(fn ($path) => $this->sourcePath . $path, $this->getSkinPaths());
        $paths = array_filter($paths, static fn (string $path) => file_exists($path));

        if ($paths) {
            $finder = Finder::create()
                ->files()
                ->in($paths);

            foreach ($finder as $path) {
                $result[] = $path->getRealPath();
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getSkinPaths()
    {
        return array_unique(array_reduce($this->skinModel, function ($carry, $item) {
            foreach ($item as $paths) {
                foreach ($paths as $path) {
                    $carry[] = $this->replacePathPattern($path);
                }
            }

            return $carry;
        }, []));
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function replacePathPattern(string $input): string
    {
        return str_replace('{{type}}', 'templates', $input);
    }

    /**
     * Read annotations from the template
     *
     * @param string $template
     *
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function getTemplateAnnotations(string $template): array
    {
        return $this->docParser->parse($template);
    }

    /**
     * @param array $annotations
     *
     * @return array
     */
    private function prepareAnnotationPresets(array $annotations): array
    {
        $result            = [];
        $presetAnnotations = [];

        foreach ($annotations as $annotation) {
            if (isset($annotation['preset'])) {
                $presetAnnotations[] = $annotation;
            } else {
                $key          = $this->generateAnnotationKey($annotation);
                $result[$key] = $annotation;
            }
        }

        foreach ($presetAnnotations as $presetAnnotation) {
            $key = $this->generateAnnotationKey($presetAnnotation);
            if (array_key_exists($key, $result)) {
                $presetAnnotation['parent'] = $key;
            }

            $result[$key . $presetAnnotation['preset']] = $presetAnnotation;
        }

        return $result;
    }

    /**
     * @param array $annotation
     *
     * @return string
     */
    private function generateAnnotationKey(array $annotation): string
    {
        $interface = $annotation['interface'] ?? '';
        $zone      = $annotation['zone'] ?? '';
        $tpl       = $annotation['tpl'] ?? '';
        $child     = $annotation['child'] ?? '';
        $name      = !empty($annotation['name']) ? $annotation['name'] : $annotation['list'];

        return md5($interface . $zone . $tpl . $child . $name);
    }
}
