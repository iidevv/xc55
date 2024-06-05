<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Command\Other;

use Includes\Utils\FileManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Escaper;
use Symfony\Component\Yaml\Yaml;

/**
 * Class StoreDataToYamlCommand
 * @package XCart\Command
 *
 * TODO Should be refactored
 */
class StoreDataToYamlCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('other:storeDataToYaml')
            ->setDescription('Generate yaml file with the products and categories present in the store')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Demo dump generator');

        $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->findAll();
        $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();

        $rootPath = str_replace('/', LC_DS, LC_DIR_ROOT . 'var/backup/');
        FileManager::mkdirRecursive($rootPath);

        $commonLngCode = \XLite::getDefaultLanguage();
        $io->text(sprintf('%s product(s) found', count($products)));

        $io->text("Generating common products and categories data");
        $yaml = [];
        $yaml[] = Yaml::dump(
            [ 'XLite\Model\Product' => $this->process($this->getMainConfig(), 'XLite\Model\Product', $products) ],
            2
        );
        $yaml[] = Yaml::dump(
            [ 'XLite\Model\Category' => $this->process($this->getMainConfig(), 'XLite\Model\Category', [$category]) ],
            10
        );

        $file = $rootPath . 'demo_dump.yaml';
        $this->writeFile($file, $yaml, 4);
        $io->writeln("<info>$file</info> [OK]");

        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();
        /** @var \XLite\Model\Language $language */
        foreach ($languages as $language) {
            $lngCode = $language->getCode();
            if ($lngCode === $commonLngCode) {
                continue;
            }

            $io->text("Generating $lngCode-translations for products and categories");
            $yaml = [];
            $productsData = $this->process($this->getLanguageConfig($lngCode), 'XLite\Model\Product', $products);
            if ($productsData) {
                $yaml[] = Yaml::dump(
                    [ 'XLite\Model\Product' => $productsData ],
                    2
                );
            }

            $categories = [$category];
            $categoriesData = $this->process($this->getLanguageConfig($lngCode), 'XLite\Model\Category', $categories);
            if ($categoriesData) {
                $yaml[] = Yaml::dump(
                    [ 'XLite\Model\Category' => $categoriesData ],
                    10
                );
            }

            $fileLng = $rootPath . "demo_dump_$lngCode.yaml";
            if ($yaml) {
                $this->writeFile($fileLng, $yaml, 4);
                $io->writeln("<info>$fileLng</info> [OK]");
            } else {
                $io->writeln("<info>$fileLng</info> [EMPTY-SKIPPED]");
            }
        }

        $io->success('Finished');

        return Command::SUCCESS;
    }

    /**
     * Write Yaml data to the file
     *
     * @param string  $f      File path
     * @param array   $yaml   Yaml data
     * @param integer $inline Inline number
     *
     * @return void
     */
    protected function writeFile($f, $yaml, $inline = 2)
    {
        $out = [];

        foreach ($yaml as $k => $v) {
            $out[] = str_replace('[--DBLQUOTE--]', '\\"', $v);
        }

        file_put_contents($f, implode(PHP_EOL, $out));
    }

    protected function process($config, $entityName, $items, $parentEntityName = null)
    {
        $repo = \XLite\Core\Database::getRepo($entityName);
        $properties = $repo->getEntityProperties();

        $excludedFields = !empty($config[$entityName]['excludedFields']) ? $config[$entityName]['excludedFields'] : [];
        $allowedFields = !empty($config[$entityName]['allowedFields']) ? $config[$entityName]['allowedFields'] : [];
        $allowedFieldsShort = $parentEntityName && !empty($config[$entityName]['allowedFieldsShort']) ? $config[$entityName]['allowedFieldsShort'] : [];
        $allowedRelations = !empty($config[$entityName]['allowedRelations']) ? $config[$entityName]['allowedRelations'] : [];
        $getWeightMethod = !empty($config[$entityName]['getWeight']) ? $config[$entityName]['getWeight'] : null;
        $mandatoryFields = !empty($config[$entityName]['mandatoryFields']) ? array_flip($config[$entityName]['mandatoryFields']) : [];
        $mandatoryRelations = !empty($config[$entityName]['mandatoryRelations']) ? array_flip($config[$entityName]['mandatoryRelations']) : [];
        $onlyValues = !empty($config[$entityName]['onlyValues']) ? $config[$entityName]['onlyValues'] : [];

        if ($allowedFieldsShort) {
            $allowedFields = $allowedFieldsShort;
            $excludedFields = [];
            $allowedRelations = [];
        }

        $fieldNames = [];

        if (!isset($parentEntityName) && $entityName == 'XLite\\Model\\Category' && in_array('category_id', $excludedFields)) {
            $excludedFields = array_filter($excludedFields, static function ($i) {
                return $i != 'category_id';
            });
        }

        foreach ($properties[0] as $key => $value) {
            if (
                (empty($allowedFields) || in_array($key, $allowedFields))
                && (empty($excludedFields) || !in_array($key, $excludedFields))
            ) {
                $fieldNames[$key] = $value;
                $fieldNames[$key]['weight'] = $getWeightMethod && is_callable($getWeightMethod) ? $getWeightMethod($key) : 100;
                $fieldNames[$key]['mandatory'] = isset($mandatoryFields[$key]);
            }
        }

        uasort($fieldNames, [$this, 'demoSortByWeight']);

        $relations = [];

        if (!empty($properties[1])) {
            foreach ($properties[1] as $key => $value) {
                if (in_array($key, $allowedRelations)) {
                    $relations[$key] = $value;
                    $relations[$key]['weight'] = $getWeightMethod && is_callable($getWeightMethod) ? $getWeightMethod($key) : 100;
                }
            }
        }

        uasort($relations, [$this, 'demoSortByWeight']);

        $etalon = new $entityName();

        $data = [];

        if (!is_array($items) && !($items instanceof \Doctrine\ORM\PersistentCollection)) {
            $items = [$items];
            $single = true;
        } else {
            $single = false;
        }

        foreach ($items as $item) {
            $allowed = true;

            if (isset($onlyValues)) {
                foreach ($onlyValues as $k => $v) {
                    if ($item->{$fieldNames[$k]['getter']}() != $v) {
                        $allowed = false;
                        break;
                    }
                }
            }

            if (!$allowed) {
                continue;
            }

            $itemData = [];

            foreach ($fieldNames as $fname => $fdata) {
                $value = $item->{$fdata['getter']}();
                if ($fdata['mandatory'] || $value != $etalon->{$fdata['getter']}()) {
                    $itemData[$fname] = (strstr($value, '"') !== false && Escaper::requiresDoubleQuoting($value))
                        ? preg_replace('/"/', '[--DBLQUOTE--]', $value)
                        : $value;
                }
            }
            foreach ($relations as $rname => $rdata) {
                $value = $item->{$rdata['getter']}();
                if (!empty($value)) {
                    $value = $this->process($config, $rdata['entityName'], $value, $entityName);
                    if ($value) {
                        $itemData[$rname] = $value;
                    }
                }

                if (isset($mandatoryRelations[$rname])) {
                    if (empty($itemData[$rname])) {
                        $allowed = false;
                        break;
                    }
                }
            }

            if ($allowed) {
                if ($single) {
                    $data = $itemData;
                } else {
                    $data[] = $itemData;
                }
            }
        }

        return $data;
    }

    /**
     * Get list of fields weights
     *
     * @param string $name Field name
     *
     * @return integer
     */
    protected function demoProductGetWeight($name)
    {
        $weights = [
            'product_id'   => 5,
            'sku'          => 10,
            'cleanURLs'    => 10,
            'inventory'    => 20,
            'images'       => 30,
            'translations' => 40,
        ];

        return $weights[$name] ?? 100;
    }

    /**
     * Get list of fields weights
     *
     * @param string $name Field name
     *
     * @return integer
     */
    protected function demoCategoryGetWeight($name)
    {
        $weights = [
            'category_id'   => 5,
            'cleanURLs'    => 10,
            'image'       => 30,
            'translations' => 40,
            'categoryProducts' => 50,
            'featuredProducts' => 60,
            'children' => 200,
        ];

        return $weights[$name] ?? 100;
    }

    /**
     * Callback function for uasort() to sort items by weight
     *
     * @param array $a First item
     * @param array $b Second item
     *
     * @return integer
     */
    protected function demoSortByWeight($a, $b)
    {
        if ($a['weight'] == $b['weight']) {
            return 0;
        }
        return ($a['weight'] < $b['weight']) ? -1 : 1;
    }

    protected function getMainConfig()
    {
        return [
            'XLite\Model\Product'                                      => [
                'excludedFields'     => [
                    'product_id',
                    'date',
                    'arrivalDate',
                    'updateDate',
                    'needProcess',
                    'ogMeta',
                    'participateSale',
                    'salePriceValue',
                    'freightFixedFee',
                ],
                'allowedRelations'   => [
                    'cleanURLs',
                    'inventory',
                    'images',
                    'translations',
                ],
                'allowedFieldsShort' => [
                    'sku',
                ],
                'getWeight'          => [$this, 'demoProductGetWeight'],
            ],
            'XLite\Model\CleanURL'                                     => [
                'excludedFields' => [
                    'id',
                ],
            ],
            'XLite\Model\Inventory'                                    => [
                'excludedFields'  => [
                    'inventoryId',
                ],
                'mandatoryFields' => [
                    'amount',
                ],
            ],
            'XLite\Model\Image\Product\Image'                          => [
                'excludedFields' => [
                    'id',
                    'date',
                ],
            ],
            'XLite\Model\ProductTranslation'                           => [
                'excludedFields'  => [
                    'label_id',
                ],
                'mandatoryFields' => [
                    'code',
                ],
                'onlyValues'      => [
                    'code' => 'en',
                ],
            ],
            'XLite\Model\Category'                                     => [
                'excludedFields'   => [
                    'category_id',
                    'lpos',
                    'rpos',
                    'depth'
                ],
                'allowedRelations' => [
                    'cleanURLs',
                    'image',
                    'translations',
                    'children',
                    'categoryProducts',
                    'featuredProducts',
                ],
                'getWeight'        => [$this, 'demoCategoryGetWeight'],
            ],
            'XLite\Model\Image\Category\Image'                         => [
                'excludedFields' => [
                    'id',
                    'date',
                ],
            ],
            'XLite\Model\CategoryTranslation'                          => [
                'excludedFields'  => [
                    'label_id',
                ],
                'mandatoryFields' => [
                    'code',
                ],
                'onlyValues'      => [
                    'code' => 'en',
                ],
            ],
            'XLite\Model\CategoryProducts'                             => [
                'excludedFields'   => [
                    'id',
                ],
                'allowedRelations' => [
                    'product',
                ],
            ],
            'CDev\FeaturedProducts\Model\FeaturedProduct' => [
                'excludedFields'   => [
                    'id',
                ],
                'allowedRelations' => [
                    'product',
                ],
            ],
        ];
    }

    protected function getLanguageConfig($lngCode)
    {
        return [
            'XLite\Model\Product'             => [
                'allowedFields'      => [
                    'sku',
                ],
                'allowedRelations'   => [
                    'translations',
                ],
                'mandatoryRelations' => [
                    'translations',
                ],
            ],
            'XLite\Model\ProductTranslation'  => [
                'excludedFields'  => [
                    'label_id',
                ],
                'mandatoryFields' => [
                    'code',
                ],
                'onlyValues'      => [
                    'code' => $lngCode,
                ],
            ],
            'XLite\Model\Category'            => [
                'allowedFields'    => [
                    'category_id',
                ],
                'mandatoryRelations' => [
                    'translations',
                ],
                'allowedRelations' => [
                    'translations',
                    'children',
                ],
            ],
            'XLite\Model\CategoryTranslation' => [
                'excludedFields'  => [
                    'label_id',
                ],
                'mandatoryFields' => [
                    'code',
                ],
                'onlyValues'      => [
                    'code' => $lngCode,
                ],
            ],
        ];
    }
}
