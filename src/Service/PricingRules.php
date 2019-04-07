<?php

namespace Checkout\Service;

use Checkout\Entity\QuoteItem;
use Checkout\Service\Discount\DiscounterInterface;

/**
 * Class PricingRules
 * To add new discounting level (e.g. Quote-level discounts), implement
 * - ::addQuoteDiscounter()
 * - ::applyQuoteDiscounter()
 * etc.
 * @package Checkout\Service
 */
class PricingRules
{
    /** @var DiscounterInterface[] */
    private $quoteItemDiscounters = [];

    /**
     * @param DiscounterInterface $discounter
     * @return PricingRules
     */
    public function addQuoteItemDiscounter(DiscounterInterface $discounter): self
    {
        $this->quoteItemDiscounters[$discounter->getId()] = $discounter;
        return $this;
    }

    /**
     * @param QuoteItem $quoteItem
     * @return PricingRules
     */
    public function applyQuoteItemDiscounts(QuoteItem $quoteItem): self
    {
        foreach ($this->quoteItemDiscounters as $discounter) {
            $discounter->applyDiscount($quoteItem);
        }
        return $this;
    }
}
