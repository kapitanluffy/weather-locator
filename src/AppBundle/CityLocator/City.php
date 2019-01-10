<?php

namespace AppBundle\CityLocator;

class City
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function country()
    {
        return $this->data['country_code'];
    }

    public function name()
    {
        return $this->data['place_name'];
    }

    public function zipCode()
    {
        return $this->data['postal_code'];
    }

    public function lat()
    {
        return $this->data['latitude'];
    }

    public function lon()
    {
        return $this->data['longitude'];
    }

    public function toArray()
    {
        return [
            'country' => $this->country(),
            'name' => $this->name(),
            'zip_code' => $this->zipCode(),
            'lat' => $this->lat(),
            'lon' => $this->lon()
        ];
    }
}
