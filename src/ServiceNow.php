<?php

namespace flashadvocate;

/**
 * Class Client
 *
 * @package App\ServiceNow
 */
class ServiceNow
{
    /**
     * @var
     */
    private $instance;

    /**
     * @var
     */
    private $username;

    /**
     * @var
     */
    private $password;

    /**
     * Client constructor.
     *
     * @param $instance
     * @param $username
     * @param $password
     */
    public function __construct(string $instance, string $username, string $password)
    {
        $this->instance = $instance;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param $table
     * @param $data
     * @return bool
     */
    public function createRecord($table, $data)
    {
        $data_string = json_encode($data);

        $url = 'https://' . $this->instance . '.service-now.com/api/now/table/' . $table;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
            ]
        );

        $result = curl_exec($ch);

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($responseCode != 201) {
            return false;
        }

        return $result;
    }

    /**
     * Authentication tester
     *
     * @return bool
     */
    public function authenticated()
    {
        $url = 'https://' . $this->instance . '.service-now.com/api/now/table/incident?sysparm_limit=1';

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
        ]);

        curl_exec($ch);

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return ($responseCode == 200);
    }

    /**
     * Return an incident path URL
     *
     * @param $inc
     * @return string
     */
    public function incidentPath($inc)
    {
        $path = "https://{$this->instance}.service-now.com/nav_to.do?uri=incident.do?sysparm_query=number={$inc}";

        return $path;
    }
}