<?php

namespace MobileCart\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MobileCart\CoreBundle\Entity\CartEntityInterface;

/**
 * Subscription
 *
 * @ORM\Table(name="subscription")
 * @ORM\Entity(repositoryClass="MobileCart\SubscriptionBundle\Repository\SubscriptionRepository")
 */
class Subscription
    implements CartEntityInterface
{

    const HANDLER_SERVER_AUTO = 1;
    const HANDLER_SERVER_MANUAL = 2;
    const HANDLER_SERVICE_EXTERNAL = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="payment_amount", type="decimal", precision=12, scale=4)
     */
    private $payment_amount;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_method_code", type="string", length=32)
     */
    private $payment_method_code;

    /**
     * Class Constant
     *  Handled by: Server, 3rd Party, Manual
     *
     * @var int
     *
     * @ORM\Column(name="payment_handler", type="integer")
     */
    private $payment_handler;

    /**
     * @var int
     *
     * @ORM\Column(name="payment_interval_days", type="integer", nullable=true)
     */
    private $payment_interval_days;

    /**
     * @var string
     *
     * @ORM\Column(name="external_plan_id", type="string", length=255, nullable=true)
     */
    private $external_plan_id;

    /**
     * @var int
     *
     * @ORM\Column(name="free_trial_days", type="integer", nullable=true)
     */
    private $free_trial_days;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_shipping", type="boolean", nullable=true)
     */
    private $has_shipping;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_method_code", type="string", length=255, nullable=true)
     */
    private $shipping_method_code;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_free_shipping", type="boolean", nullable=true)
     */
    private $is_free_shipping;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_groups", type="boolean", nullable=true)
     */
    private $has_groups;

    /**
     * @var int
     *
     * @ORM\Column(name="max_group_size", type="integer", nullable=true)
     */
    private $max_group_size;

    /**
     * @var \MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer
     *
     * @ORM\OneToMany(targetEntity="MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer", mappedBy="subscription")
     */
    private $customers;

    public function __construct()
    {
        $this->customers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
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

    public function getObjectTypeKey()
    {
        return \MobileCart\SubscriptionBundle\Constants\EntityConstants::SUBSCRIPTION;
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
     * @param $key
     * @param $value
     * @return $this
     */
    public function set($key, $value)
    {
        $vars = get_object_vars($this);
        if (array_key_exists($key, $vars)) {
            $this->$key = $value;
        }

        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function fromArray($data)
    {
        if (!$data) {
            return $this;
        }

        foreach($data as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * Lazy-loading getter
     *  ideal for usage in the View layer
     *
     * @param $key
     * @return mixed|null
     */
    public function get($key)
    {
        if (isset($this->$key)) {
            return $this->$key;
        }

        $data = $this->getBaseData();
        if (isset($data[$key])) {
            return $data[$key];
        }

        $data = $this->getData();
        if (isset($data[$key])) {

            if (is_array($data[$key])) {
                return implode(',', $data[$key]);
            }

            return $data[$key];
        }

        return '';
    }

    /**
     * Getter , after fully loading
     *  use only if necessary, and avoid calling multiple times
     *
     * @param string $key
     * @return array|null
     */
    public function getData($key = '')
    {
        $data = $this->getBaseData();

        if (strlen($key) > 0) {

            return isset($data[$key])
                ? $data[$key]
                : null;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getLuceneVarValuesData()
    {
        // Note:
        // be careful with adding foreign relationships here
        // since it will add 1 query every time an item is loaded

        return $this->getBaseData();
    }

    /**
     * @return array
     */
    public function getBaseData()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'payment_method_code' => $this->getPaymentMethodCode(),
            'payment_amount' => $this->getPaymentAmount(),
            'payment_interval_days' => $this->getPaymentIntervalDays(),
            'payment_handler' => $this->getPaymentHandler(),
            'external_plan_id' => $this->getExternalPlanId(),
            'free_trial_days' => $this->getFreeTrialDays(),
            'has_groups' => $this->getHasGroups(),
            'max_group_size' => $this->getMaxGroupSize(),
            'has_shipping' => $this->getHasShipping(),
            'shipping_method_code' => $this->getShippingMethodCode(),
            'is_free_shipping' => $this->getIsFreeShipping(),
        ];
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set payment_amount
     *
     * @param float $payment_amount
     * @return Subscription
     */
    public function setPaymentAmount($payment_amount)
    {
        $this->payment_amount = $payment_amount;
        return $this;
    }

    /**
     * Get payment_amount
     *
     * @return float 
     */
    public function getPaymentAmount()
    {
        return $this->payment_amount;
    }

    /**
     * Set payment_method_code
     *
     * @param string $payment_method_code
     * @return Subscription
     */
    public function setPaymentMethodCode($payment_method_code)
    {
        $this->payment_method_code = $payment_method_code;
        return $this;
    }

    /**
     * Get payment_method_code
     *
     * @return string 
     */
    public function getPaymentMethodCode()
    {
        return $this->payment_method_code;
    }

    /**
     * Set payment_handler
     *
     * @param integer $payment_handler
     * @return Subscription
     */
    public function setPaymentHandler($payment_handler)
    {
        $this->payment_handler = $payment_handler;
        return $this;
    }

    /**
     * Get payment_handler
     *
     * @return integer 
     */
    public function getPaymentHandler()
    {
        return $this->payment_handler;
    }

    /**
     * Set payment_interval_days
     *
     * @param integer $payment_interval_days
     * @return Subscription
     */
    public function setPaymentIntervalDays($payment_interval_days)
    {
        $this->payment_interval_days = $payment_interval_days;
        return $this;
    }

    /**
     * Get payment_interval_days
     *
     * @return integer 
     */
    public function getPaymentIntervalDays()
    {
        return $this->payment_interval_days;
    }

    /**
     * Set external_plan_id
     *
     * @param string $external_plan_id
     * @return Subscription
     */
    public function setExternalPlanId($external_plan_id)
    {
        $this->external_plan_id = $external_plan_id;
        return $this;
    }

    /**
     * Get external_plan_id
     *
     * @return string 
     */
    public function getExternalPlanId()
    {
        return $this->external_plan_id;
    }

    /**
     * Set free_trial_days
     *
     * @param integer $free_trial_days
     * @return Subscription
     */
    public function setFreeTrialDays($free_trial_days)
    {
        $this->free_trial_days = $free_trial_days;
        return $this;
    }

    /**
     * Get free_trial_days
     *
     * @return integer 
     */
    public function getFreeTrialDays()
    {
        return $this->free_trial_days;
    }

    /**
     * Set has_shipping
     *
     * @param boolean $has_shipping
     * @return Subscription
     */
    public function setHasShipping($has_shipping)
    {
        $this->has_shipping = $has_shipping;
        return $this;
    }

    /**
     * Get has_shipping
     *
     * @return boolean 
     */
    public function getHasShipping()
    {
        return $this->has_shipping;
    }

    /**
     * Set shipping_method_code
     *
     * @param string $shipping_method_code
     * @return Subscription
     */
    public function setShippingMethodCode($shipping_method_code)
    {
        $this->shipping_method_code = $shipping_method_code;
        return $this;
    }

    /**
     * Get shipping_method_code
     *
     * @return string 
     */
    public function getShippingMethodCode()
    {
        return $this->shipping_method_code;
    }

    /**
     * Set is_free_shipping
     *
     * @param boolean $is_free_shipping
     * @return Subscription
     */
    public function setIsFreeShipping($is_free_shipping)
    {
        $this->is_free_shipping = $is_free_shipping;
        return $this;
    }

    /**
     * Get is_free_shipping
     *
     * @return boolean 
     */
    public function getIsFreeShipping()
    {
        return $this->is_free_shipping;
    }

    /**
     * Set has_groups
     *
     * @param boolean $has_groups
     * @return Subscription
     */
    public function setHasGroups($has_groups)
    {
        $this->has_groups = $has_groups;
        return $this;
    }

    /**
     * Get has_groups
     *
     * @return boolean 
     */
    public function getHasGroups()
    {
        return $this->has_groups;
    }

    /**
     * Set max_group_size
     *
     * @param integer $max_group_size
     * @return Subscription
     */
    public function setMaxGroupSize($max_group_size)
    {
        $this->max_group_size = $max_group_size;
        return $this;
    }

    /**
     * Get max_group_size
     *
     * @return integer 
     */
    public function getMaxGroupSize()
    {
        return $this->max_group_size;
    }

    /**
     * @param SubscriptionCustomer $customer
     * @return $this
     */
    public function addCustomer(SubscriptionCustomer $customer)
    {
        $this->customers[] = $customer;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|SubscriptionCustomer
     */
    public function getCustomers()
    {
        return $this->customers;
    }
}
