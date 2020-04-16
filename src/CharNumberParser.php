<?php

namespace Sportradar\BankOCR;

class CharNumberParser {

    public function getFileArray(array $fileArray): array {
        $fileArraySetLength = [];
        foreach ($fileArray as $fileString) {
            $fileArraySetLength[] = $this->setLineLength($fileString);
        }
        return array_chunk($fileArraySetLength, 4);
    }

    private function setLineLength(string $fileString): string {
        if (strlen($fileString) === 0) {
            $fileString = substr_replace($fileString, str_repeat(" ", 27), 0, 27);
        } elseif (strlen($fileString) === 26) {
            $fileString = substr_replace($fileString, " ", 26, 1);
        }
        return $fileString;
    }

    private function removeLine(array $fileMultiArray): array {
        $fileTwoMultiArray = [];
        foreach ($fileMultiArray as $fileArray) {
            if (array_key_last($fileArray) === 3) {
                array_pop($fileArray);
                $fileTwoMultiArray[] = $fileArray;
            } else {
                $fileTwoMultiArray[] = $fileArray;
            }
        }
        return $fileTwoMultiArray;
    }

    public function breakIntoChunks(array $fileMultiArray): array {
        $fileMultiArray = $this->removeLine($fileMultiArray);
        $resultArray = [];
        $resultArray = $this->getChunkArray($fileMultiArray, $resultArray);
        return array_chunk($resultArray, 3);
    }

    public function getChunkArray(array $fileMultiArray, array $resultArray): array {
        foreach ($fileMultiArray as $fileArray) {
            foreach ($fileArray as $fileString) {
                $resultArray[] = str_split($fileString, 3);
            }
        }
        return $resultArray;
    }

    public function generateCharNumbers(array $fileMultiArray): array {
        $charNumberArray = [];
        foreach ($fileMultiArray as $fileArray) {
            for ($key = 0; $key <= count(reset($fileArray)) - 1; $key++) {
                $charNumberArray[] = array_column($fileArray, $key);
            }
        }
        return array_chunk($charNumberArray, count(reset($fileArray)));
    }

    public function getCharNumbers(array $charTwoMultiArray): array {
        $resultArray = [];
        foreach ($charTwoMultiArray as $charMultiArray) {
            foreach ($charMultiArray as $charArray) {
                $resultArray[] = $charArray;
            }
        }
        return $resultArray;
    }

    public function deleteFalseCharNumber(array $charMultiArray): array {
        $resultMultiArray =[];
        foreach ($charMultiArray as $charArray) {
            $resultArray = [];
            foreach (CharNumbers::SAMPLE_NUMBERS as $sampleArray) {
                if($sampleArray === $charArray){
                    $resultArray = $charArray;
                }
            }$resultMultiArray[] = $resultArray;
        }return $resultMultiArray;
    }

    public function transformCharsIntoDigits(array $charMultiArray): array {
        $digitsInArray = [];
        foreach ($charMultiArray as $charArray) {
            foreach (CharNumbers::SAMPLE_NUMBERS as $digit => $sampleArray) {
                if ($sampleArray === $charArray) {
                    $digitsInArray[] = $digit;
                }
            }
        }
        return array_chunk($digitsInArray, 9);
    }
}