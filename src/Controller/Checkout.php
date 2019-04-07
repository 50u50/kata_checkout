<?php

namespace Checkout\Controller;

use Checkout\Service\Logger;
use Checkout\Service\PricingRules;
use Checkout\Service\Quote;
use Checkout\Service\Scanner;
use Psr\Log\LoggerInterface;

/**
 * Class Checkout
 * @package Checkout\Controller
 */
class Checkout
{
    /** @var Quote */
    private $quote;

    /** @var Scanner */
    private $scanner;

    /** @var Logger */
    private $logger;

    /**
     * Checkout constructor.
     * @param PricingRules $pricingRules
     * @param Quote $quote
     * @param Scanner $scanner
     * @param LoggerInterface $logger
     */
    public function __construct(
        PricingRules $pricingRules,
        Quote $quote,
        Scanner $scanner,
        LoggerInterface $logger
    ) {
        $this->quote = $quote;
        $this->quote->setPricingRules($pricingRules);
        $this->scanner = $scanner;
        $this->logger = $logger;
    }

    /**
     * @param string $skus
     * @return Checkout
     */
    public function scan(string $skus): self
    {
        try {
            foreach ($this->scanner->scanSkus($skus) as $sku) {
                $this->quote->addProduct($sku);
            }
        } catch (\RuntimeException $e) {
            $this->logger->error($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error(Logger::MSG_UNKNOWN_APPLICATION_ERROR);
            $this->logger->critical($e);
        }
        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->quote->getTotal();
    }
}
