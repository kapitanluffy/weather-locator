<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\CityLocator\CityLocator;
use AppBundle\Weather\WeatherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class MapController extends Controller
{
    public function index(Request $request, WeatherInterface $weather, CityLocator $cityLocator)
    {
        $placeName = $request->get('place_name');
        $zipCode = $request->get('zip_code');

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

        $data['city'] = $city->toArray();

        $weathers = $weather->get($city->lat(), $city->lon());

        if ($weathers == false) {
            return $this->render('map/index.html.twig', $data);
        }

        $data['weathers'] = $weathers;
        return $this->render('map/index.html.twig', $data);
    }

    public function places(Request $request, CityLocator $cityLocator)
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
