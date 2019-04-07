<?php

namespace Checkout\Factory;

use Checkout\Entity\QuoteItem;
use Checkout\Repository\ProductRepository;

/**
 * Class QuoteItemFactory
 * @package Checkout\Factory
 */
class QuoteItemFactory
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * QuoteItemFactory constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param $sku
     * @return QuoteItem
     */
    public function create($sku): QuoteItem
    {
        $product = $this->productRepository->getBySku($sku);
        $quoteItem = new QuoteItem();
        return $quoteItem->setProduct($product);
    }
}
