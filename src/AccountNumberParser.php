<?php

namespace Sportradar\BankOCR;

class AccountNumberParser {

    public function getNumbersByTag(array $numberArray, string $keyword): array {
        $resultArray = [];
        foreach ($numberArray as $number) {
            if (strpos($number, $keyword) !== FALSE)
                $resultArray[] = $number;
        }
        return $resultArray;
    }

    public function removeTag(array $numberArray, string $keyword): array {
        $resultArray = [];
        foreach ($numberArray as $number) {
            $resultArray[] = str_replace($keyword, "", $number);
        }
        return $resultArray;
    }

    public function createVariations(array $numberArray): array {
        $resultArray = [];
        $occurrence = [];
        foreach ($numberArray as $haystack) {
            foreach (VariationNumbers::VARIATION_NUMBERS as $needle => $replaceNumbers) {
                $occurrence[] = substr_count($haystack, $needle);
                foreach ($replaceNumbers as $replace) {
                    $resultArray[] = $this->replaceFirstOccurrence($needle, $replace, $haystack);
                }
            }
        }
        if ($occurrence[0] === 1) {
            return $resultArray;
        }
        return $this->createVariations($resultArray);
    }

    function replaceFirstOccurrence(string $needle, string $replace, string $haystack): string {
        if (($position = strpos($haystack, $needle)) === false)
            return $haystack;

        return substr_replace($haystack, $replace, $position, strlen($needle));
    }

    public function createStringMultiArray(array $arrayOfStrings): array {
        $stringMultiArray = [];
        foreach ($arrayOfStrings as $string) {
            $stringMultiArray[] = str_split($string);
        }
        return $stringMultiArray;
    }

    public function createDigitMultiArray(array $stringMultiArray): array {
        $resultMultiArray = [];
        foreach ($stringMultiArray as $stringArray) {
            foreach ($stringArray as $string) {
                $resultMultiArray[] = intval($string);
            }
        }
        return array_chunk($resultMultiArray, 9);
    }

    public function getOnlyValidNumber(array $multiArrayOfDigits): array {
        $resultMultiArray = [];
        foreach ($multiArrayOfDigits as $arrayOfDigits){
            if (CharNumberValidator::checkForValidAccountNumber($arrayOfDigits)){
                $resultMultiArray[] = $arrayOfDigits;
            }
        }
        return  $resultMultiArray;
    }

    public function addAMBTag(array $multiArrayOfDigits): array {
        $resultMultiArray = [];
        foreach ($multiArrayOfDigits as $arrayOfDigits){
            if (count($multiArrayOfDigits) > 1){
                array_push($arrayOfDigits, " AMB");
                $resultMultiArray[] = $arrayOfDigits;
            }
            else{
                $resultMultiArray[] = $arrayOfDigits;
            }
        }
        return  $resultMultiArray;
    }
}