<?php

/*
 * This file is part of the Ivory Google Map package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMap\Helper\Subscriber\Place\Base;

use Ivory\GoogleMap\Helper\Collector\Place\Base\AutocompleteCoordinateCollector;
use Ivory\GoogleMap\Helper\Event\PlaceAutocompleteEvent;
use Ivory\GoogleMap\Helper\Event\PlaceAutocompleteEvents;
use Ivory\GoogleMap\Helper\Formatter\Formatter;
use Ivory\GoogleMap\Helper\Renderer\Base\CoordinateRenderer;
use Ivory\GoogleMap\Helper\Subscriber\AbstractSubscriber;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class AutocompleteCoordinateSubscriber extends AbstractSubscriber
{
    /**
     * @var AutocompleteCoordinateCollector
     */
    private $coordinateCollector;

    /**
     * @var CoordinateRenderer
     */
    private $coordinateRenderer;

    public function __construct(
        Formatter $formatter,
        AutocompleteCoordinateCollector $coordinateCollector,
        CoordinateRenderer $coordinateRenderer
    ) {
        parent::__construct($formatter);

        $this->setCoordinateCollector($coordinateCollector);
        $this->setCoordinateRenderer($coordinateRenderer);
    }

    /**
     * @return AutocompleteCoordinateCollector
     */
    public function getCoordinateCollector()
    {
        return $this->coordinateCollector;
    }

    public function setCoordinateCollector(AutocompleteCoordinateCollector $coordinateCollector)
    {
        $this->coordinateCollector = $coordinateCollector;
    }

    /**
     * @return CoordinateRenderer
     */
    public function getCoordinateRenderer()
    {
        return $this->coordinateRenderer;
    }

    public function setCoordinateRenderer(CoordinateRenderer $coordinateRenderer)
    {
        $this->coordinateRenderer = $coordinateRenderer;
    }

    public function handleAutocomplete(PlaceAutocompleteEvent $event)
    {
        $formatter = $this->getFormatter();
        $autocomplete = $event->getAutocomplete();

        foreach ($this->coordinateCollector->collect($autocomplete) as $coordinate) {
            $event->addCode($formatter->renderContainerAssignment(
                $autocomplete,
                $this->coordinateRenderer->render($coordinate),
                'base.coordinates',
                $coordinate
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [PlaceAutocompleteEvents::JAVASCRIPT_BASE_COORDINATE => 'handleAutocomplete'];
    }
}
