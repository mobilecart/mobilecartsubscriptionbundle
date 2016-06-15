<?php

namespace MobileCart\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubscriptionCustomer
 *
 * @ORM\Table(name="subscription_customer")
 * @ORM\Entity(repositoryClass="MobileCart\SubscriptionBundle\Repository\SubscriptionCustomerRepository")
 */
class SubscriptionCustomer
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
     * @ORM\ManyToOne(targetEntity="MobileCart\SubscriptionBundle\Entity\Subscription", inversedBy="customers")
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
     * @ORM\Column(name="customer_name", type="string", length=255)
     */
    private $customer_name;

    /**
     * @var \MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer
     *
     * @ORM\ManyToOne(targetEntity="MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer", inversedBy="child_subscription_customers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_subscription_customer_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $parent_subscription_customer;

    /**
     * @ORM\OneToMany(targetEntity="\MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer", mappedBy="parent_subscription_customer")
     */
    private $child_subscription_customers;

    /**
     * @var \MobileCart\CoreBundle\Entity\CustomerToken
     *
     * @ORM\ManyToOne(targetEntity="MobileCart\CoreBundle\Entity\CustomerToken")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="customer_token_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $customer_token;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_in_free_trial", type="boolean", nullable=true)
     */
    private $is_in_free_trial;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $is_active;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_overdue", type="boolean", nullable=true)
     */
    private $is_overdue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="payment_reminder_at", type="datetime", nullable=true)
     */
    private $payment_reminder_at;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_logged_in", type="boolean", nullable=true)
     */
    private $is_logged_in;

    public function __construct()
    {
        $this->child_subscription_customers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->getCustomer()
            ? $this->getCustomer()->getEmail()
            : '';
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

    public function getObjectTypeName()
    {
        return \MobileCart\SubscriptionBundle\Constants\EntityConstants::SUBSCRIPTION_CUSTOMER;
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
            'customer_name' => $this->getCustomerName(),
            //'parent_subscription_customer_id' => $this->
            //'customer_id' => $this->
            //'subscription_id' => $this->
            //'customer_token_id' => $this->
            'is_in_free_trial' => $this->getIsInFreeTrial(),
            'is_active' => $this->getIsActive(),
            'is_overdue' => $this->getIsOverdue(),
            'payment_reminder_at' => $this->getPaymentReminderAt(),
            'is_logged_in' => $this->getIsLoggedIn(),
        ];
    }

    /**
     * @param $name
     * @return $this
     */
    public function setCustomerName($name)
    {
        $this->customer_name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customer_name;
    }

    /**
     * Set subscription
     *
     * @param Subscription $subscription
     * @return SubscriptionCustomer
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Get subscription
     *
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
     * @return SubscriptionCustomer
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

    public function setParentSubscriptionCustomer($parent)
    {
        $this->parent_subscription_customer = $parent;
        return $this;
    }

    public function getParentSubscriptionCustomer()
    {
        return $this->parent_subscription_customer;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildSubscriptionCustomers()
    {
        return $this->child_subscription_customers;
    }

    /**
     * @param SubscriptionCustomer $child
     * @return SubscriptionCustomer
     */
    public function addChildCategory(SubscriptionCustomer $child)
    {
        $this->child_subscription_customers[] = $child;
        return $this;
    }

    /**
     * @return RecursiveIteratorIterator
     */
    public function getChildren()
    {
        $collection = new \Doctrine\Common\Collections\ArrayCollection(array($this));
        $iterator = new RecursiveSubscriptionCustomerIterator($collection);
        return new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
    }

    /**
     * Set customerToken
     *
     * @param CustomerToken $customerToken
     * @return SubscriptionCustomer
     */
    public function setCustomerToken($customerToken)
    {
        $this->customer_token = $customerToken;
        return $this;
    }

    /**
     * Get customerToken
     *
     * @return CustomerToken
     */
    public function getCustomerToken()
    {
        return $this->customer_token;
    }

    /**
     * Set isInFreeTrial
     *
     * @param boolean $isInFreeTrial
     * @return SubscriptionCustomer
     */
    public function setIsInFreeTrial($isInFreeTrial)
    {
        $this->is_in_free_trial = $isInFreeTrial;
        return $this;
    }

    /**
     * Get isInFreeTrial
     *
     * @return boolean 
     */
    public function getIsInFreeTrial()
    {
        return $this->is_in_free_trial;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return SubscriptionCustomer
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set isOverdue
     *
     * @param boolean $isOverdue
     * @return SubscriptionCustomer
     */
    public function setIsOverdue($isOverdue)
    {
        $this->is_overdue = $isOverdue;
        return $this;
    }

    /**
     * Get isOverdue
     *
     * @return boolean 
     */
    public function getIsOverdue()
    {
        return $this->is_overdue;
    }

    /**
     * Set paymentReminderAt
     *
     * @param \DateTime $paymentReminderAt
     * @return SubscriptionCustomer
     */
    public function setPaymentReminderAt($paymentReminderAt)
    {
        $this->payment_reminder_at = $paymentReminderAt;
        return $this;
    }

    /**
     * Get paymentReminderAt
     *
     * @return \DateTime 
     */
    public function getPaymentReminderAt()
    {
        return $this->payment_reminder_at;
    }

    /**
     * Set isLoggedIn
     *
     * @param boolean $isLoggedIn
     * @return SubscriptionCustomer
     */
    public function setIsLoggedIn($isLoggedIn)
    {
        $this->is_logged_in = $isLoggedIn;
        return $this;
    }

    /**
     * Get isLoggedIn
     *
     * @return boolean 
     */
    public function getIsLoggedIn()
    {
        return $this->is_logged_in;
    }
}
