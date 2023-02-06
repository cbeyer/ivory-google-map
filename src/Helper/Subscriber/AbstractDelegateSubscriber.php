<?php

/*
 * This file is part of the Ivory Google Map package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMap\Helper\Subscriber;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractDelegateSubscriber extends AbstractSubscriber implements DelegateSubscriberInterface
{
    /**
     * @param string $eventName
     */
    public function handle(Event $event, $eventName, EventDispatcherInterface $eventDispatcher)
    {
        $delegates = static::getDelegatedSubscribedEvents();

        if (isset($delegates[$eventName])) {
            foreach ($delegates[$eventName] as $delegate) {
                $eventDispatcher->dispatch($event, $delegate);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        $events = [];

        foreach (array_keys(static::getDelegatedSubscribedEvents()) as $event) {
            $events[$event] = 'handle';
        }

        return $events;
    }
}
