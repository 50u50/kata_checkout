<?php

namespace Checkout\Tests\Service;

use Checkout\Entity\Product;
use Checkout\Entity\QuoteItem;
use Checkout\Factory\QuoteItemFactory;
use Checkout\Service\PricingRules;
use Checkout\Service\Quote;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QuoteTest extends TestCase
{
    /** @var Quote */
    private $quote;

    /** @var QuoteItemFactory|MockObject */
    private $quoteItemFactoryMock;

    /** @var QuoteItem|MockObject */
    private $quoteItemMock;

    /** @var Product|MockObject */
    private $productMock;

    /** @var PricingRules|MockObject */
    private $pricingRulesMock;

    protected function setUp()
    {
        parent::setUp();
        $this->prepareMocks();
        $this->quote = new Quote(
            $this->quoteItemFactoryMock
        );
    }

    public function testAddProductWithSkuWillCallQuoteItemFactory()
    {
        $sku = 1;
        $this->quoteItemFactoryMock
            ->expects($this->once())
            ->method('create')
            ->with($sku)
            ->willReturn($this->quoteItemMock);
        $this->quote->addProduct($sku);
    }

    public function testAddProductWithDifferentProductsWillCreateItems()
    {
        $sku1 = 1;
        $sku2 = 2;
        $this->quoteItemFactoryMock
            ->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive([$sku1], [$sku2])
            ->willReturn($this->quoteItemMock);
        $this->quote
            ->addProduct($sku1)
            ->addProduct($sku2);
    }

    public function testAddProductWithSameProductTwiceWillCreateSingleItem()
    {
        $sku1 = 1;
        $this->quoteItemFactoryMock
            ->expects($this->once(1))
            ->method('create')
            ->with($sku1)
            ->willReturn($this->quoteItemMock);
        $this->quote
            ->addProduct($sku1)
            ->addProduct($sku1);
    }

    public function testAddProductWithSameProductTwiceWillUpdateItemQtyOnce()
    {
        $sku1 = 1;
        $this->quoteItemFactoryMock
            ->expects($this->once(1))
            ->method('create')
            ->with($sku1)
            ->willReturn($this->quoteItemMock);
        $this->quoteItemMock
            ->expects($this->once())
            ->method('getQty')
            ->willReturn(1); /** QuoteItemFactory::create() sets qty = 1 */
        $this->quoteItemMock
            ->expects($this->once())
            ->method('setQty')
            ->with(2);
        $this->quote
            ->addProduct($sku1)
            ->addProduct($sku1);
    }

    public function testGetTotalWithNoItemsWillReturnZero()
    {
        $exp = 0.00;
        $act = $this->quote->getTotal();
        $this->assertSame($exp, $act);
    }

    public function testGetTotalWithItemWillCallApplyDiscount()
    {
        $sku = 1;
        $this->pricingRulesMock
            ->expects($this->once())
            ->method('applyQuoteItemDiscounts')
            ->with($this->quoteItemMock);

        $this->quote->setPricingRules($this->pricingRulesMock);
        $this->quote->addProduct($sku);
        $this->quote->getTotal();
    }

    /**
     * @dataProvider  getTotalDataProvider
     * @param $skus
     * @param $getQty
     * @param $getBasePrice
     * @param $getDiscount
     * @param $exp
     */
    public function testGetTotalWithDataProviderWillReturnExpected($skus, $getQty, $getBasePrice, $getDiscount, $exp)
    {
        /** Prepare quote */
        foreach ($skus as $k => $sku) {
            $this->quote->addProduct($sku);
        }

        $this->willReturnOnConsecutiveCallsArray(
            $this->productMock
                ->expects($this->any())
                ->method('getBasePrice'),
            $getBasePrice
        );
        $this->willReturnOnConsecutiveCallsArray(
            $this->quoteItemMock
                ->expects($this->any())
                ->method('getQty'),
            $getQty
        );
        $this->willReturnOnConsecutiveCallsArray(
            $this->quoteItemMock
                ->expects($this->any())
                ->method('getDiscount'),
            $getDiscount
        );

        $this->quote->setPricingRules($this->pricingRulesMock);
        $act = $this->quote->getTotal();
        $this->assertSame($exp, $act);
    }

    /**
     * @param $mockObject
     * @param array $array
     */
    private function willReturnOnConsecutiveCallsArray($mockObject, array $array)
    {
        call_user_func_array([$mockObject, 'willReturnOnConsecutiveCalls'], $array);
    }

    public function getTotalDataProvider()
    {
        return [
            [
                'skus' => [1],
                'getQty' => [1],
                'getBasePrice' => [10],
                'getDiscount' => [0],
                'exp' => 10.00,
            ],
            [
                'skus' => [1],
                'getQty' => [1],
                'getBasePrice' => [10],
                'getDiscount' => [5],
                'exp' => 5.00,
            ],
            [
                'skus' => [1, 2, 3],
                'getQty' => [1, 2, 3],
                'getBasePrice' => [10, 20, 30],
                'getDiscount' => [1, 2, 3],
                'exp' => 134.00,/** 10*1 + 20*2 + 30*3 - (1 + 2 + 3) */
            ],
        ];
    }

    private function prepareMocks()
    {
        $this->productMock = $this->createMock(Product::class);
        $this->quoteItemMock = $this->createConfiguredMock(QuoteItem::class, [
            'getProduct' => $this->productMock,
        ]);
        $this->quoteItemFactoryMock = $this->createConfiguredMock(QuoteItemFactory::class, [
            'create' => $this->quoteItemMock,
        ]);
        $this->pricingRulesMock = $this->createMock(PricingRules::class);
    }
}
