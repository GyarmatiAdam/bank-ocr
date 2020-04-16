<?php

namespace Sportradar\BankOCR\Tests;

use Sportradar\BankOCR\AccountNumberParser;
use Sportradar\BankOCR\CharNumberValidator;
use Sportradar\BankOCR\CharNumberParser;
use PHPUnit\Framework\TestCase;
use Sportradar\BankOCR\CharNumberWriter;
use Sportradar\BankOCR\exceptions\InvalidFileException;
use Sportradar\BankOCR\FileReader;

class WriterTest extends TestCase {
    private object $charNumber;
    private object $numberValidator;
    private object $numberWriter;
    private object $fileReader;
    private object $numberParser;

    protected function setUp(): void {
        parent::setUp();
        $this->charNumber = new CharNumberParser();
        $this->numberValidator = new CharNumberValidator();
        $this->numberWriter = new CharNumberWriter();
        $this->fileReader = new FileReader();
        $this->numberParser = new AccountNumberParser();
    }

    /**
     * @throws InvalidFileException
     * @test
     */    public function should_throw_exception_on_empty_file() {
        $this->expectException(InvalidFileException::class);

        $this->fileReader->fileReader("src/docs/translated/empty.txt");
    }

    /**
     * @throws InvalidFileException
     * @test
     */
    public function should_write_file_with_different_type_of_tags() {
        $multiArrayOfDigits = [
            [0,3,0,6,3,0,8,0,0], //ERR
            [1,2,3,4,5,6,7,8,9],
            [2,2,2,"?",2,"?",2,2,2] //ILL
        ];

        $this->numberWriter->validatedFileWriter($multiArrayOfDigits, "src/docs/translated/multiple-account.txt");
        $overWriteFile = $this->fileReader->fileReader("src/docs/translated/multiple-account.txt");

        $this->assertNotEmpty($overWriteFile);

        return $overWriteFile;
    }

    /**
     * @depends should_write_file_with_different_type_of_tags
     * @param array $overWriteFile
     * @throws InvalidFileException
     * @test
     */
    public function should_write_AMB_tag_if_has_more_than_one_result(array $overWriteFile) {
        $validAccountNumber = [
            [1,2,3,4,5,6,7,8,9], //AMB
            [1,2,3,4,5,6,7,8,9]  //AMB
        ];

        $addAMBTag = $this->numberParser->addAMBTag($validAccountNumber);
        $this->numberWriter->validatedFileWriter($addAMBTag, "src/docs/translated/multiple-account.txt");

        $this->assertNotEmpty($overWriteFile);
    }
}
