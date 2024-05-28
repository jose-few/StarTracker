<?php

require_once('../vendor/autoload.php');

class StarTracker
{
    /**
     * @var string
     * Base url for API calls.
     */
    public string $base = "https://api.astronomyapi.com/api/v2/";

    /**
     * @var string|null
     * Base64 Auth token.
     */
    public ?string $auth = null;

    /**
     * @var string|null
     * Combined Bearer token for API calls.
     */
    public ?string $bearer = null;

    /**
     * @var string
     * Public App ID used for verifying API calls.
     * Can be generated at astronomyapi.com
     *
     * Store in config/config.php.
     */
    public string $appID = "";

    /**
     * @var string
     * Secret ID used for verifying API calls.
     * Can be generated at astronomyapi.com
     *
     * Store in config/config.php.
     */
    public string $secret = "";

    public function __construct()
    {
        include('../config/config.php');

        $this->appID = $appID;
        $this->secret = $secret;

        $this->auth = base64_encode("{$this->appID}:{$this->secret}");
        $this->bearer = "Authorization: Basic {$this->auth}";
    }

    /**
     * @param $data
     * @return string
     *
     * Renders a table for each planetary body in the $data array.
     */
    public function toScreen($data): string
    {
        $out = "";

        foreach ($data as $thisBody => $positions)
        {
            $out.="<h2>{$thisBody}</h2>";
            $out.="<table><thead><tr>";

            foreach ($positions[0] as $head => $body)
            {
                $out.="<th>{$head}</th>";
            }

            $out.="</tr></thead><tbody>";

            foreach ($positions as $position)
            {
                $out.="<tr>";

                foreach ($position as $value)
                {
                    $out.="<td>{$value}</td>";
                }

                $out.="</tr>";
            }

            $out.="</tbody></table>";
        }

        return $out;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array|null $body
     * @return mixed
     *
     * Connects to the API to make a call, using a provided URL.
     */
    public function connect(string $url = 'bodies', string $method = 'GET', ?array $body = null): mixed
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->base.$url);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        $headers = array($this->bearer);

        if ($method == "POST")
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $res = json_decode(curl_exec($curl), true)['data'];

        curl_close($curl);

        return $res;
    }

    /**
     * @param array $location
     * @return array
     *
     * Searches for Planetary bodies in the Solar System.
     *
     * $location is an associative array with the following data required:
     * 'latitude' => '',
     * 'longitude' => '',
     * 'elevation' => '',
     * 'from_date' => 'Y-m-d',
     * 'to_date' => 'Y-m-d',
     * 'time' => 'H:i:s',
     * 'output' => 'rows'
     *
     * latitude, longitude, elevation specify the location of the observer.
     * from_date and to_date specify the dates that positions will be returned for.
     * time specifies what time of night the observer will be observing.
     * output must be set to 'rows' for table display to work correctly.
     */
    public function searchPositions(array $location): array
    {
        $res = $this->connect("bodies/positions?".http_build_query($location));

        $data = array();

        foreach ($res['rows'] as $thisBody)
        {
            //Exclude Sun && Earth for obvious reasons.
            if ($thisBody['body']['name'] != 'Sun' && $thisBody['body']['name'] != 'Earth')
            {
                $data[$thisBody['body']['name']] = array();

                foreach ($thisBody['positions'] as $key => $thisPosition) {
                    $data[$thisBody['body']['name']][$key] = array();
                    $data[$thisBody['body']['name']][$key]['Date'] = date('Y-m-d H:i:s', strtotime($thisPosition['date']));
                    $data[$thisBody['body']['name']][$key]['Altitude'] = $thisPosition['position']['horizontal']['altitude']['string'];
                    $data[$thisBody['body']['name']][$key]['Azimuth'] = $thisPosition['position']['horizontal']['azimuth']['string'];
                    $data[$thisBody['body']['name']][$key]['Right Ascension'] = $thisPosition['position']['equatorial']['rightAscension']['string'];
                    $data[$thisBody['body']['name']][$key]['Declination'] = $thisPosition['position']['equatorial']['declination']['string'];
                    $data[$thisBody['body']['name']][$key]['Distance (AU)'] = $thisPosition['distance']['fromEarth']['au'];
                }
            }
        }

        return $data;
    }

    /**
     * @param array|null $observer
     * @param array|null $view
     * @return mixed
     *
     * Renders a star-chart based on the observer location ('lat', 'long', 'date')
     * and any items of interest to highlight.
     *
     * Defaults to the constellation of Orion as viewed from Honiton, England.
     *
     * Returns error on fail, or a url to image for use in img src tag on success.
     */
    public function starChart(?array $observer = null, ?array $view = null)
    {
        $style = 'default';

        if (is_null($observer))
        {
            $observer = array('latitude' => 50.79094, 'longitude' => -3.20736, 'date' => date('Y-m-d'));
        }

        if (is_null($view))
        {
            $view = array('type' => 'constellation', 'parameters' => array('constellation' => 'ori'));
        }

        $params = array('style' => $style, 'observer' => $observer, 'view' => $view);

        return $this->connect('studio/star-chart', 'POST', $params)['imageUrl'];
    }
}