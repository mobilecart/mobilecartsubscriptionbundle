<?php

namespace MobileCart\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MobileCart\CoreBundle\Entity\Customer;

/**
 * SubscriptionPayment
 *
 * @ORM\Table(name="subscription_payment")
 * @ORM\Entity(repositoryClass="MobileCart\SubscriptionBundle\Repository\SubscriptionPaymentRepository")
 */
class SubscriptionPayment
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
     * @var \DateTime
     *
     * @ORM\Column(name="payment_date", type="datetime")
     */
    private $payment_date;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     * @return SubscriptionPayment
     */
    public function setPaymentDate($paymentDate)
    {
        $this->payment_date = $paymentDate;
        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime 
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
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
}
