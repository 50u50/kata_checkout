<?php

namespace Checkout\Tests\Unit\Service\Discount\Quote\Item;

use Checkout\Entity\Product;
use Checkout\Entity\QuoteItem;
use Checkout\Service\Discount\Quote\Item\QtyFixedDiscounter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QtyFixedDiscounterTest extends TestCase
{
    /** @var QtyFixedDiscounter */
    private $qtyFixedDiscounter;

    /** @var array */
    private $applicableSkus = [1, 2, 3];

    /** @var integer */
    private $qtyThreshold = 100;

    /** @var float */
    private $discountValue = 50;

    /** @var QuoteItem|MockObject */
    private $quoteItemMock;

    /** @var Product|MockObject */
    private $productMock;

    protected function setUp()
    {
        parent::setUp();
        $this->prepareMocks();
        $this->qtyFixedDiscounter = new QtyFixedDiscounter(
            $this->applicableSkus,
            $this->qtyThreshold,
            $this->discountValue
        );
    }

    public function testGetIdWithNewDiscounterWillGenerateDifferentKey()
    {
        $key1 = $this->qtyFixedDiscounter->getId();
        $qtyFixedDiscounter2 = new QtyFixedDiscounter([1, 2, 3], 100, 1);
        $key2 = $qtyFixedDiscounter2->getId();
        $this->assertNotSame($key1, $key2);
    }

    public function testApplyDiscountWithWrongQuoteItemWillSkipSettingValue()
    {
        $wrongSku = 999;
        $this->productMock
            ->expects($this->once())
            ->method('getSku')
            ->willReturn($wrongSku);
        $this->quoteItemMock
            ->expects($this->never())
            ->method('setDiscount');
        $this->qtyFixedDiscounter->applyDiscount($this->quoteItemMock);
    }

    public function testApplyDiscountWithWrongQtyWillSkipSettingValue()
    {
        $properSku = 1;
        $wrongQty = 1;
        /** 1 < 100 (threshold) */
        $this->productMock
            ->expects($this->once())
            ->method('getSku')
            ->willReturn($properSku);
        $this->quoteItemMock
            ->expects($this->once())
            ->method('getQty')
            ->willReturn($wrongQty);
        $this->quoteItemMock
            ->expects($this->never())
            ->method('setDiscount');

        $this->qtyFixedDiscounter->applyDiscount($this->quoteItemMock);
    }

    /**
     * @dataProvider applyDiscountDataProvider
     * @param $properQty
     * @param $discountValue
     */
    public function testApplyDiscountWithDataProviderWillReturnExpected($properQty, $discountValue)
    {
        $properSku = 1;
        $this->productMock
            ->expects($this->once())
            ->method('getSku')
            ->willReturn($properSku);
        $this->quoteItemMock
            ->expects($this->any())
            ->method('getQty')
            ->willReturn($properQty);
        $this->quoteItemMock
            ->expects($this->once())
            ->method('setDiscount')
            ->with($discountValue);

        $this->qtyFixedDiscounter->applyDiscount($this->quoteItemMock);
    }

    public function applyDiscountDataProvider()
    {
        return [
            [
                'qty' => $this->qtyThreshold,
                'discount' => $this->discountValue,
            ],
            [
                'qty' => $this->qtyThreshold + 1,
                'discount' => $this->discountValue,
            ],
            [
                'qty' => $this->qtyThreshold * 2 - 1,
                'discount' => $this->discountValue,
            ],
            [
                'qty' => $this->qtyThreshold * 3,
                'discount' => 3 * $this->discountValue,
            ],
        ];
    }

    private function prepareMocks()
    {
        $this->productMock = $this->createMock(Product::class);
        $this->quoteItemMock = $this->createConfiguredMock(QuoteItem::class, [
            'getProduct' => $this->productMock,
        ]);
    }
}
