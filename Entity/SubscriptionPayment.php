<?php

namespace MobileCart\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MobileCart\CoreBundle\Entity\Customer;
use MobileCart\CoreBundle\Entity\CartEntityInterface;
use MobileCart\CoreBundle\Entity\AbstractCartEntity;

/**
 * SubscriptionPayment
 *
 * @ORM\Table(name="subscription_payment")
 * @ORM\Entity(repositoryClass="MobileCart\SubscriptionBundle\Repository\SubscriptionPaymentRepository")
 */
class SubscriptionPayment
    extends AbstractCartEntity
    implements CartEntityInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @var \MobileCart\SubscriptionBundle\Entity\Subscription
     *
     * @ORM\ManyToOne(targetEntity="MobileCart\SubscriptionBundle\Entity\Subscription")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="subscription_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $subscription;

    /**
     * @var \MobileCart\CoreBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="MobileCart\CoreBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method", type="string", length=64, nullable=true)
     */
    private $payment_method;

    /**
     * @var string
     *
     * @ORM\Column(name="base_currency", type="string", length=8, nullable=true)
     */
    private $base_currency;

    /**
     * @var string
     *
     * @ORM\Column(name="base_amount", type="decimal", precision=12, scale=4, nullable=true)
     */
    private $base_amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=8, nullable=true)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=12, scale=4)
     */
    private $amount;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_approved", type="boolean", nullable=true)
     */
    private $is_approved;

    /**
     * @var int
     *
     * @ORM\Column(name="retries", type="integer", nullable=true)
     */
    private $retries;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectTypeKey()
    {
        return \MobileCart\SubscriptionBundle\Constants\EntityConstants::SUBSCRIPTION_PAYMENT;
    }

    /**
     * @return array
     */
    public function getBaseData()
    {
        return [
            'id' => $this->getId(),
            'created_at' => $this->getCreatedAt(),
            'payment_method' => $this->getPaymentMethod(),
            'base_currency' => $this->getBaseCurrency(),
            'base_amount' => $this->getBaseAmount(),
            'currency' => $this->getCurrency(),
            'amount' => $this->getAmount(),
            'is_approved' => $this->getIsApproved(),
        ];
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param Subscription $subscription
     * @return $this
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Set customer
     *
     * @param Customer $customer
     * @return SubscriptionPayment
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set paymentMethod
     *
     * @param string $paymentMethod
     * @return SubscriptionPayment
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->payment_method = $paymentMethod;
        return $this;
    }

    /**
     * Get paymentMethod
     *
     * @return string 
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * Set baseCurrency
     *
     * @param string $baseCurrency
     * @return SubscriptionPayment
     */
    public function setBaseCurrency($baseCurrency)
    {
        $this->base_currency = $baseCurrency;
        return $this;
    }

    /**
     * Get baseCurrency
     *
     * @return string 
     */
    public function getBaseCurrency()
    {
        return $this->base_currency;
    }

    /**
     * Set baseAmount
     *
     * @param string $baseAmount
     * @return SubscriptionPayment
     */
    public function setBaseAmount($baseAmount)
    {
        $this->base_amount = $baseAmount;
        return $this;
    }

    /**
     * Get baseAmount
     *
     * @return string 
     */
    public function getBaseAmount()
    {
        return $this->base_amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return SubscriptionPayment
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return SubscriptionPayment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set isApproved
     *
     * @param boolean $isApproved
     * @return SubscriptionPayment
     */
    public function setIsApproved($isApproved)
    {
        $this->is_approved = $isApproved;
        return $this;
    }

    /**
     * Get isApproved
     *
     * @return boolean 
     */
    public function getIsApproved()
    {
        return $this->is_approved;
    }

    /**
     * @param $retries
     * @return $this
     */
    public function setRetries($retries)
    {
        $this->retries = $retries;
        return $this;
    }

    /**
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }
}
