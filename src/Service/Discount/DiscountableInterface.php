<?php

namespace Checkout\Service\Discount;

/**
 * Interface DiscountableInterface
 * @package Checkout\Service\Discount
 */
interface DiscountableInterface
{
    /**
     * @return float
     */
    public function getDiscount(): float;

    /**
     * @param float $discount
     * @return mixed
     */
    public function setDiscount(float $discount);
}
