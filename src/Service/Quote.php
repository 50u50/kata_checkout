<?php

namespace Checkout\Service;

use Checkout\Entity\QuoteItem;
use Checkout\Factory\QuoteItemFactory;

/**
 * Class Quote
 * @todo add persistence logic
 * @package Checkout\Service
 */
class Quote
{
    /** @var PricingRules */
    private $pricingRules;

    /** @var QuoteItemFactory */
    private $quoteItemFactory;

    /** @var QuoteItem[] */
    private $items = [];

    /**
     * Quote constructor.
     * @param QuoteItemFactory $quoteItemFactory
     */
    public function __construct(
        QuoteItemFactory $quoteItemFactory
    ) {
        $this->quoteItemFactory = $quoteItemFactory;
    }

    /**
     * @param PricingRules $pricingRules
     * @return Quote
     */
    public function setPricingRules(PricingRules $pricingRules): self
    {
        $this->pricingRules = $pricingRules;
        return $this;
    }

    /**
     * @param string $sku
     * @return Quote
     */
    public function addProduct(string $sku): self
    {
        if (empty($this->items[$sku])) {
            $this->items[$sku] = $this->quoteItemFactory->create($sku);
        } else {
            $this->items[$sku]->setQty(
                $this->items[$sku]->getQty() + 1
            );
        }
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        $total = 0.00;
        foreach ($this->items as $item) {
            $this->pricingRules->applyQuoteItemDiscounts($item);
            $total += $item->getProduct()->getBasePrice() * $item->getQty() - $item->getDiscount();
        }
        /** @todo round value */
        return $total;
    }
}
