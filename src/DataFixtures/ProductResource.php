<?php

namespace Checkout\DataFixtures;

use Checkout\Entity\Product;
use Checkout\Service\ResourceInterface;

/**
 * Class ProductResource
 * @package Checkout\DataFixtures
 * @codeCoverageIgnore
 */
class ProductResource implements ResourceInterface
{
    const FILE_SOURCE = __DIR__ . '/products.csv';

    /** @var array */
    private $data = [];

    /**
     * ProductResource constructor.
     */
    public function __construct()
    {
        $this->loadProductData();
    }

    /**
     * @inheritDoc
     */
    public function findBy(string $field, $value)
    {
        if ($field === 'sku') {
            return $this->findBySku($value);
        } else {
            throw new \UnexpectedValueException("findBy $field is not implemented");
        }
    }

    /**
     * Loads fixture data from CSV
     */
    private function loadProductData()
    {
        foreach (file(self::FILE_SOURCE) as $i => $l) {
            if ($i === 0) {
                continue;
            }
            $lData = str_getcsv($l);
            $this->data[$lData[0]] = (float)$lData[1];
        }
    }

    /**
     * @param string $sku
     * @return Product
     */
    private function findBySku(string $sku): Product
    {
        if (empty($this->data[$sku])) {
            throw new \RuntimeException("Product not found (SKU:'$sku')");
        }
        $product = new Product();
        $product->setSku($sku);
        $product->setBasePrice($this->data[$sku]);
        return $product;
    }
}
