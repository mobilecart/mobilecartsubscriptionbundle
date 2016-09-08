<?php

namespace MobileCart\SubscriptionBundle\Service;

use MobileCart\CoreBundle\Constants\EntityConstants;
use MobileCart\SubscriptionBundle\Constants\EntityConstants as SubEntityConstants;
use MobileCart\SubscriptionBundle\Entity\SubscriptionCustomer;

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

    public function cancelRecurringSubscription(SubscriptionCustomer $subscriptionCustomer)
    {
        // cancel all future payments

        $methodCode = $subscriptionCustomer->getSubscription()->getPaymentMethodCode();

        $paymentMethodService = $this->getPaymentService()
            ->findPaymentMethodServiceByCode($methodCode);

        if (!$paymentMethodService) {
            throw new \Exception("Cannot find Payment Method Service by code: {$methodCode}");
        }

        $success = false;

        try {

            $paymentMethodService->setSubscriptionCustomer($subscriptionCustomer)
                ->cancelRecurring();

            if ($paymentMethodService->getIsCanceledRecurring()) {
                $subscriptionCustomer->setIsCanceled(true);
                $this->getEntityService()->persist($subscriptionCustomer);
                $success = true;
            }

        } catch(\Exception $e) {

        }

        return $success;
    }

    public function sendReminderEmails($date='now')
    {
        // todo
        // send each customer a payment reminder
    }

    public function processScheduledPayments($date='now')
    {
        // todo
        // attempt payment on all payments scheduled for today, with is_approved=0 and retries=0 or null
        // if payment fails, mark as overdue
        // else set is_approved=1 , and send an email receipt , schedule the next payment
    }

    public function updateOverdueSubscriptions($date='now')
    {
        // todo
        // get overdue customers and retry payment and/or delete subscription
    }
}
