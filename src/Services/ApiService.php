<?php

namespace SeniorProgramming\FanCourier\Services;

use SeniorProgramming\FanCourier\Core\Base;
use SeniorProgramming\FanCourier\Helpers\Hints;
use SeniorProgramming\FanCourier\Exceptions\FanCourierInstanceException;
use SeniorProgramming\FanCourier\Exceptions\FanCourierInvalidParamException;
use SeniorProgramming\FanCourier\Exceptions\FanCourierUnknownModelException;

class ApiService extends Base
{
    private $credentials;

    /**
     *
     * @throws FanCourierInvalidParamException
     */
    public function __construct()
    {
        if (!config('fancourier.username') || !config('fancourier.password')) {
            throw new FanCourierInvalidParamException('Please set FANCOURIER_USERNAME, FANCOURIER_PASSWORD environment variables.');
        }

        $this->credentials = array_filter([
            'username'  => config('fancourier.username'),
            'user_pass'  => config('fancourier.password'),
            'client_id' => config('fancourier.client_id'),
        ]);
    }

    /**
     *
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws FanCourierUnknownModelException
     * @throws FanCourierInstanceException
     */
    public function __call($method, $args = [])
    {
        $instance = parent::instantiate(ucfirst($method));

        if (!is_callable([$instance, 'set'])) {
            throw new FanCourierUnknownModelException("Method $method does not exist");
        }

        try {
            return parent::makeRequest($this->credentials, call_user_func_array([$instance, 'set'], $args));
        } catch (Exception $ex) {
            throw new FanCourierInstanceException("Invalid request");
        }
    }

    public function csvImportHelper()
    {
        return Hints::importCsvKeys();
    }
}
