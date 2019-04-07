<?php

namespace Checkout\Entity;

use Checkout\Service\Discount\DiscountableInterface;

/**
 * Class QuoteItem
 * @package Checkout\Entity
 */
class QuoteItem implements DiscountableInterface
{
    /** @var Product */
    private $product;

    /** @var float */
    private $discount = 0.00;

    /** @var float */
    private $qty = 1;

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return QuoteItem
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return float
     */
    public function getQty(): float
    {
        return $this->qty;
    }

    /**
     * @param float $qty
     * @return QuoteItem
     */
    public function setQty(float $qty): self
    {
        $this->qty = $qty;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @inheritDoc
     */
    public function setDiscount(float $discount): self
    {
        $this->discount = $discount;
        return $this;
    }
}
