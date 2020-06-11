<?php

namespace Amplifier;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AmplifierService implements EventSubscriberInterface
{
    protected $events = [];

    public function record($eventName, $event, $stamp = null)
    {
        $payload = $this->getPayload($event);

        $e = new AmplifierEvent($eventName, $payload);
        $this->events[] = $e;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'onTerminate',
        ];
    }

    public function getPayload($event)
    {
        $data = [];
        if (is_object($event)) {
            foreach ($event as $key=>$value) {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    public function onTerminate(PostResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $filename = '/tmp/amplifier.txt';
        foreach ($this->events as $event) {
            file_put_contents($filename, $event->getName() . '@' . $event->getStamp() . json_encode($event->getPayload(), JSON_PRETTY_PRINT). PHP_EOL, FILE_APPEND);
        }
    }
}
