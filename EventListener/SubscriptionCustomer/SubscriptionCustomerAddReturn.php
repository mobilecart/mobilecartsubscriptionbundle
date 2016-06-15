<?php

namespace MobileCart\SubscriptionBundle\EventListener\SubscriptionCustomer;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;

class SubscriptionCustomerAddReturn
{
    protected $request;

    protected $varSet;

    protected $entityService;

    protected $imageService;

    protected $themeService;

    protected $event;

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

    public function setImageService($imageService)
    {
        $this->imageService = $imageService;
        return $this;
    }

    public function getImageService()
    {
        return $this->imageService;
    }

    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setVarSet($varSet)
    {
        $this->varSet = $varSet;
        return $this;
    }

    public function getVarSet()
    {
        return $this->varSet;
    }

    public function onSubscriptionCustomerAddReturn(Event $event)
    {
        $this->setEvent($event);
        $returnData = $this->getReturnData();
        $request = $event->getRequest();
        $format = $request->get('format', '');

        $entity = $event->getEntity();
        $varSet = $this->getVarSet();
        $objectType = $event->getObjectType();

        switch($format) {
            case 'json':
                $response = new JsonResponse([
                    'success' => 0,
                ]);

                $event->setResponse($response);
                break;
            default:

                $typeSections = [];

                $returnData['template_sections'] = $typeSections;

                $form = $returnData['form'];
                $returnData['form'] = $form->createView();
                $returnData['entity'] = $entity;

                $response = $this->getThemeService()
                    ->render('subscription_frontend', 'SubscriptionCustomer:add.html.twig', $returnData);

                $event->setResponse($response);

                break;
        }



        $event->setReturnData($returnData);
    }
}
