<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use XLite\View\AResourcesContainer;

final class AssetsInstallCommand extends Command
{
    protected static $defaultName = 'xcart:assets:install';

    private Filesystem $filesystem;

    private $skinModel;

    public function __construct(
        Filesystem $filesystem
    ) {
        parent::__construct();

        $this->filesystem = $filesystem;

        $this->skinModel = \XCart\Container::getContainer()->getParameter('xcart.skin_model');
    }

    protected function configure()
    {
        $this
            ->setDescription('X-Cart core and module resources installation (js, css, images).')
            ->setHelp('Installs resources (js, css, images) from the X-Cart core (assets/*) and modules (modules/*/*/public/*) to the public/assets folder. The operation starts automatically after the rebuild (add-ons enabling/disabling, installation/uninstallation, updates). Options and arguments are not supported.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows       = [];
        $targetPath = LC_DIR_RESOURCES;

        $newCacheTimestamp = AResourcesContainer::refreshCacheTimestamp();
        $output->writeln("New cache timestamp is {$newCacheTimestamp}");
        $output->write('Installing assets');

        $this->filesystem->remove($targetPath);

        foreach ($this->skinModel as $interface => $data) {
            foreach ($data as $zone => $paths) {
                $target = LC_DIR_PUBLIC . 'assets' . LC_DS . $interface . LC_DS . $zone;

                foreach ($paths as $path) {
                    $origin = LC_DIR_ROOT . $this->replacePathPattern($path);

                    try {
                        $this->hardCopy($origin, $target);
                        $rows[] = ["<fg=green;options=bold>\xE2\x9C\x94</>", $origin . ' -> ' . $target];
                    } catch (\Exception $e) {
                        $rows[] = ["<fg=red;options=bold>\xE2\x9C\x98</>", $e->getMessage()];
                    }
                }
            }
        }

        if ($input->getOption('verbose') && $rows) {
            $io = new SymfonyStyle($input, $output);
            $io->newLine();
            $io->table(['', 'Origin -> Target'], $rows);
        }

        // Copy modules images
        if ($this->filesystem->exists(LC_DIR_ROOT . 'modules')) {
            $modulesConfigPath = LC_DIR_ROOT . 'modules/*/*';
            $addonDefaultIcon  = LC_DIR_ROOT . 'assets/web/admin/images/addon_default.png';

            $finder = new Finder();

            $finder->in($modulesConfigPath)->directories();

            $pattern = '/(?<=modules\/)(.*)(?=\/config)/';

            foreach ($finder as $image) {
                preg_match($pattern, $image->getPathname(), $matches, PREG_OFFSET_CAPTURE);

                if ($matches) {
                    $targetModuleDir = LC_DIR_PUBLIC . 'modules' . LC_DS . $matches[0][0] . LC_DS . 'images';

                    if ($image->getRelativePathname() === 'config/images') {
                        $this->hardCopy($image->getPathname(), $targetModuleDir);

                        if (!$this->filesystem->exists($image->getPathname() . '/list_icon.png')) {
                            $this->filesystem->copy(
                                $image->getPathname() . '/icon.png',
                                $targetModuleDir . '/list_icon.png'
                            );
                        }
                    }

                    if (
                        $image->getRelativePathname() === 'config'
                        && !$this->filesystem->exists($image->getPathname() . '/images')
                    ) {
                        $this->filesystem->copy($addonDefaultIcon, $targetModuleDir . '/icon.png');
                        $this->filesystem->copy($addonDefaultIcon, $targetModuleDir . '/list_icon.png');
                    }
                }
            }
        }

        $output->writeln(' <info>OK</info>');

        return Command::SUCCESS;
    }

    private function replacePathPattern(string $input): string
    {
        $pattern     = '{{type}}';
        $replacement = strpos($input, $pattern) === 0 ? 'assets' : 'public';

        return str_replace($pattern, $replacement, $input);
    }

    private function hardCopy(string $originDir, string $targetDir)
    {
        $this->filesystem->mkdir($targetDir, 0777);
        $this->filesystem->mirror($originDir, $targetDir, Finder::create()->ignoreDotFiles(false)->in($originDir)->notName('*.less'));
    }
}
