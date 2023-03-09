<?php

namespace SeniorProgramming\FanCourier\Requests;

use SeniorProgramming\FanCourier\Core\Endpoint;
use SeniorProgramming\FanCourier\Exceptions\FanCourierInvalidParamException;

class ClientIds extends Endpoint
{
    /**
     *
     * @return string
     */
    protected function getCallMethod()
    {
        return 'get_account_clients_integrat.php';
    }

    /**
     *
     * @return string
     */
    public function fetchResults()
    {
        return 'json';
    }

    /**
     *
     * @param string $result
     * @return int|string
     */
    public function parseResult($result)
    {
        return json_decode($result, true);
    }

    /**
     *
     * @param array $params
     * @return boolean
     */
    public function validate($params)
    {
        if (!empty($params))
            throw new FanCourierInvalidParamException('No fields required');

        return true;
    }
}
