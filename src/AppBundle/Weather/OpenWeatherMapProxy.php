<?php

namespace AppBundle\Weather;

use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\CurrentWeather;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

/**
 * Proxy class for cmfcmf\openweathermap
 *
 * Converted private methods to protected visibility
 * to allow us to extend the main OpenWeatherMap class
 */
class OpenWeatherMapProxy extends OpenWeatherMap
{
    protected $urls = [
        'find' => 'http://api.openweathermap.org/data/2.5/find?'
    ];

    /**
     * Get cities in cycle
     *
     * @param  int $lat
     * @param  int $lon
     * @param  int    $cnt
     * @param  string $units
     * @param  string $lang
     * @param  string $appid
     * @param  string $mode
     *
     * @return CurrentWeather[]
     */
    public function getCitiesInCycle($lat, $lon, $cnt = 10, $units = 'imperial', $lang = 'en', $appid = '', $mode = 'xml')
    {
        $url = $this->buildUrl(['lat' => $lat, 'lon' => $lon, 'cnt' => $cnt], $units, $lang, $appid, $mode, $this->urls['find']);

        $response = $this->fetch($url);
        $xml = $this->parseXML($response);
        $cities = [];

        foreach ($xml->list->item as $item) {
            $cities[] = new CurrentWeather($item, $units);
        }

        return $cities;
    }

    protected function parseXML($answer)
    {
        // Disable default error handling of SimpleXML (Do not throw E_WARNINGs).
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
            return new \SimpleXMLElement($answer);
        } catch (\Exception $e) {
            // Invalid xml format. This happens in case OpenWeatherMap returns an error.
            // OpenWeatherMap always uses json for errors, even if one specifies xml as format.
            $error = json_decode($answer, true);
            if (isset($error['message'])) {
                throw new OWMException($error['message'], isset($error['cod']) ? $error['cod'] : 0);
            } else {
                throw new OWMException('Unknown fatal error: OpenWeatherMap returned the following json object: ' . $answer);
            }
        }
    }

    protected function buildUrl($query, $units, $lang, $appid, $mode, $url)
    {
        $queryUrl = $this->buildQueryUrlParameter($query);

        $url = $url."$queryUrl&units=$units&lang=$lang&mode=$mode&APPID=";
        $url .= empty($appid) ? $this->getApiKey() : $appid;

        return $url;
    }

    protected function buildQueryUrlParameter($query)
    {
        switch ($query) {
            case is_array($query) && isset($query['lat']) && isset($query['lon']) && is_numeric($query['lat']) && is_numeric($query['lon']):
                return "lat={$query['lat']}&lon={$query['lon']}";
            case is_array($query) && is_numeric($query[0]):
                return 'id='.implode(',', $query);
            case is_numeric($query):
                return "id=$query";
            case is_string($query) && strpos($query, 'zip:') === 0:
                $subQuery = str_replace('zip:', '', $query);
                return 'zip='.urlencode($subQuery);
            case is_string($query):
                return 'q='.urlencode($query);
            default:
                throw new \InvalidArgumentException('Error: $query has the wrong format. See the documentation of OpenWeatherMap::getWeather() to read about valid formats.');
        }
    }

    protected function fetch($url, $options = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt_array($ch, $options);

        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }
}
