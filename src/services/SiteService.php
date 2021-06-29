<?php

namespace src\services;

use components\View;
use ErrorException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use src\models\Country;
use src\models\Ip;

/**
 * Class SiteService
 * @package src\services
 */
class SiteService
{
    /**
     * @var string|mixed
     */
    private string $ipstack_key;

    /**
     * SiteService constructor.
     * @throws ErrorException
     */
    public function __construct()
    {
        $application_params = include_once(PROJECT_ROOT . '/config/params.php');

        if (empty($application_params['ipstack_key'])) {

            throw new ErrorException('ipstack_key not set in config');
        }

        $this->ipstack_key = $application_params['ipstack_key'];
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    public function calculateData(): array
    {
        //validate

        $data = $this->parseData();

        $result = [];

        foreach ($data as $customer) {
            $continent_by_phone = $continent_by_ip = 0;
            if ($customer->customer_ip) {
                $continent_by_ip = $this->getContinentByIp($customer->customer_ip);
                $continent_by_phone = $this->getContinentByPhoneNumber($customer->dialed_phone_number, $continent_by_ip->continent_code);
            }

            if ($customer->customer_id) {
                if (!isset($result[$customer->customer_id]['customer_id'])) {
                    $result[$customer->customer_id] = [
                        'customer_id' => $customer->customer_id,
                        'total_number_of_all_calls' => 0,
                        'the_total_duration_of_all_calls' => 0,

                        'number_of_calls_within_the_same_continent' => 0,
                        'total_duration_of_calls_within_the_same_continent' => 0,
                    ];
                }

                $result[$customer->customer_id]['total_number_of_all_calls'] += 1;
                $result[$customer->customer_id]['the_total_duration_of_all_calls'] += $customer->duration_in_seconds;

                if ($continent_by_phone) {
                    $result[$customer->customer_id]['number_of_calls_within_the_same_continent'] += 1;
                    $result[$customer->customer_id]['total_duration_of_calls_within_the_same_continent'] += $customer->duration_in_seconds;
                }
            }
        }

        return array_values($result);
    }

    /**
     * @return array
     */
    public function parseData(): array
    {
        if (empty($_FILES['userfile']['tmp_name'])) {
            $view = new View();
            $view->render('/views/site/error', ['userfile not found']);
        }

        $csv_file = file($_FILES['userfile']['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $raw_data = array_map('str_getcsv', $csv_file);

        $data = [];

        foreach ($raw_data as $item) {
            $data[] = array_combine(
                [
                    'customer_id',
                    'call_date',
                    'duration_in_seconds',
                    'dialed_phone_number',
                    'customer_ip',
                ],
                $item
            );
        }

        return json_decode(json_encode($data));
    }

    /**
     * @param Ip     $ipModel
     * @param Client $guzzleClient
     * @param string $ip
     * @return object
     * @throws GuzzleException
     * @throws Exception
     */
    public function getIpStackInfo(Ip $ipModel, Client $guzzleClient, string $ip): object
    {
        $response = $guzzleClient->request(
            'GET',
            'http://api.ipstack.com/' . $ip . '?access_key=' . $this->ipstack_key . '&format=1'
        );

        if (!$ipModel->create($ip, $response->getBody()->getContents())) {
            throw new Exception('error create ip record');
        }

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param string $ip
     * @return object
     * @throws GuzzleException
     */
    public function getContinentByIp(string $ip): object {
        $ipModel = new Ip();

        $client = new Client();

        $ip_info = $ipModel->getInfo($ip);

        if (empty($ip_info)) {
            return $this->getIpStackInfo($ipModel, $client, $ip);
        } else {
            return json_decode($ip_info->params);
        }
    }

    /**
     * @param string $phone_number
     * @param string $continent_code
     * @return bool
     */
    public function getContinentByPhoneNumber(string $phone_number, string $continent_code): bool {
        $countryModel = new Country();
        return $countryModel->getInfo($phone_number, $continent_code);
    }
}
