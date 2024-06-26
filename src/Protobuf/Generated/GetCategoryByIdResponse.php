<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: inventory.proto

namespace App\Protobuf\Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>app.GetCategoryByIdResponse</code>
 */
class GetCategoryByIdResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>.app.Category category = 1;</code>
     */
    private $category = null;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type \App\Protobuf\Generated\Category $category
     * }
     */
    public function __construct($data = NULL) {
        \App\Protobuf\Generated\GPBMetadata\Inventory::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>.app.Category category = 1;</code>
     * @return \App\Protobuf\Generated\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Generated from protobuf field <code>.app.Category category = 1;</code>
     * @param \App\Protobuf\Generated\Category $var
     * @return $this
     */
    public function setCategory($var)
    {
        GPBUtil::checkMessage($var, \App\Protobuf\Generated\Category::class);
        $this->category = $var;

        return $this;
    }

}

