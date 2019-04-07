<?php

namespace Checkout\Service\Discount\Quote\Item;

use Checkout\Entity\QuoteItem;
use Checkout\Service\Discount\DiscountableInterface;
use Checkout\Service\Discount\DiscounterInterface;

/**
 * Class QtyFixedDiscounter
 * @package Checkout\Service\Discount\Quote\Item
 */
class QtyFixedDiscounter implements DiscounterInterface
{
    const ID_FORMAT = 'fixed_discount_for_qty_%d_of_%s_value_%d';

    /** @var array */
    private $skus;

    /** @var int */
    private $qtyThreshold;

    /** @var float */
    private $discountValue;

    /**
     * QtyFixedDiscounter constructor.
     * @param array $skus
     * @param int $qtyThreshold
     * @param float $discountValue
     */
    public function __construct(
        array $skus,
        int $qtyThreshold,
        float $discountValue
    ) {
        $this->skus = $skus;
        $this->qtyThreshold = (int)$qtyThreshold;
        $this->discountValue = (float)$discountValue;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return sprintf(
            self::ID_FORMAT,
            $this->qtyThreshold,
            implode(',', $this->skus),
            $this->discountValue
        );
    }

    /**
     * @param DiscountableInterface|QuoteItem $discountable
     * @return QtyFixedDiscounter
     */
    public function applyDiscount(DiscountableInterface $discountable): self
    {
        if (!$this->isApplicable($discountable)) {
            return $this;
        }
        /** with threshold = 2 and qty = 7, discount should be applied 3 times (cnt = 3) */
        $cnt = (int)($discountable->getQty() / $this->qtyThreshold);
        $discountable->setDiscount($cnt * $this->discountValue);
        return $this;
    }

    /**
     * @param QuoteItem $quoteItem
     * @return bool
     */
    private function isApplicable($quoteItem)
    {
        return in_array($quoteItem->getProduct()->getSku(), $this->skus) &&
            $quoteItem->getQty() >= $this->qtyThreshold;
    }
}
