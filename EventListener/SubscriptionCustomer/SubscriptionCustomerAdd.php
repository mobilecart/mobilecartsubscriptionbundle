<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Constants\EntityConstants as CoreEntityConstants;

class SubscriptionCustomerAdd
{
    protected $entityService;

    protected $themeService;

    protected $event;

    protected $mailer;

    protected $router;

    protected $passwordEncoder;

    protected function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    protected function getEvent()
    {
        return $this->event;
    }

    protected function getReturnData()
    {
        return $this->getEvent()->getReturnData()
            ? $this->getEvent()->getReturnData()
            : [];
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

    public function setRouter($router)
    {
        $this->router = $router;
        return $this;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function setSecurityPasswordEncoder($passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        return $this;
    }

    public function getSecurityPasswordEncoder()
    {
        return $this->passwordEncoder;
    }

    public function setThemeService($themeService)
    {
        $this->themeService = $themeService;
        return $this;
    }

    public function getThemeService()
    {
        return $this->themeService;
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

    public function onSubscriptionCustomerAdd(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();
        $returnData['success'] = 0;

        $request = $event->getRequest();
        $entity = $event->getEntity();
        $formData = $event->getFormData();

        $parentCustomer = $returnData['user'];

        $parentSubscription = $this->getEntityService()->findOneBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
            'customer' => $parentCustomer->getId(),
        ]);

        $subscription = $parentSubscription
            ? $parentSubscription->getSubscription()
            : null;

        if (
            $subscription
            && $subscription->getHasGroups()
            && count($parentSubscription->getChildren()) < $subscription->getMaxGroupSize()
        ) {

            $customer = $this->getEntityService()->findOneBy(CoreEntityConstants::CUSTOMER, [
                'email' => $entity->getEmail(),
            ]);

            if ($customer) {

                $subscriptionCustomer = $this->getEntityService()->findOneBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
                    'customer' => $customer->getId(),
                ]);

                if (!$subscriptionCustomer) {

                    $subscriptionCustomer = $this->getEntityService()->getInstance(EntityConstants::SUBSCRIPTION_CUSTOMER);
                    $subscriptionCustomer->setCustomerName($customer->getName())
                        ->setCustomer($customer)
                        ->setParentSubscriptionCustomer($parentSubscription)
                        ->setSubscription($parentSubscription->getSubscription())
                        ->setIsActive(1)
                        ;

                    $this->getEntityService()->persist($subscriptionCustomer);

                    $route = '_home';
                    $params = [];
                    $url = $this->getRouter()->generate($route, $params);

                    $tpl = 'Email:subscription_customer_add.html.twig';
                    $tplData = [
                        'inviter' => $parentSubscription,
                        'invitee' => $subscriptionCustomer,
                        'new_password' => '', // blank since customer already exists
                        'url' => $url,
                    ];

                    $body = $this->getThemeService()->renderView('subscription_email', $tpl, $tplData);

                    try {

                        $message = \Swift_Message::newInstance()
                            ->setSubject('Subscription Account Registration')
                            //->setFrom('~')
                            ->setTo($customer->getEmail())
                            ->setBody($body, 'text/html');

                        $this->getMailer()->send($message);

                    } catch(\Exception $e) {
                        // todo : handle error
                    }
                }

            } else {

                // further populate customer object
                //  password, enabled, etc

                $plaintext = substr(md5(microtime()), 0, 8);
                $encoder = $this->getSecurityPasswordEncoder();
                $encoded = $encoder->encodePassword($entity, $plaintext);
                $entity->setHash($encoded);

                $this->getEntityService()->persist($entity);

                $subscriptionCustomer = $this->getEntityService()->getInstance(EntityConstants::SUBSCRIPTION_CUSTOMER);
                $subscriptionCustomer->setCustomerName($entity->getName())
                    ->setCustomer($entity)
                    ->setParentSubscriptionCustomer($parentSubscription)
                    ->setSubscription($parentSubscription->getSubscription())
                    ->setIsActive(1)
                ;

                $this->getEntityService()->persist($subscriptionCustomer);

                $route = '_home';
                $params = [];
                $url = $this->getRouter()->generate($route, $params);

                $tpl = 'Email:subscription_customer_add.html.twig';
                $tplData = [
                    'inviter' => $parentSubscription,
                    'invitee' => $subscriptionCustomer,
                    'new_password' => $plaintext,
                    'url' => $url,
                ];

                $body = $this->getThemeService()->renderView('subscription_email', $tpl, $tplData);

                try {

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Subscription Account Registration')
                        //->setFrom('~')
                        ->setTo($entity->getEmail())
                        ->setBody($body, 'text/html');

                    $this->getMailer()->send($message);

                } catch(\Exception $e) {
                    // todo : handle error
                }
            }

            $returnData['success'] = 1;

        } else {

            // todo: add warning/error messages to the event

        }

        $event->setReturnData($returnData);
    }
}
