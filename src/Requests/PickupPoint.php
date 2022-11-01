<?php

namespace SeniorProgramming\FanCourier\Requests;

use SeniorProgramming\FanCourier\Core\Endpoint;

class PickupPoint extends Endpoint
{
    protected $type = 'fanbox';

    /**
     *
     * @return string
     */
    protected function getCallMethod()
    {
        return 'pickup-points.php';
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
        if (empty($params))
            return true;

        parent::optionalParams(array_keys($params), ['type']);
        return true;
    }
}
