<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: inventory.proto

namespace App\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>app.GetCategoriesResponse</code>
 */
class GetCategoriesResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .app.Category categories = 1;</code>
     */
    private $categories;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \App\Protobuf\Generated\Category[]|\Google\Protobuf\Internal\RepeatedField $categories
     * }
     */
    public function __construct($data = NULL) {
        \App\Protobuf\Generated\GPBMetadata\Inventory::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .app.Category categories = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Generated from protobuf field <code>repeated .app.Category categories = 1;</code>
     * @param \App\Protobuf\Generated\Category[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setCategories($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \App\Protobuf\Generated\Category::class);
        $this->categories = $arr;

        return $this;
    }

}

