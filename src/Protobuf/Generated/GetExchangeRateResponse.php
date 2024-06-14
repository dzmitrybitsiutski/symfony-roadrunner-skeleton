<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: finance.proto

namespace App\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>app.GetExchangeRateResponse</code>
 */
class GetExchangeRateResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>double rate = 1;</code>
     */
    private $rate = 0.0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type float $rate
     * }
     */
    public function __construct($data = NULL) {
        \App\Protobuf\Generated\GPBMetadata\Finance::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>double rate = 1;</code>
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Generated from protobuf field <code>double rate = 1;</code>
     * @param float $var
     * @return $this
     */
    public function setRate($var)
    {
        GPBUtil::checkDouble($var);
        $this->rate = $var;

        return $this;
    }

}
