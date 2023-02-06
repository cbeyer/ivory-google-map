<?php

/*
 * This file is part of the Ivory Google Map package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMap\Service\DistanceMatrix;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Ivory\GoogleMap\Service\AbstractSerializableService;
use Ivory\GoogleMap\Service\Base\Distance;
use Ivory\GoogleMap\Service\Base\Duration;
use Ivory\GoogleMap\Service\DistanceMatrix\Request\DistanceMatrixRequestInterface;
use Ivory\GoogleMap\Service\DistanceMatrix\Response\DistanceMatrixElement;
use Ivory\GoogleMap\Service\DistanceMatrix\Response\DistanceMatrixElementStatus;
use Ivory\GoogleMap\Service\DistanceMatrix\Response\DistanceMatrixResponse;
use Ivory\GoogleMap\Service\DistanceMatrix\Response\DistanceMatrixRow;
use Ivory\Serializer\Context\Context;
use Ivory\Serializer\Naming\SnakeCaseNamingStrategy;
use Ivory\Serializer\SerializerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DistanceMatrixService extends AbstractSerializableService
{
    public function __construct(
        HttpClient $client,
        MessageFactory $messageFactory,
        SerializerInterface $serializer = null
    ) {
        parent::__construct(
            'https://maps.googleapis.com/maps/api/distancematrix',
            $client,
            $messageFactory,
            $serializer
        );
    }

    /**
     * @return DistanceMatrixResponse
     */
    public function process(DistanceMatrixRequestInterface $request)
    {
        $httpRequest = $this->createRequest($request);
        $httpResponse = $this->getClient()->sendRequest($httpRequest);
        
        $array = json_decode($httpResponse->getBody(), true);

        $response = new DistanceMatrixResponse();
        $response->setOrigins($array['origin_addresses']);
        $response->setDestinations($array['destination_addresses']);

        foreach ($array['rows'] as $row) {
            $distanceMatrixRow  = new DistanceMatrixRow();
            foreach ($row['elements'] as $element) {
                $distanceMatrixElement  = new DistanceMatrixElement();
                $distanceMatrixElement->setStatus($element['status']);
                $distance = new Distance($element['distance']['value'], $element['distance']['text']);
                $distanceMatrixElement->setDistance($distance);
                $duration = new Duration($element['duration']['value'], $element['duration']['text']);
                $distanceMatrixElement->setDuration($duration);
                $distanceMatrixRow->addElement($distanceMatrixElement);
            }
            $response->addRow($distanceMatrixRow);
        }

        $response->setRequest($request);

        return $response;
    }
}
