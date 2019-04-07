<?php

namespace Checkout\Tests\Unit\Service;

use Checkout\Service\Scanner;
use PHPUnit\Framework\TestCase;

class ScannerTest extends TestCase
{
    /** @var Scanner */
    private $scanner;

    protected function setUp()
    {
        parent::setUp();
        $this->scanner = new Scanner();
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testScanSkusWithInvalidStringWillTrow()
    {
        $invalidSkus = '12345!@#$%';
        $this->scanner->scanSkus($invalidSkus);
    }

    public function testScanSkusWithEmptyStringWillReturnEmpty()
    {
        $exp = [];
        $act = $this->scanner->scanSkus('');
        $this->assertSame($exp, $act);
    }

    public function testScanSkusWithValidStringWillReturnExpected()
    {
        $valid = 'A';
        $exp = [$valid];
        $act = $this->scanner->scanSkus($valid);
        $this->assertSame($exp, $act);
    }

    public function testScanSkusWithMultipleSkusWillReturnExpected()
    {
        $valid = 'AABCZ';
        $exp = ['A', 'A', 'B', 'C', 'Z'];
        $act = $this->scanner->scanSkus($valid);
        $this->assertSame($exp, $act);
    }
}
