<?php

/*
 * This file is part of the Ivory Google Map package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\GoogleMap\Service\Geocoder;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Service\AbstractSerializableService;
use Ivory\GoogleMap\Service\Base\Geometry;
use Ivory\GoogleMap\Service\Geocoder\Request\GeocoderRequestInterface;
use Ivory\GoogleMap\Service\Geocoder\Response\GeocoderResponse;
use Ivory\GoogleMap\Service\Geocoder\Response\GeocoderResult;
use Ivory\Serializer\Context\Context;
use Ivory\Serializer\Naming\SnakeCaseNamingStrategy;
use Ivory\Serializer\SerializerInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class GeocoderService extends AbstractSerializableService
{
    public function __construct(
        HttpClient $client,
        MessageFactory $messageFactory,
        SerializerInterface $serializer = null
    ) {
        parent::__construct(
            'https://maps.googleapis.com/maps/api/geocode',
            $client,
            $messageFactory,
            $serializer
        );
    }

    /**
     * @return GeocoderResponse
     */
    public function geocode(GeocoderRequestInterface $request)
    {
        $httpRequest = $this->createRequest($request);
        $httpResponse = $this->getClient()->sendRequest($httpRequest);

        $array = json_decode($httpResponse->getBody(), true);

        $response = new GeocoderResponse();
        $response->setStatus($array['status']);

        foreach ($array['results'] as $result) {
            $geocoderResult  = new GeocoderResult();
            $geometry = new Geometry();
            $location = new Coordinate($result['geometry']['location']['lat'], $result['geometry']['location']['lng']);
            $geometry->setLocation($location);
            $geocoderResult->setGeometry($geometry);
            $response->addResult($geocoderResult);
        }

        $response->setRequest($request);

        return $response;
    }
}
