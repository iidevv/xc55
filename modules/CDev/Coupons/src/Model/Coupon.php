<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use CDev\Coupons\API\Endpoint\Coupon\DTO\CouponInput;
use CDev\Coupons\API\Endpoint\Coupon\DTO\CouponOutput;
use Doctrine\ORM\Mapping as ORM;

/**
 * Coupon
 *
 * @ORM\Entity
 * @ORM\Table  (name="coupons",
 *      indexes={
 *          @ORM\Index (name="ce", columns={"code", "enabled"})
 *      }
 * )
 *
 * @ORM\HasLifecycleCallbacks
 * @ApiPlatform\ApiResource(
 *     input=CouponInput::class,
 *     output=CouponOutput::class,
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/coupons/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/coupons/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/coupons/{id}.{_format}",
 *              "identifiers"={"id"},
 *              "requirements"={"id"="\d+"}
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "identifiers"={},
 *              "path"="/coupons.{_format}"
 *          },
 *          "post"={
 *              "method"="POST",
 *              "identifiers"={},
 *              "path"="/coupons.{_format}"
 *          }
 *     }
 * )
 */
class Coupon extends \XLite\Model\AEntity
{
    /**
     * Coupon types
     */
    public const TYPE_PERCENT  = '%';
    public const TYPE_ABSOLUTE = '$';

    /**
     * Coupon validation error codes
     */
    public const ERROR_DISABLED      = 'disabled';
    public const ERROR_EXPIRED       = 'expired';
    public const ERROR_USES          = 'uses';
    public const ERROR_TOTAL         = 'total';
    public const ERROR_PRODUCT_CLASS = 'product_class';
    public const ERROR_MEMBERSHIP    = 'membership';
    public const ERROR_SINGLE_USE    = 'singleUse';
    public const ERROR_SINGLE_USE2   = 'singleUse2';
    public const ERROR_CATEGORY      = 'category';


    /**
     * Product unique ID
     *
     * @var   integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Code
     *
     * @var   string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=16)
     */
    protected $code;

    /**
     * Enabled status
     *
     * @var   boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Value
     *
     * @var   float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $value = 0.0000;

    /**
     * Type
     *
     * @var   string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $type = self::TYPE_PERCENT;

    /**
     * Comment
     *
     * @var   string
     *
     * @ORM\Column (type="string", length=64)
     */
    protected $comment = '';

    /**
     * Uses count
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $uses = 0;

    /**
     * Date range (begin)
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $dateRangeBegin = 0;

    /**
     * Date range (end)
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $dateRangeEnd = 0;

    /**
     * Total range (begin)
     *
     * @var   float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $totalRangeBegin = 0;

    /**
     * Total range (end)
     *
     * @var   float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $totalRangeEnd = 0;

    /**
     * Uses limit
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $usesLimit = 0;

    /**
     * Uses limit per user
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $usesLimitPerUser = 0;

    /**
     * Flag: Can a coupon be used together with other coupons (false) or no (true)
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $singleUse = false;

    /**
     * Flag: Coupon is used for specific products or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $specificProducts = false;

    /**
     * Product classes
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\ProductClass", inversedBy="coupons")
     * @ORM\JoinTable (name="product_class_coupons",
     *      joinColumns={@ORM\JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="class_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $productClasses;

    /**
     * Memberships
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Membership", inversedBy="coupons")
     * @ORM\JoinTable (name="membership_coupons",
     *      joinColumns={@ORM\JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")}
     * )
     */
    protected $memberships;

    /**
     * Zones
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Zone", inversedBy="coupons")
     * @ORM\JoinTable (name="zone_coupons",
     *      joinColumns={@ORM\JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")}
     * )
     */
    protected $zones;

    /**
     * Coupon products
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="CDev\Coupons\Model\CouponProduct", mappedBy="coupon", cascade={"persist"})
     */
    protected $couponProducts;

    /**
     * Used coupons
     *
     * @var   \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\Coupons\Model\UsedCoupon", mappedBy="coupon")
     */
    protected $usedCoupons;

    /**
     * Categories
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Category", inversedBy="coupons")
     * @ORM\JoinTable (name="coupon_categories",
     *      joinColumns={@ORM\JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")}
     * )
     */
    protected $categories;

    protected static $runtimeCacheForUsedCouponsCount = [];

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->productClasses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->memberships    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->zones    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->couponProducts    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->usedCoupons    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories     = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Check - discount is absolute or not
     *
     * @return boolean
     */
    public function isAbsolute()
    {
        return $this->getType() === static::TYPE_ABSOLUTE;
    }

    /**
     * Check - coupon is started
     *
     * @return boolean
     */
    public function isStarted()
    {
        return $this->getDateRangeBegin() === 0 || $this->getDateRangeBegin() < \XLite\Core\Converter::time();
    }

    /**
     * Check - coupon is expired or not
     *
     * @return boolean
     */
    public function isExpired()
    {
        return 0 < $this->getDateRangeEnd() && $this->getDateRangeEnd() < \XLite\Core\Converter::time();
    }

    /**
     * Check coupon activity
     *
     * @param \XLite\Model\Order $order Order OPTIONAL
     *
     * @return boolean
     */
    public function isActive(\XLite\Model\Order $order = null)
    {
        try {
            $result = $this->checkCompatibility($order);
        } catch (\CDev\Coupons\Core\CompatibilityException $exception) {
            $result = false;
        }

        return $result;
    }

    /**
     * Get public code
     *
     * @return string
     */
    public function getPublicCode()
    {
        return $this->getCode();
    }

    /**
     * Get coupon public name
     *
     * @return string
     */
    public function getPublicName()
    {
        $suffix = '';

        if ($this->getType() === \CDev\Coupons\Model\Coupon::TYPE_PERCENT) {
            $suffix = sprintf('(%s%%)', $this->getValue());
        }

        return $this->getPublicCode() . ' ' . $suffix;
    }

    // {{{ Amount

    /**
     * Get amount
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    public function getAmount(\XLite\Model\Order $order)
    {
        $total = $this->getOrderTotal($order);

        return $this->isAbsolute()
            ? min($total, $this->getValue())
            : ($total * $this->getValue() / 100);
    }

    /**
     * Get order total
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return float
     */
    protected function getOrderTotal(\XLite\Model\Order $order)
    {
        return array_reduce($this->getValidOrderItems($order), static function ($carry, $item) {
            return $carry + $item->getSubtotal();
        }, 0);
    }

    /**
     * Get order items which are valid for the coupon
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return array
     */
    protected function getValidOrderItems($order)
    {
        return $order->getValidItemsByCoupon($this);
    }

    /**
     * Is coupon valid for product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return boolean
     */
    public function isValidForProduct(\XLite\Model\Product $product)
    {
        $result = true;

        if (0 < count($this->getProductClasses())) {
            // Check product class
            $result = $product->getProductClass()
                && $this->getProductClasses()->contains($product->getProductClass());
        }

        if ($result && 0 < count($this->getCategories())) {
            // Check categories
            $result = false;
            foreach ($product->getCategories() as $category) {
                if ($this->getCategories()->contains($category)) {
                    $result = true;
                    break;
                }
            }
        }

        if ($result && $this->getSpecificProducts()) {
            // Check product
            $result = in_array($product->getProductId(), $this->getApplicableProductIds());
        }

        return $result;
    }

    // }}}

    /**
     * Check coupon compatibility
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return boolean
     */
    public function checkCompatibility(\XLite\Model\Order $order = null)
    {
        if (!$this->getEnabled()) {
            $this->throwCompatibilityException(
                '',
                'Sorry, the coupon you entered is invalid. Make sure the coupon code is spelled correctly'
            );
        }

        $this->checkDate();
        $this->checkUsage();

        if ($order) {
            if ($order->getProfile()) {
                $this->checkPerUserUsage($order->getProfile(), $order->containsCoupon($this));
            }
            $this->checkConflictsWithCoupons($order);
            $this->checkMembership($order);
            $this->checkCategory($order);
            $this->checkProductClass($order);
            $this->checkProducts($order);
            $this->checkOrderTotal($order);
            $this->checkZone($order);
        }

        return true;
    }

    // {{{ Date

    /**
     * Check coupon dates
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkDate()
    {
        if (!$this->isStarted()) {
            $this->throwCompatibilityException(
                '',
                'Sorry, the coupon you entered is invalid. Make sure the coupon code is spelled correctly'
            );
        }
        if ($this->isExpired()) {
            $this->throwCompatibilityException(
                '',
                'Sorry, the coupon has expired'
            );
        }
    }

    // }}}

    // {{{ Usage

    /**
     * Check coupon usages
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkUsage()
    {
        if (0 < $this->getUsesLimit() && $this->getUsesLimit() <= $this->getUses()) {
            $this->throwCompatibilityException(
                '',
                'Sorry, the coupon use limit has been reached'
            );
        }
    }

    /**
     * Check coupon usages per user
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkPerUserUsage(\XLite\Model\Profile $profile, $inOrder)
    {
        if (0 >= $this->getUsesLimitPerUser()) {
            return;
        }

        $profileUsesCount = null;

        if (array_key_exists($profile->getLogin(), static::$runtimeCacheForUsedCouponsCount)) {
            $profileUsesCount = static::$runtimeCacheForUsedCouponsCount[$profile->getLogin()];
        } else {
            $profileUsesCount = $this->calculatePerUserUsage($profile);

            static::$runtimeCacheForUsedCouponsCount[$profile->getLogin()] = $profileUsesCount;
        }

        if ($inOrder) {
            $profileUsesCount -= 1;
        }

        if ($this->getUsesLimitPerUser() <= $profileUsesCount) {
            $this->throwCompatibilityException(
                '',
                'Sorry, the coupon use limit has been reached'
            );
        }
    }

    /**
     * @param \XLite\Model\Profile $profile
     *
     * @return int
     */
    protected function calculatePerUserUsage(\XLite\Model\Profile $profile)
    {
        return $this->getUsedCoupons()->filter(
            static function ($usedCoupon) use ($profile) {
                /** @var \CDev\Coupons\Model\UsedCoupon $usedCoupon */
                $orderProfileIdentificator = $usedCoupon->getOrder()->getProfile()
                    ? $usedCoupon->getOrder()->getProfile()->getLogin()
                    : null;

                $currentProfileIdentificator = $profile->getLogin();

                return $orderProfileIdentificator
                    && $currentProfileIdentificator
                    && $orderProfileIdentificator === $currentProfileIdentificator;
            }
        )->count();
    }

    // }}}

    // {{{ Coupons conflicts

    /**
     * Check if coupon is unique within an order
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return boolean
     */
    public function checkUnique(\XLite\Model\Order $order)
    {
        if ($order->containsCoupon($this)) {
            $this->throwCompatibilityException(
                '',
                'You have already used the coupon'
            );
        }

        return true;
    }

    /**
     * Check coupon usages
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkConflictsWithCoupons(\XLite\Model\Order $order)
    {
        if (!$order->containsCoupon($this)) {
            if ($this->getSingleUse() && count($this->getOrderUsedCoupons($order))) {
                $this->throwCompatibilityException(
                    static::ERROR_SINGLE_USE,
                    'This coupon cannot be combined with other coupons'
                );
            }

            if (!$this->getSingleUse() && $this->hasOrderSingleCoupon($order)) {
                $this->throwCompatibilityException(
                    static::ERROR_SINGLE_USE2,
                    'Sorry, this coupon cannot be combined with the coupon already applied. Revome the previously applied coupon and try again.'
                );
            }
        }
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    protected function getOrderUsedCoupons($order)
    {
        return $order->getUsedCoupons();
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return bool
     */
    protected function hasOrderSingleCoupon($order)
    {
        return $order->hasSingleUseCoupon();
    }

    // }}}

    // {{{ Total

    /**
     * Check order total
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkOrderTotal(\XLite\Model\Order $order)
    {
        $total = $this->getOrderTotal($order);
        $currency = $order->getCurrency();

        $rangeBegin = $this->getTotalRangeBegin();
        $rangeEnd = $this->getTotalRangeEnd();

        $betweenTotalCoupon = $rangeBegin > 0 && $rangeEnd > 0;
        $rangeBeginValid = $rangeBegin === 0.0 || $rangeBegin <= $total;
        $rangeEndValid = $rangeEnd === 0.0 || $rangeEnd >= $total;

        $hasProductsConditions = $this->getSpecificProducts()
            || count($this->getCategories()) > 0
            || count($this->getProductClasses()) > 0;

        if ($betweenTotalCoupon && (!$rangeBeginValid || !$rangeEndValid)) {
            $text = $hasProductsConditions
                ? $this->getBetweenSubtotalConditionalExceptionText()
                : $this->getBetweenSubtotalExceptionText();
            $this->throwCompatibilityException(
                static::ERROR_TOTAL,
                $text,
                [
                    'min' => implode('', $currency->formatParts($rangeBegin)),
                    'max' => implode('', $currency->formatParts($rangeEnd)),
                ]
            );
        } elseif (!$rangeBeginValid) {
            $text = $hasProductsConditions
                ? $this->getLeastSubtotalConditionalExceptionText()
                : $this->getLeastSubtotalExceptionText();
            $this->throwCompatibilityException(
                static::ERROR_TOTAL,
                $text,
                [
                    'min' => implode('', $currency->formatParts($rangeBegin))
                ]
            );
        } elseif (!$rangeEndValid) {
            $text = $hasProductsConditions
                ? $this->getExceedSubtotalConditionalExceptionText()
                : $this->getExceedSubtotalExceptionText();
            $this->throwCompatibilityException(
                static::ERROR_TOTAL,
                $text,
                [
                    'max' => implode('', $currency->formatParts($rangeEnd))
                ]
            );
        }
    }

    /**
     * Return text of exception
     *
     * @return void
     */
    protected function getBetweenSubtotalExceptionText()
    {
        return 'To use the coupon, your order subtotal must be between X and Y';
    }

    /**
     * Return text of exception
     *
     * @return void
     */
    protected function getLeastSubtotalExceptionText()
    {
        return 'To use the coupon, your order subtotal must be at least X';
    }

    /**
     * Return text of exception
     *
     * @return void
     */
    protected function getExceedSubtotalExceptionText()
    {
        return 'To use the coupon, your order subtotal must not exceed Y';
    }

    /**
     * Return text of exception
     *
     * @return void
     */
    protected function getBetweenSubtotalConditionalExceptionText()
    {
        return 'To use the coupon, your order subtotal must be between X and Y for specific products';
    }

    /**
     * Return text of exception
     *
     * @return void
     */
    protected function getLeastSubtotalConditionalExceptionText()
    {
        return 'To use the coupon, your order subtotal must be at least X for specific products';
    }

    /**
     * Return text of exception
     *
     * @return void
     */
    protected function getExceedSubtotalConditionalExceptionText()
    {
        return 'To use the coupon, your order subtotal must not exceed Y for specific products';
    }

    // }}}

    // {{{ Category

    /**
     * Check coupon category
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkCategory(\XLite\Model\Order $order)
    {
        if ($this->getCategories()->count()) {
            $found = false;

            foreach ($order->getItems() as $item) {
                foreach ($item->getProduct()->getCategories() as $category) {
                    if ($this->getCategories()->contains($category)) {
                        $found = true;

                        break;
                    }
                }

                if ($found) {
                    break;
                }
            }

            if (!$found) {
                $this->throwCompatibilityException(
                    '',
                    'Sorry, the coupon you entered cannot be applied to the items in your cart'
                );
            }
        }
    }

    // }}}

    // {{{ Products

    /**
     * Check coupon products
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkProducts(\XLite\Model\Order $order)
    {
        if ($this->getSpecificProducts()) {
            $applicableProductIds = $this->getApplicableProductIds();

            $found = false;

            foreach ($order->getItems() as $item) {
                if (in_array($item->getProduct()->getProductId(), $applicableProductIds)) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->throwCompatibilityException(
                    '',
                    'Sorry, the coupon you entered cannot be applied to the items in your cart'
                );
            }
        }
    }

    // }}}

    // {{{ Membership

    /**
     * Check coupon membership
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkMembership(\XLite\Model\Order $order)
    {
        if (
            $this->getMemberships()->count()
            && (!$order->getProfile()
                || !$this->getMemberships()->contains($order->getProfile()->getMembership())
            )
        ) {
            $this->throwCompatibilityException(
                '',
                'Sorry, the coupon you entered is not valid for your membership level. Contact the administrator'
            );
        }
    }

    // }}}

    // {{{ Zone

    /**
     * Check coupon zone
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkZone(\XLite\Model\Order $order)
    {
        $profile = $order->getProfile();
        $shippingAddress = $profile ? $profile->getShippingAddress() : null;

        if (!$shippingAddress) {
            $shippingAddress = \XLite\Model\Address::createDefaultShippingAddress();
        }

        if ($shippingAddress && !$this->getZones()->isEmpty()) {
            $applicableZones = \XLite\Core\Database::getRepo('XLite\Model\Zone')->findApplicableZones($shippingAddress->toArray());
            $couponZoneIds = array_map(static function ($zone) {
                return $zone->getZoneId();
            }, $this->getZones()->toArray());

            $isApplicable = false;
            foreach ($applicableZones as $zone) {
                if (in_array($zone->getZoneId(), $couponZoneIds)) {
                    $isApplicable = true;
                    break;
                }
            }

            if (!$isApplicable) {
                $this->throwCompatibilityException(
                    '',
                    'Sorry, the coupon you entered cannot be applied to this delivery address'
                );
            }
        }
    }

    // }}}

    // {{{ Product class

    /**
     * Check coupon product class
     *
     * @param \XLite\Model\Order $order Order
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function checkProductClass(\XLite\Model\Order $order)
    {
        if ($this->getProductClasses()->count()) {
            $found = false;
            foreach ($order->getItems() as $item) {
                if (
                    $item->getProduct()->getProductClass()
                    && $this->getProductClasses()->contains($item->getProduct()->getProductClass())
                ) {
                    $found = true;

                    break;
                }
            }

            if (!$found) {
                $this->throwCompatibilityException(
                    '',
                    'Sorry, the coupon you entered cannot be applied to the items in your cart'
                );
            }
        }
    }

    // }}}

    /**
     * Throws exception
     *
     * @param string $code    Message params
     * @param string $message Message text
     * @param array  $params  Message params
     *
     * @throws \CDev\Coupons\Core\CompatibilityException
     *
     * @return void
     */
    protected function throwCompatibilityException($code = '', $message = null, array $params = [])
    {
        throw new \CDev\Coupons\Core\CompatibilityException($message, $params, $this, $code);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Coupon
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Coupon
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return Coupon
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Coupon
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Coupon
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set uses
     *
     * @param integer $uses
     * @return Coupon
     */
    public function setUses($uses)
    {
        $this->uses = $uses;
        return $this;
    }

    /**
     * Get uses
     *
     * @return integer
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * Set dateRangeBegin
     *
     * @param integer $dateRangeBegin
     * @return Coupon
     */
    public function setDateRangeBegin($dateRangeBegin)
    {
        $this->dateRangeBegin = $dateRangeBegin;
        return $this;
    }

    /**
     * Get dateRangeBegin
     *
     * @return integer
     */
    public function getDateRangeBegin()
    {
        return $this->dateRangeBegin;
    }

    /**
     * Set dateRangeEnd
     *
     * @param integer $dateRangeEnd
     * @return Coupon
     */
    public function setDateRangeEnd($dateRangeEnd)
    {
        $this->dateRangeEnd = $dateRangeEnd;
        return $this;
    }

    /**
     * Get dateRangeEnd
     *
     * @return integer
     */
    public function getDateRangeEnd()
    {
        return $this->dateRangeEnd;
    }

    /**
     * Set totalRangeBegin
     *
     * @param float $totalRangeBegin
     * @return Coupon
     */
    public function setTotalRangeBegin($totalRangeBegin)
    {
        $this->totalRangeBegin = $totalRangeBegin;
        return $this;
    }

    /**
     * Get totalRangeBegin
     *
     * @return float
     */
    public function getTotalRangeBegin()
    {
        return $this->totalRangeBegin;
    }

    /**
     * Set totalRangeEnd
     *
     * @param float $totalRangeEnd
     * @return Coupon
     */
    public function setTotalRangeEnd($totalRangeEnd)
    {
        $this->totalRangeEnd = $totalRangeEnd;
        return $this;
    }

    /**
     * Get totalRangeEnd
     *
     * @return float
     */
    public function getTotalRangeEnd()
    {
        return $this->totalRangeEnd;
    }

    /**
     * Set usesLimit
     *
     * @param integer $usesLimit
     * @return Coupon
     */
    public function setUsesLimit($usesLimit)
    {
        $this->usesLimit = $usesLimit;
        return $this;
    }

    /**
     * Get usesLimit
     *
     * @return integer
     */
    public function getUsesLimit()
    {
        return $this->usesLimit;
    }

    /**
     * Set usesLimitPerUser
     *
     * @param integer $usesLimitPerUser
     * @return Coupon
     */
    public function setUsesLimitPerUser($usesLimitPerUser)
    {
        $this->usesLimitPerUser = $usesLimitPerUser;
        return $this;
    }

    /**
     * Get usesLimitPerUser
     *
     * @return integer
     */
    public function getUsesLimitPerUser()
    {
        return $this->usesLimitPerUser;
    }

    /**
     * Set singleUse
     *
     * @param boolean $singleUse
     * @return Coupon
     */
    public function setSingleUse($singleUse)
    {
        $this->singleUse = $singleUse;
        return $this;
    }

    /**
     * Get singleUse
     *
     * @return boolean
     */
    public function getSingleUse()
    {
        return $this->singleUse;
    }

    /**
     * Set specificProducts
     *
     * @param boolean $specificProducts
     * @return Coupon
     */
    public function setSpecificProducts($specificProducts)
    {
        $this->specificProducts = $specificProducts;
        return $this;
    }

    /**
     * Get specificProducts
     *
     * @return boolean
     */
    public function getSpecificProducts()
    {
        return $this->specificProducts;
    }

    /**
     * Add productClasses
     *
     * @param \XLite\Model\ProductClass $productClasses
     * @return Coupon
     */
    public function addProductClasses(\XLite\Model\ProductClass $productClasses)
    {
        $this->productClasses[] = $productClasses;
        return $this;
    }

    /**
     * Get productClasses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductClasses()
    {
        return $this->productClasses;
    }

    /**
     * Clear product classes
     */
    public function clearProductClasses()
    {
        foreach ($this->getProductClasses()->getKeys() as $key) {
            $this->getProductClasses()->remove($key);
        }
    }

    /**
     * Add memberships
     *
     * @param \XLite\Model\Membership $memberships
     * @return Coupon
     */
    public function addMemberships(\XLite\Model\Membership $memberships)
    {
        $this->memberships[] = $memberships;
        return $this;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Add coupon products
     *
     * @param \CDev\Coupons\Model\CouponProduct $couponProduct
     * @return Coupon
     */
    public function addCouponProducts(\CDev\Coupons\Model\CouponProduct $couponProduct)
    {
        $this->couponProducts[] = $couponProduct;
        return $this;
    }

    /**
     * Get product ids if coupon is specificProducts
     *
     * @return array
     */
    public function getApplicableProductIds()
    {
        $ids = [];
        if ($this->isPersistent() && $this->getSpecificProducts()) {
            $ids = \XLite\Core\Database::getRepo('CDev\Coupons\Model\CouponProduct')
                ->getCouponProductIds($this->getId());
        }

        return $ids;
    }

    /**
     * Get coupon products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCouponProducts()
    {
        return $this->couponProducts;
    }

    /**
     * Clear memberships
     */
    public function clearMemberships()
    {
        foreach ($this->getMemberships()->getKeys() as $key) {
            $this->getMemberships()->remove($key);
        }
    }

    /**
     * Add zones
     *
     * @param \XLite\Model\Zone $zone
     * @return Coupon
     */
    public function addZones(\XLite\Model\Zone $zone)
    {
        $this->zones[] = $zone;
        return $this;
    }

    /**
     * Get zones
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Clear zones
     */
    public function clearZones()
    {
        foreach ($this->getZones()->getKeys() as $key) {
            $this->getZones()->remove($key);
        }
    }

    /**
     * Add usedCoupons
     *
     * @param \CDev\Coupons\Model\UsedCoupon $usedCoupons
     * @return Coupon
     */
    public function addUsedCoupons(\CDev\Coupons\Model\UsedCoupon $usedCoupons)
    {
        $this->usedCoupons[] = $usedCoupons;
        return $this;
    }

    /**
     * Get usedCoupons
     *
     * @return \Doctrine\Common\Collections\Collection|\CDev\Coupons\Model\UsedCoupon[]
     */
    public function getUsedCoupons()
    {
        return $this->usedCoupons;
    }

    /**
     * Add categories
     *
     * @param \XLite\Model\Category $categories
     * @return Coupon
     */
    public function addCategories(\XLite\Model\Category $categories)
    {
        $this->getCategories()->add($categories);
        return $this;
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Clear categories
     */
    public function clearCategories()
    {
        foreach ($this->getCategories()->getKeys() as $key) {
            $this->getCategories()->remove($key);
        }
    }
}
