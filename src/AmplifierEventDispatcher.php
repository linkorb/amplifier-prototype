<?php

namespace Amplifier;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Amplifier\AmplifierService;

class AmplifierEventDispatcher extends AbstractEventDispatcherProxy
{
    protected $dispatcher;
    protected $service;

    protected $events = [];

    public function __construct(
        EventDispatcherInterface $dispatcher,
        AmplifierService $service
    )
    {
        $this->service = $service;
        parent::__construct($dispatcher);
    }

    public function dispatch($eventName, Event $event = null)
    {
        $this->service->record($eventName, $event);

        return parent::dispatch($eventName, $event);
    }
}
