<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: finance.proto

namespace App\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>app.GetExchangeRateRequest</code>
 */
class GetExchangeRateRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.app.Currency from = 1;</code>
     */
    private $from = 0;
    /**
     * Generated from protobuf field <code>.app.Currency to = 2;</code>
     */
    private $to = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $from
     *     @type int $to
     * }
     */
    public function __construct($data = NULL) {
        \App\Protobuf\Generated\GPBMetadata\Finance::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.app.Currency from = 1;</code>
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Generated from protobuf field <code>.app.Currency from = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setFrom($var)
    {
        GPBUtil::checkEnum($var, \App\Protobuf\Generated\Currency::class);
        $this->from = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.app.Currency to = 2;</code>
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Generated from protobuf field <code>.app.Currency to = 2;</code>
     * @param int $var
     * @return $this
     */
    public function setTo($var)
    {
        GPBUtil::checkEnum($var, \App\Protobuf\Generated\Currency::class);
        $this->to = $var;

        return $this;
    }

}

