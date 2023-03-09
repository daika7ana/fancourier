<?php

namespace SeniorProgramming\FanCourier\Core;

use GuzzleHttp\Client as Guzzle;
use SeniorProgramming\FanCourier\Helpers\Csv;
use SeniorProgramming\FanCourier\Core\BaseInterface;
use SeniorProgramming\FanCourier\Exceptions\FanCourierInstanceException;
use SeniorProgramming\FanCourier\Exceptions\FanCourierInvalidParamException;
use SeniorProgramming\FanCourier\Exceptions\FanCourierUnknownModelException;

abstract class Base  implements BaseInterface
{
    protected $instance;

    /**
     *
     * @param string $class
     * @return \SeniorProgramming\FanCourier\Requests\class_call
     * @throws FanCourierUnknownModelException
     */
    public function instantiate($class)
    {
        $class_call = "SeniorProgramming\\FanCourier\\Requests\\" . $class;
        if (!class_exists($class_call)) {
            throw new FanCourierUnknownModelException("Class $class_call does not exist");
        }
        return new $class_call();
    }

    /**
     *
     * @param array $credentials
     * @param \SeniorProgramming\FanCourier\Requests\class_call $object
     * @return string
     * @throws FanCourierInvalidParamException
     */
    public function makeRequest($credentials, $object)
    {
        if (!is_object($object) && empty($object)) {
            throw new FanCourierInvalidParamException("Invalid object");
        }

        if (!in_array($object->fetchResults(), $this->checkResultType())) {
            throw new FanCourierInvalidParamException("Invalid result type");
        }
        $url = $this->getUrl($object);
        $this->instance = $object;

        $params = (array) $object;
        if (isset($params['callMethod'])) {
            unset($params['callMethod']);
        }

        if (is_callable([$this->instance, 'convertInCsv']) && !empty($this->instance->convertInCsv())) {
            $params = Csv::convertToCSV($this->instance->getParams(), $this->instance->convertInCsv());
        }

        $params += (array) $credentials;
        return $this->postCurlRequest($params, $url, $object->fetchResults());
    }

    /**
     *
     * @param array $data
     * @param string $url
     * @param string $resultType
     * @return string
     * @throws FanCourierInstanceException
     */
    private function postCurlRequest($data, $url, $resultType)
    {
        $this->checkParams($data);
        $this->checkUrl($url);

        $client = new Guzzle();
        $new_data = [];

        foreach ($data as $key => $value) {
            $new_data[] = array(
                'name' => $key,
                'contents' => is_object($value) ? fopen($value->name, 'r') : $value
            );
        }

        try {
            $response = $client->post($url, ['multipart' => $new_data]);
        } catch (\Exception $e) {
            throw new FanCourierInstanceException('Guzzle error. Message: ' . $e->getMessage());
        }

        return $this->getResultType($resultType, $response->getBody()->getContents());
    }

    /**
     *
     * @param \SeniorProgramming\FanCourier\Requests\class_call $object
     * @return string
     * @throws FanCourierInstanceException
     */
    private function getUrl($object)
    {
        if (empty($object->callMethod)) {
            throw new FanCourierInstanceException("Unset url request");
        }

        return $object->callMethod;
    }

    /**
     *
     * @param string $url
     * @throws FanCourierInstanceException
     */
    private function checkUrl($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new FanCourierInstanceException("Invalid url request");
        }
    }

    /**
     *
     * @param array $data
     * @throws \Exception
     */
    private function checkParams($data)
    {
        if (!is_array($data) && empty($data)) {
            throw new \Exception("Invalid params");
        }

        if (empty($data['username']) ||  empty($data['user_pass'])) {
            throw new \Exception("Invalid credentials");
        }
    }

    /**
     *
     * @return array
     */
    private function checkResultType()
    {
        return ['csv', 'plain', 'bool', 'parse', 'html', 'pdf', 'json'];
    }

    /**
     *
     * @param string $type
     * @param string $result
     * @return string|bool
     */
    private function getResultType($type, $result)
    {
        switch ($type) {
            case 'csv':
                return Csv::stringToObjects($result);
            case 'bool':
                return is_callable([$this->instance, 'parseResult']) ? $this->instance->parseResult($result) : false;
            case 'parse':
            case 'html':
            case 'json':
            case 'pdf':
                return is_callable([$this->instance, 'parseResult']) ? $this->instance->parseResult($result) : $result;
            default:
                return $result;
        }
    }
}
