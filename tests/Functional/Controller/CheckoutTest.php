<?php
declare(strict_types=1);

namespace Checkout\Tests\Functional\Controller;

use Checkout\Controller\Checkout;
use Checkout\DataFixtures\ProductResource;
use Checkout\Factory\QuoteItemFactory;
use Checkout\Repository\ProductRepository;
use Checkout\Service\Discount\Quote\Item\QtyFixedDiscounter;
use Checkout\Service\Logger;
use Checkout\Service\PricingRules;
use Checkout\Service\Quote;
use Checkout\Service\Scanner;
use PHPUnit\Framework\TestCase;

class CheckoutTest extends TestCase
{
    /**
     * @dataProvider getTotalValidDataProvider
     * @param $scans
     * @param $exp
     */
    public function testGetTotalWithValidDataProviderWillReturnExpected($scans, $exp)
    {
        $checkout = $this->prepareCheckout();
        foreach ($scans as $scan) {
            $checkout->scan($scan);
        }
        $act = $checkout->getTotal();
        $this->assertSame($exp, $act);
    }

    public function getTotalValidDataProvider()
    {
        return [
            [
                'scans' => [''],
                'exp' => 0.00,
            ],
            [
                'scans' => ['A'],
                'exp' => 50.00,
            ],
            [
                'scans' => ['AB'],
                'exp' => 80.00,
            ],
            [
                'scans' => ['CDBA'],
                'exp' => 115.00,
            ],
            [
                'scans' => ['AA'],
                'exp' => 100.00,
            ],
            [
                'scans' => ['AAA'],
                'exp' => 130.00,
            ],
            [
                'scans' => ['AAAA'],
                'exp' => 180.00,
            ],
            [
                'scans' => ['AAAAA'],
                'exp' => 230.00,
            ],
            [
                'scans' => ['AAAAAA'],
                'exp' => 260.00,
            ],
            [
                'scans' => ['AAAB'],
                'exp' => 160.00,
            ],
            [
                'scans' => ['AAABB'],
                'exp' => 175.00,
            ],
            [
                'scans' => ['AAABBD'],
                'exp' => 190.00,
            ],
            [
                'scans' => ['DABABA'],
                'exp' => 190.00,
            ],
            [
                'scans' => ['A', 'B', 'A', 'A', 'B'],
                'exp' => 175.00,
            ],
        ];
    }

    private function prepareCheckout(): Checkout
    {
        /** prepare pricingRules */
        $pricingRules = new PricingRules();
        $discount1 = new QtyFixedDiscounter(
            ['A'],
            3,
            20
        );
        $discount2 = new QtyFixedDiscounter(
            ['B'],
            2,
            15
        );
        $pricingRules
            ->addQuoteItemDiscounter($discount1)
            ->addQuoteItemDiscounter($discount2);

        /** prepare quote */
        $productResourceFixture = new ProductResource();
        $productRepository = new ProductRepository($productResourceFixture);
        $quoteItemFactory = new QuoteItemFactory($productRepository);
        $quote = new Quote($quoteItemFactory);

        return new Checkout(
            $pricingRules,
            $quote,
            new Scanner(),
            new Logger()
        );
    }
}
