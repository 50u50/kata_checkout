<?php

namespace Checkout\Repository;

use Checkout\Entity\Product;
use Checkout\Service\ResourceInterface;

/**
 * Class ProductRepository
 * @todo - switch to real implementation
 * @package Checkout\Repository
 * @codeCoverageIgnore
 */
class ProductRepository
{
    /** @var ResourceInterface */
    private $productResource;

    /**
     * ProductRepository constructor.
     * @param ResourceInterface $productResource
     */
    public function __construct(
        ResourceInterface $productResource
    ) {
        $this->productResource = $productResource;
    }

    /**
     * @param string $sku
     * @return mixed
     */
    public function getBySku(string $sku): Product
    {
        return $this->productResource->findBy('sku', $sku);
    }
}
