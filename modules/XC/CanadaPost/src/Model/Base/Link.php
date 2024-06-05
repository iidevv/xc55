<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link abstract object
 *
 * @ORM\MappedSuperclass
 */
abstract class Link extends \XLite\Model\AEntity
{
    /**
     * Link relationship types
     */
    public const REL_ARTIFACT               = 'artifact';
    public const REL_COD_REC_OF_DELIVERY    = 'codRecordOfDelivery';
    public const REL_COD_REMIT_LABEL        = 'codRemittanceLabel';
    public const REL_COD_REMIT_RETURN_LABEL = 'codRemittanceReturnLabel';
    public const REL_COMMERCIAL_INVOICE     = 'commercialInvoice';
    public const REL_DETAILS                = 'details';
    public const REL_GROUP                  = 'group';
    public const REL_LABEL                  = 'label';
    public const REL_MANIFEST_SHIPMENTS     = 'manifestShipments';
    public const REL_MANIFEST               = 'manifest';
    public const REL_PRICE                  = 'price';
    public const REL_RETURN_LABEL           = 'returnLabel';
    public const REL_RECEIPT                = 'receipt';
    public const REL_SELF                   = 'self';

    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Relationship to link
     * Indicates the resource related to the current response
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $rel = self::REL_SELF;

    /**
     * Indicates the endpoint to be used to call the web service. It contains the URL and may also contain a query string.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $href;

    /**
     * Present on links to formatted output such as labels. The value starts at 0 for the first page and subsequent pages (if extant) count up by 1.
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $idx;

    /**
     * A character string that indicates the format and version of the data to expect when the web service is invoked.
     * The value in this attribute should be copied to the HTTP header variable "Accept" when the href link is invoked.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $mediaType;

    /**
     * Link's rels that can be downloaded via Get Artifact request
     *
     * @var array
     */
    protected static $artifactRels = [
        self::REL_ARTIFACT,
        self::REL_LABEL,
        self::REL_RETURN_LABEL,
        self::REL_COD_REC_OF_DELIVERY,
        self::REL_COD_REMIT_LABEL,
        self::REL_COD_REMIT_RETURN_LABEL,
        self::REL_COMMERCIAL_INVOICE,
    ];

    /**
     * Canada Post API calls errors
     *
     * @var null|array
     */
    protected $apiCallErrors = null;

    /**
     * Get link title
     */
    public function getLinkTitle()
    {
        return static::getAllowedRels($this->getRel());
    }

    /**
     * Get attachment getter URL
     *
     * @return string
     */
    public function getURL()
    {
        $storage = $this->getStorage();

        return (isset($storage)) ? $storage->getGetterURL() : $this->getGetterURL();
    }

    /**
     * Get attachment getter URL
     *
     * @return string
     */
    public function getGetterURL()
    {
        return \XLite\Core\URLManager::getShopURL(\Includes\Utils\Converter::buildURL('capost_link_storage', 'download', $this->getGetterParams(), \XLite::getCustomerScript()));
    }

    /**
     * Get getter parameters
     *
     * @return array
     */
    protected function getGetterParams()
    {
        return [
            'storage'   => get_called_class(),
            'linkId'    => $this->getId(),
            'returnUrl' => \XLite::getController()->getURL(),
        ];
    }

    /**
     * Return list of all allowed rels
     *
     * @param string $rel Rel to get (OPTIONAL)
     *
     * @return mixed
     */
    public static function getAllowedRels($rel = null)
    {
        $list = [
            static::REL_ARTIFACT               => 'Artifact',
            static::REL_COD_REC_OF_DELIVERY    => 'COD record of delivery',
            static::REL_COD_REMIT_LABEL        => 'COD remittance label',
            static::REL_COD_REMIT_RETURN_LABEL => 'COD remittance return label',
            static::REL_COMMERCIAL_INVOICE     => 'Commercial invoice',
            static::REL_DETAILS                => 'Details',
            static::REL_GROUP                  => 'Group',
            static::REL_LABEL                  => 'Shipping label',
            static::REL_MANIFEST_SHIPMENTS     => 'Manifest shipments',
            static::REL_MANIFEST               => 'Manifest',
            static::REL_PRICE                  => 'Price',
            static::REL_RETURN_LABEL           => 'Return label',
            static::REL_RECEIPT                => 'Receipt',
            static::REL_SELF                   => 'Self',
        ];

        return (isset($rel)) ? ((isset($list[$rel])) ? $list[$rel] : null) : $list;
    }

    /**
     * Return list of all rels file prefixes
     *
     * @param string $rel Rel to get (OPTIONAL)
     *
     * @return mixed
     */
    public static function getAllowedRelsPrefixes($rel = null)
    {
        $list = [
            static::REL_ARTIFACT               => 'A',
            static::REL_COD_REC_OF_DELIVERY    => 'CROD',
            static::REL_COD_REMIT_LABEL        => 'CODRL',
            static::REL_COD_REMIT_RETURN_LABEL => 'CODRRL',
            static::REL_COMMERCIAL_INVOICE     => 'CI',
            static::REL_DETAILS                => 'DET',
            static::REL_GROUP                  => 'GR',
            static::REL_LABEL                  => 'SL',
            static::REL_MANIFEST_SHIPMENTS     => 'MS',
            static::REL_MANIFEST               => 'MAN',
            static::REL_PRICE                  => 'PR',
            static::REL_RETURN_LABEL           => 'RL',
            static::REL_RECEIPT                => 'RP',
            static::REL_SELF                   => 'SELF',
        ];

        return (isset($rel)) ? ((isset($list[$rel])) ? $list[$rel] : null) : $list;
    }

    /**
     * Get filename for PDF documents
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->getRel() . '.pdf';
    }

    // {{{ Canda Post API calls

    /**
     * Get Canada Post API call errors
     *
     * @return null|array
     */
    public function getApiCallErrors()
    {
        return $this->apiCallErrors;
    }

    /**
     * Call "Get Artifact" request (i.e. download PDF file)
     * To get error message you need to call "getApiCallErrors" method (if return is false)
     *
     * @param boolean $flushChanges Flag - flush changes or not
     *
     * @return boolean
     */
    public function callApiGetArtifact($flushChanges = false)
    {
        $result = false;

        if (
            $this->isGetArtifactCallAllowed()
            && $this->getStorageClass()
        ) {
            $storageRootDir = \XLite\Core\Database::getRepo($this->getStorageClass())->getFileSystemRoot();
            if (
                (
                    \Includes\Utils\FileManager::isExists($storageRootDir)
                    && \Includes\Utils\FileManager::isWriteable($storageRootDir)
                )
                || (
                    !\Includes\Utils\FileManager::isExists($storageRootDir)
                    && \Includes\Utils\FileManager::isOperateable($storageRootDir)
                )
            ) {
                $data = \XC\CanadaPost\Core\API::getInstance()->callGetArtifactRequest($this);

                $storageClass = $this->getStorageClass();

                if (
                    isset($data->filePath)
                    && !empty($data->filePath)
                ) {
                    // Save PDF document to storage
                    $storage = $this->getStorage();

                    if (!isset($storage)) {
                        $storage = new $storageClass();
                        $storage->setLink($this);

                        $this->setStorage($storage);
                    }

                    $storage->loadFromLocalFile($data->filePath);
                    $storage->setMime($this->getMediaType());

                    \Includes\Utils\FileManager::deleteFile($data->filePath);

                    $result = true;

                    if ($flushChanges) {
                        \XLite\Core\Database::getEM()->flush();
                    }
                }
            } else {
                \XLite\Core\TopMessage::addError(
                    'Directory X is not writable',
                    [
                        'path' => 'files' . LC_DS . \XLite\Core\Database::getRepo($this->getStorageClass())->getStorageName() . LC_DS,
                    ]
                );
            }

            if (isset($data->errors)) {
                $this->apiCallErrors = $data->errors;
            }
        }

        return $result;
    }

    /**
     * Check - is "Get Artifact" call allowed for this link or not
     *
     * @return boolean
     */
    protected function isGetArtifactCallAllowed()
    {
        return (in_array($this->getRel(), static::$artifactRels));
    }

    /**
     * Get store class
     *
     * @return string
     */
    protected function getStorageClass()
    {
        return '';
    }

    // }}}
}
