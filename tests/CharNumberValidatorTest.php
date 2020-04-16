<?php

namespace Sportradar\BankOCR\Tests;

use PHPUnit\Framework\TestCase;
use Sportradar\BankOCR\CharNumberValidator;

class CharNumberValidatorTest extends TestCase {

    /** @test */
    public function should_multiply_every_digit_with_position() {
        $accountDigits = [3, 4, 5, 8, 8, 2, 8, 6, 5];

        $validAccountNumber = CharNumberValidator::checkForValidAccountNumber($accountDigits);

        $this->assertTrue($validAccountNumber);
    }

    /*** @test */
    public function should_return_true_if_contains_given_string() {
        $accountDigits = [3, "?", 5, 8, 8, 2, "?", 6, 5];

        $hasStringValue = CharNumberValidator::checkContainString($accountDigits, "?");

        $this->assertFalse($hasStringValue);
    }
}
