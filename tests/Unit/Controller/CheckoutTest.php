<?php

namespace Checkout\Tests\Unit\Controller;

use Checkout\Controller\Checkout;
use Checkout\Service\Logger;
use Checkout\Service\PricingRules;
use Checkout\Service\Quote;
use Checkout\Service\Scanner;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CheckoutTest extends TestCase
{
    /** @var Checkout */
    private $checkout;

    /** @var Quote|MockObject */
    private $quoteMock;

    /** @var PricingRules|MockObject */
    private $pricingRulesMock;

    /** @var Scanner|MockObject */
    private $scannerMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    protected function setUp()
    {
        parent::setUp();
        $this->prepareMocks();
        $this->checkout = new Checkout(
            $this->pricingRulesMock,
            $this->quoteMock,
            $this->scannerMock,
            $this->loggerMock
        );
    }

    public function testScanWithEmptyScannerReturnWillNotCallQuoteAddProduct()
    {
        $this->scannerMock
            ->expects($this->once())
            ->method('scanSkus')
            ->willReturn([]);
        $this->quoteMock
            ->expects($this->never())
            ->method('addProduct');
        $this->checkout->scan('');
    }

    public function testScanWithSkusFoundWillCallQuoteAddProduct()
    {
        $str = '123';
        $skus = ['1', '2', '3'];
        $this->scannerMock
            ->expects($this->once())
            ->method('scanSkus')
            ->with($str)
            ->willReturn($skus);
        $this->quoteMock
            ->expects($this->exactly(3))
            ->method('addProduct')
            ->withConsecutive(['1'], ['2'], ['3']);
        $this->checkout->scan($str);
    }

    public function testScanWithRuntimeExceptionWillCallLoggerMethod()
    {
        $invalidInput = 'abc';
        $msg = 'Runtime Error!';
        $this->scannerMock
            ->expects($this->once())
            ->method('scanSkus')
            ->with($invalidInput)
            ->willThrowException(new \UnexpectedValueException($msg));
        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with($msg);
        $this->checkout->scan($invalidInput);
    }

    public function testScanWithLogicExceptionWillCallLoggerMethod()
    {
        $input = 'abc';
        $e = new \LogicException();
        $this->scannerMock
            ->expects($this->once())
            ->method('scanSkus')
            ->with($input)
            ->willThrowException($e);
        $this->loggerMock
            ->expects($this->once())
            ->method('error')
            ->with(Logger::MSG_UNKNOWN_APPLICATION_ERROR);
        $this->loggerMock
            ->expects($this->once())
            ->method('critical')
            ->with($e);
        $this->checkout->scan($input);
    }

    public function testGetTotalWillReturnQuoteValue()
    {
        $exp = 123.00;
        $this->quoteMock
            ->expects($this->once())
            ->method('getTotal')
            ->willReturn($exp);
        $act = $this->checkout->getTotal();
        $this->assertSame($exp, $act);
    }

    private function prepareMocks()
    {
        $this->quoteMock = $this->createMock(Quote::class);
        $this->pricingRulesMock = $this->createMock(PricingRules::class);
        $this->quoteMock
            ->expects($this->once())
            ->method('setPricingRules')
            ->with($this->pricingRulesMock)
            ->willReturnSelf();
        $this->scannerMock = $this->createMock(Scanner::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }
}
