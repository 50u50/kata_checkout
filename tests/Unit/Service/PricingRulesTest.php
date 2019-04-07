<?php
namespace Checkout\Tests\Unit\Service;

use Checkout\Entity\QuoteItem;
use Checkout\Service\Discount\DiscounterInterface;
use Checkout\Service\PricingRules;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PricingRulesTest extends TestCase
{
    /** @var PricingRules */
    private $pricingRules;

    /** @var DiscounterInterface|MockObject */
    private $quoteItemDiscounterMock;

    /** @var QuoteItem|MockObject */
    private $quoteItemMock;

    protected function setUp()
    {
        parent::setUp();
        $this->prepareMocks();
        $this->pricingRules = new PricingRules();
    }

    public function testApplyQuoteItemDiscountsWithDifferentDiscountersWillCallApplyOnEach()
    {
        $this->quoteItemDiscounterMock
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturnOnConsecutiveCalls(1, 2);
        $this->quoteItemDiscounterMock
            ->expects($this->exactly(2))
            ->method('applyDiscount')
            ->with($this->quoteItemMock);
        $this->pricingRules
            ->addQuoteItemDiscounter($this->quoteItemDiscounterMock)
            ->addQuoteItemDiscounter($this->quoteItemDiscounterMock)
            ->applyQuoteItemDiscounts($this->quoteItemMock);
    }

    public function testApplyQuoteItemDiscountsWithSameDiscounterWillCallApplyOnce()
    {
        $this->quoteItemDiscounterMock
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturnOnConsecutiveCalls(1, 1);
        $this->quoteItemDiscounterMock
            ->expects($this->exactly(1))
            ->method('applyDiscount')
            ->with($this->quoteItemMock);
        $this->pricingRules
            ->addQuoteItemDiscounter($this->quoteItemDiscounterMock)
            ->addQuoteItemDiscounter($this->quoteItemDiscounterMock)
            ->applyQuoteItemDiscounts($this->quoteItemMock);
    }

    private function prepareMocks()
    {
        $this->quoteItemDiscounterMock = $this->createMock(DiscounterInterface::class);
        $this->quoteItemMock = $this->createMock(QuoteItem::class);
    }
}
