<?php

namespace Checkout\Service;

/**
 * Interface ResourceInterface
 * @package Checkout\Repository
 */
interface ResourceInterface
{
    /**
     * @param string $field
     * @param $value
     * @return mixed
     */
    public function findBy(string $field, $value);
}
