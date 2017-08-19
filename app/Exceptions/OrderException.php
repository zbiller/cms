<?php

namespace App\Exceptions;

use Exception;

class OrderException extends Exception
{
    /**
     * @return static
     */
    public static function createOrderFailed()
    {
        return new static('Failed creating the order!');
    }

    /**
     * @return static
     */
    public static function updateOrderFailed()
    {
        return new static('Failed updating the order!');
    }

    /**
     * @return static
     */
    public static function syncTotalsFailed()
    {
        return new static('Failed syncing the order\'s totals!');
    }

    /**
     * @return static
     */
    public static function syncItemsFailed()
    {
        return new static('Failed syncing the order\'s items!');
    }

    /**
     * @return static
     */
    public static function noOrderItems()
    {
        return new static(
            'There are no items supplied for the order!' . PHP_EOL .
            'Please specify the items for the order by defining an array in which for each item, a "product_id" and "quantity" key should be present.'
        );
    }

    /**
     * @return static
     */
    public static function invalidOrderItem()
    {
        return new static(
            'No product id was supplied inside the order\'s items details!' . PHP_EOL .
            'Please specify the product id by including the "product_id" key to your order\'s items data.'
        );
    }

    /**
     * @return static
     */
    public static function invalidOrderItemProduct()
    {
        return new static(
            'Could not find a product corresponding to the order item\'s product id value!'
        );
    }

    /**
     * @return static
     */
    public static function invalidCustomerFirstName()
    {
        return new static(
            'No first name was supplied inside the customer\'s details!' . PHP_EOL .
            'Please specify the first name by including the "first_name" key to your customer data.'
        );
    }

    /**
     * @return static
     */
    public static function invalidCustomerLastName()
    {
        return new static(
            'No last name was supplied inside the customer\'s details!' . PHP_EOL .
            'Please specify the last name by including the "last_name" key to your customer data.'
        );
    }

    /**
     * @return static
     */
    public static function invalidCustomerEmail()
    {
        return new static(
            'No email was supplied inside the customer\'s details!' . PHP_EOL .
            'Please specify the email by including the "email" key to your customer data.'
        );
    }

    /**
     * @return static
     */
    public static function invalidCustomerPhone()
    {
        return new static(
            'No phone was supplied inside the customer\'s details!' . PHP_EOL .
            'Please specify the phone by including the "phone" key to your customer data.'
        );
    }

    /**
     * @return static
     */
    public static function invalidShippingDetails()
    {
        return new static(
            'No shipping address was supplied inside the addresses details!' . PHP_EOL .
            'Please specify the shipping by including the "shipping" key to your addresses data.' . PHP_EOL .
            'The "shipping" key should represent an array containing: "country" (optional), "state" (optional), "city" (required), "address" (required).'
        );
    }

    /**
     * @return static
     */
    public static function invalidShippingCity()
    {
        return new static(
            'No shipping city was supplied inside the shipping address details!' . PHP_EOL .
            'Please specify the shipping city by including the "city" key to your shipping address data. ("shipping" => ["city" => ""])'
        );
    }

    /**
     * @return static
     */
    public static function invalidShippingAddress()
    {
        return new static(
            'No shipping address was supplied inside the shipping address details!' . PHP_EOL .
            'Please specify the shipping address by including the "address" key to your shipping address data. ("shipping" => ["address" => ""])'
        );
    }

    /**
     * @return static
     */
    public static function invalidBillingDetails()
    {
        return new static(
            'No billing address was supplied inside the addresses details!' . PHP_EOL .
            'Please specify the billing by including the "billing" key to your addresses data.' . PHP_EOL .
            'The "billing" key should represent an array containing: "country" (optional), "state" (optional), "city" (required), "address" (required).'
        );
    }

    /**
     * @return static
     */
    public static function invalidBillingCity()
    {
        return new static(
            'No billing city was supplied inside the billing address details!' . PHP_EOL .
            'Please specify the billing city by including the "city" key to your billing address data. ("billing" => ["city" => ""])'
        );
    }

    /**
     * @return static
     */
    public static function invalidBillingAddress()
    {
        return new static(
            'No billing address was supplied inside the billing address details!' . PHP_EOL .
            'Please specify the billing address by including the "address" key to your billing address data. ("billing" => ["address" => ""])'
        );
    }
}