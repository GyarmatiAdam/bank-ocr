<?php

namespace Sportradar\BankOCR\Tests;

use PHPUnit\Framework\TestCase;
use Sportradar\BankOCR\AccountNumberParser;
use Sportradar\BankOCR\FileReader;
use Sportradar\BankOCR\exceptions\InvalidFileException;

class AccountNumberParserTest extends TestCase {

    private object $fileReader;
    private object $numberParser;

    protected function setUp(): void {
        parent::setUp();
        $this->fileReader = new FileReader();
        $this->numberParser = new AccountNumberParser();
    }

    /**
     * @test
     * @throws InvalidFileException
     */
    public function should_return_account_numbers() {
        $accountNumbers = $this->fileReader->fileReader("src/docs/translated/account-numbers.txt");

        $this->assertNotEmpty($accountNumbers);

        return $accountNumbers;
    }

    /**
     * @depends should_return_account_numbers
     * @param $accountNumbers
     * @return array
     * @test
     */
    public function should_find_numbers_with_ERR_tag($accountNumbers) {
        $withERR = ["123456799  ERR", "123456788  ERR"];

        $onlyERR = $this->numberParser->getNumbersByTag($accountNumbers, "ERR");

        $this->assertSame($onlyERR, $withERR);

        return $withERR;
    }

    /**
     * @depends should_find_numbers_with_ERR_tag
     * @param array $withERR
     * @test
     */
    public function should_remove_ERR_tag(array $withERR) {
        $withoutERR = ["123456799", "123456788"];

        $onlyNumber = $this->numberParser->removeTag($withERR, "  ERR");

        $this->assertSame($withoutERR, $onlyNumber);
    }

    /** @test */
    public function should_have_more_than_10_results_if_replacing_one_question_mark() {
        $baseNumber = ["1234567?9"];
        $lessThanExpected = 10;

        $createVariations = $this->numberParser->createVariations($baseNumber);

        $this->assertGreaterThan($lessThanExpected, $createVariations);
    }

    /** @test */
    public function should_have_more_than_100_results_if_replacing_two_question_marks() {
        $baseNumber = ["1?34567?9"];
        $allVariations = 100;

        $createVariations = $this->numberParser->createVariations($baseNumber);

        $this->assertGreaterThan($allVariations, $createVariations);
    }

    /** @test */
    public function should_get_multi_dimensional_array_of_numbers() {
        $baseNumber = ["1234567?9"];

        $createVariations = $this->numberParser->createVariations($baseNumber);
        $multiArrayOfStrings = $this->numberParser->createStringMultiArray($createVariations);
        $multiArrayOfDigits = $this->numberParser->createDigitMultiArray($multiArrayOfStrings);

        $this->assertNotEmpty($multiArrayOfDigits);

        return $multiArrayOfDigits;
    }

    /**
     * @depends should_get_multi_dimensional_array_of_numbers
     * @param array $multiArrayOfDigits
     * @test
     */
    public function should_return_only_valid_account_number(array $multiArrayOfDigits) {
        $validAccountNumber = [
            [1,2,3,4,5,6,7,8,9]
        ];

        $returnedValid = $this->numberParser->getOnlyValidNumber($multiArrayOfDigits);

        $this->assertSame($validAccountNumber, $returnedValid);
    }

    /** @test */
    public function should_add_AMB_tag_if_more_than_one_result() {
        $validAccountNumber = [
            [1,2,3,4,5,6,7,8,9],
            [1,2,3,4,5,6,7,8,9]
        ];
        $withAMBTag = [
            [1,2,3,4,5,6,7,8,9," AMB"],
            [1,2,3,4,5,6,7,8,9," AMB"]
        ];

        $addAMBTag = $this->numberParser->addAMBTag($validAccountNumber);

        $this->assertSame($withAMBTag, $addAMBTag);
    }
}
