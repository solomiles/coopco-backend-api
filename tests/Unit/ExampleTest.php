<?php

namespace Tests\Unit;

use App\Traits\CsvTrait;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    use CsvTrait;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_true_is_true()
    {
        $this->assertTrue($this->setCSVStructure(['Name', 'Email', 'Number'], 'wisdom-cooperative'));
    }
}
