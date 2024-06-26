<?php
# Generated by the protocol buffer compiler (roadrunner-server/grpc). DO NOT EDIT!
# source: inventory.proto

namespace App\Protobuf\Generated;

use Spiral\RoadRunner\GRPC;

interface InventoryInterface extends GRPC\ServiceInterface
{
    // GRPC specific service name.
    public const NAME = "app.Inventory";

    /**
    * @param GRPC\ContextInterface $ctx
    * @param GetProductByIdRequest $in
    * @return GetProductByIdResponse
    *
    * @throws GRPC\Exception\InvokeException
    */
    public function GetProductById(GRPC\ContextInterface $ctx, GetProductByIdRequest $in): GetProductByIdResponse;

    /**
    * @param GRPC\ContextInterface $ctx
    * @param GetCategoryByIdRequest $in
    * @return GetCategoryByIdResponse
    *
    * @throws GRPC\Exception\InvokeException
    */
    public function GetCategoryById(GRPC\ContextInterface $ctx, GetCategoryByIdRequest $in): GetCategoryByIdResponse;

    /**
    * @param GRPC\ContextInterface $ctx
    * @param \Google\Protobuf\GPBEmpty $in
    * @return GetCategoriesResponse
    *
    * @throws GRPC\Exception\InvokeException
    */
    public function GetCategories(GRPC\ContextInterface $ctx, \Google\Protobuf\GPBEmpty $in): GetCategoriesResponse;
}
