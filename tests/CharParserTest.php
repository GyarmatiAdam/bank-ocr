<?php

namespace Sportradar\BankOCR\Tests;

use PHPUnit\Framework\TestCase;
use Sportradar\BankOCR\CharNumberParser;
use Sportradar\BankOCR\FileReader;
use Sportradar\BankOCR\exceptions\InvalidFileException;

class CharParserTest extends TestCase {

    private object $charNumber;
    private object $fileReader;

    protected function setUp(): void {
        parent::setUp();
        $this->charNumber = new CharNumberParser();
        $this->fileReader = new FileReader();
    }

    /**
     * @throws InvalidFileException
     * @test
     */
    public function should_get_3_char_long_strings_as_array() {
        $oneLine = [
                        "    _  _     _  _  _  _  _",
                        "  | _| _||_||_ |_   ||_||_|",
                        "  ||_  _|  | _||_|  ||_| _|"
                    ];

        $fileArray = $this->fileReader->fileReader("src/docs/number-123456789.txt");

        $this->assertSame($oneLine, $fileArray);

        return $fileArray;
    }

    /**
     * @param array $fileArray
     * @depends should_get_3_char_long_strings_as_array
     * @return array
     * @test
     */
    public function should_get_3_lines_into_multi_array(array $fileArray) {
        $threeLine = [
            [
                ["   "," _ "," _ ","   "," _ "," _ "," _ "," _ "," _ "],
                ["  |"," _|"," _|","|_|","|_ ","|_ ","  |","|_|","|_|"],
                ["  |","|_ "," _|","  |"," _|","|_|","  |","|_|"," _|"]
            ]
        ];

        $getFileArray = $this->charNumber->getFileArray($fileArray);
        $chunks = $this->charNumber->breakIntoChunks($getFileArray);

        $this->assertSame($threeLine, $chunks);

        return $chunks;
    }

    /**
     * @param array $chunks
     * @depends should_get_3_lines_into_multi_array
     * @test
     */
    public function should_return_one_line_file_in_digits(array $chunks) {
        $arrayOfDigits = [
            [1,2,3,4,5,6,7,8,9]
        ];

        $charNumbers = $this->charNumber->generateCharNumbers($chunks);
        $getCharNumber = $this->charNumber->getCharNumbers($charNumbers);
        $digits = $this->charNumber->transformCharsIntoDigits($getCharNumber);

        $this->assertSame($arrayOfDigits, $digits);
    }

    /**
     * @throws InvalidFileException
     * @test
     */
    public function should_return_multiple_line_file_in_digits () {
        $multiArrayOfDigits = [
            [0,0,0,0,0,0,0,0,0],
            [1,1,1,1,1,1,1,1,1],
            [2,2,2,2,2,2,2,2,2],
            [3,3,3,3,3,3,3,3,3],
            [4,4,4,4,4,4,4,4,4],
            [5,5,5,5,5,5,5,5,5],
            [6,6,6,6,6,6,6,6,6],
            [7,7,7,7,7,7,7,7,7],
            [8,8,8,8,8,8,8,8,8],
            [9,9,9,9,9,9,9,9,9]
        ];

        $file = $this->fileReader->fileReader("src/docs/number-multi.txt");
        $fileArray = $this->charNumber->getFileArray($file);
        $chunks = $this->charNumber->breakIntoChunks($fileArray);
        $charNumbers = $this->charNumber->generateCharNumbers($chunks);
        $getCharNumber = $this->charNumber->getCharNumbers($charNumbers);
        $digits = $this->charNumber->transformCharsIntoDigits($getCharNumber);

        $this->assertSame($multiArrayOfDigits, $digits);
    }

    /**
     * @throws InvalidFileException
     * @test
     */
    public function should_return_wrong_digits_as_question_marks() {
        $multiArrayOfDigits = [
            [1, "?", 3, 4, 5, 6, 7, 8, "?"]
        ];

        $file = $this->fileReader->fileReader("src/docs/wrong-numbers.txt");
        $fileArray = $this->charNumber->getFileArray($file);
        $chunks = $this->charNumber->breakIntoChunks($fileArray);
        $charNumbers = $this->charNumber->generateCharNumbers($chunks);
        $getCharNumber = $this->charNumber->getCharNumbers($charNumbers);
        $remainCharNumber = $this->charNumber->deleteFalseCharNumber($getCharNumber);
        $digits = $this->charNumber->transformCharsIntoDigits($remainCharNumber);

        $this->assertSame($multiArrayOfDigits, $digits);
    }
}
