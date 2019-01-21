<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\CityLocator\CityLocatorInterface;
use AppBundle\CityLocator\City;
use AppBundle\Weather\WeatherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class MapController extends Controller
{
    public function index(Request $request, WeatherInterface $weather, CityLocatorInterface $cityLocator)
    {
        $placeName = $request->get('place_name');
        $zipCode = $request->get('zip_code');
        $count = $request->get('count', 10);

        $data = [
            'place_name' => $placeName,
            'zip_code' => $zipCode,
            'places_source' => $this->generateUrl('places')
        ];

        if ($placeName == false) {
            return $this->render('map/index.html.twig', $data);
        }

        $city = $cityLocator->get($placeName, $zipCode);

        if ($city == false) {
            return $this->render('map/index.html.twig', $data);
        }

        $main = $weather->get($city->lat(), $city->lon());

        if ($main->city == null) {
            return $this->render('map/index.html.twig', $data);
        }

        // convert Util\City to CityLocator\City
        $city = new City([
            'country_code' => $main->city->country,
            'place_name' => $main->city->name,
            'postal_code' => $city->zipCode(),
            'latitude' => $main->city->lat,
            'longitude' => $main->city->lon,
            'original' => $main->city
        ]);
        $data['city'] = $city->toArray();

        $nearby = $cityLocator->getNearbyCities($city, $count - 1, 1000);
        $weathers = [$main];

        foreach ($nearby as $n) {
            $weathers[] = $weather->get($n->lat(), $n->lon());
        }

        $data['weathers'] = $weathers;
        return $this->render('map/index.html.twig', $data);
    }

    public function places(Request $request, CityLocatorInterface $cityLocator)
    {
        $place = $request->get('q');

        $cities = $cityLocator->search($place);

        if ($cities == false) {
            $data = ['error' => 404, 'message' => 'not found'];
            return new JsonResponse(['data' => $data], 404);
        }

        $cities = array_map(function ($city) {
            return ['zip_code' => $city->zipCode(), 'name' => $city->name()];
        }, $cities);

        return new JsonResponse(['data' => $cities]);
    }
}
