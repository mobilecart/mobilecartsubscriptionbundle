<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use MobileCart\CoreBundle\Event\CoreEvent;
use MobileCart\SubscriptionBundle\Constants\EntityConstants;
use MobileCart\CoreBundle\Constants\EntityConstants as CoreEntityConstants;

/**
 * Class SubscriptionCustomerAdd
 * @package MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer
 */
class SubscriptionCustomerAdd
{
    /**
     * @var \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    protected $entityService;

    /**
     * @var \MobileCart\CoreBundle\Service\ThemeService
     */
    protected $themeService;

    protected $mailer;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    protected $passwordEncoder;

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @return $this
     */
    public function setRouter(\Symfony\Component\Routing\RouterInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
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

    /**
     * @param \MobileCart\CoreBundle\Service\ThemeService $themeService
     * @return $this
     */
    public function setThemeService(\MobileCart\CoreBundle\Service\ThemeService $themeService)
    {
        $this->themeService = $themeService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\ThemeService
     */
    public function getThemeService()
    {
        return $this->themeService;
    }

    /**
     * @param \MobileCart\CoreBundle\Service\AbstractEntityService $entityService
     * @return $this
     */
    public function setEntityService(\MobileCart\CoreBundle\Service\AbstractEntityService $entityService)
    {
        $this->entityService = $entityService;
        return $this;
    }

    /**
     * @return \MobileCart\CoreBundle\Service\AbstractEntityService
     */
    public function getEntityService()
    {
        return $this->entityService;
    }

    public function onSubscriptionCustomerAdd(CoreEvent $event)
    {
        $success = false;
        $entity = $event->getEntity();
        $parentCustomer = $event->getReturnData('user');

        $parentSubscription = $this->getEntityService()->findOneBy(EntityConstants::SUBSCRIPTION_CUSTOMER, [
            'customer' => $parentCustomer->getId(),
        ]);

        $subscription = $parentSubscription
            ? $parentSubscription->getSubscription()
            : null;

        if ($subscription
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
                        ->setCreatedAt(new \DateTime('now'))
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
                    ->setCreatedAt(new \DateTime('now'))
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

            $success = true;

        }

        $event->setReturnData('success', $success);
    }
}
