<?php

namespace Checkout\Service;

/**
 * Class Scanner
 * @package Checkout\Service
 */
class Scanner
{
    /**
     * @param string $s
     * @return array
     */
    public function scanSkus(string $s): array
    {
        if (empty($s)) {
            return [];
        }
        $this->validateInput($s);
        return str_split($s);
    }

    /**
     * @todo when implementing real validation logic, create separate validator class(es)
     * @param string $s
     */
    private function validateInput(string $s)
    {
        if (!preg_match("/^[A-Z]+$/", $s)) {
            throw new \UnexpectedValueException("Invalid SKU string: '$s''");
        }
    }
}
