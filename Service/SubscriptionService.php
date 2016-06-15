<?php

namespace MobileCart\SubscriptionBundle\Service;

use MobileCart\CoreBundle\Constants\EntityConstants;
use MobileCart\SubscriptionBundle\Constants\EntityConstants as SubEntityConstants;

class SubscriptionService
{

    protected $isEnabled = 1;

    protected $mailer;

    protected $orderService;

    protected $entityService;

    protected $paymentService;

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    public function getMailer()
    {
        return $this->mailer;
    }

    public function setOrderService($orderService)
    {
        $this->orderService = $orderService;
        return $this;
    }

    public function getOrderService()
    {
        return $this->orderService;
    }

    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    public function getEntityService()
    {
        return $this->entityService;
    }

    public function setPaymentService($paymentService)
    {
        $this->paymentService = $paymentService;
        return $this;
    }

    public function getPaymentService()
    {
        return $this->paymentService;
    }

    public function createFreeTrial($customerId, $subscriptionId, $startDate='now')
    {
        $customer = $this->getEntityService()->find(EntityConstants::CUSTOMER, $customerId);
        $subscription = $this->getEntityService()->find(SubEntityConstants::SUBSCRIPTION, $subscriptionId);

    }

    public function updateFreeTrials($date='now')
    {

    }

    public function updateOverdueSubscriptions()
    {

    }

    public function sendPaymentReminders()
    {

    }

    public function retryScheduledPayments()
    {

    }

    public function fingerprintExists($fingerprint, $subscriptionId=0)
    {
        //check if a card has already been used, possibly for a free trial
    }

    public function captureScheduledPayments($date='now', $subscriptionId=0)
    {
        //specify a date . default is today
        //specify a subscription or charge all subscriptions
        // only capture scheduled payments with a status of approved=0
        //retry scheduled payments
        //schedule the next payment
    }

    public function buildCart($customer, $subscription)
    {

    }

    public function submitCart($cart)
    {

    }

    public function cancelCustomerSubscription($customerId, $subscriptionId)
    {
        //cancel all future payments
        //mark subscription as in_active
    }
}
