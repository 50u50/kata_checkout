<?php

namespace Checkout\Service\Discount;

/**
 * Interface DiscounterInterface
 * @package Checkout\Service\Discount
 */
interface DiscounterInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param DiscountableInterface $discountable
     * @return mixed
     */
    public function applyDiscount(DiscountableInterface $discountable);
}
