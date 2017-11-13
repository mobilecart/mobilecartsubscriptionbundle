<?php

namespace MobileCart\SubscriptionBundle\EventListener\Subscription;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use MobileCart\CoreBundle\Event\CoreEvent;

/**
 * Class SubscriptionCreateReturn
 * @package MobileCart\SubscriptionBundle\EventListener\Subscription
 */
class SubscriptionCreateReturn
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

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

    public function onSubscriptionCreateReturn(CoreEvent $event)
    {
        $entity = $event->getEntity();
        $request = $event->getRequest();
        $format = $request->get(\MobileCart\CoreBundle\Constants\ApiConstants::PARAM_RESPONSE_TYPE, '');

        $params = ['id' => $entity->getId()];
        $route = 'cart_admin_subscription_edit';
        $url = $this->getRouter()->generate($route, $params);

        if ($event->getRequest()->getSession() && $event->getMessages()) {
            foreach($event->getMessages() as $code => $messages) {
                if (!$messages) {
                    continue;
                }
                foreach($messages as $message) {
                    $event->getRequest()->getSession()->getFlashBag()->add($code, $message);
                }
            }
        }

        switch($format) {
            case 'json':
                $event->setResponse(new JsonResponse([
                    'success' => true,
                    'entity' => $entity->getData(),
                    'redirect_url' => $url,
                ]));
                break;
            default:
                $event->setResponse(new RedirectResponse($url));
                break;
        }
    }

}
