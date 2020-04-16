<?php

namespace Sportradar\BankOCR;

class CharNumberValidator {

    private const POSITIONS = [9, 8, 7, 6, 5, 4, 3, 2, 1];

    public static function writerValidator(array $accountArray): string {
        if (self::checkContainString($accountArray, "?") && self::checkContainString($accountArray, " AMB")) {
            $stringTag = " ERR";
        } elseif (self::checkContainString($accountArray, " AMB" || self::checkForValidAccountNumber($accountArray))) {
            $stringTag = "";
        } else {
            $stringTag = " ILL";
        }
        return $stringTag;
    }

    public static function checkForValidAccountNumber(array $accountArray): bool {
        $resultDigits = self::countPositions($accountArray);
        if (array_sum($resultDigits) % 11 !== 0) {
            return false;
        }
        return true;
    }

    private static function countPositions(array $accountArray): array {
        $resultDigits = [];
        foreach ($accountArray as $key => $digit) {
            $resultDigits[] += $digit * self::POSITIONS[$key];
        }
        return $resultDigits;
    }

    public static function checkContainString(array $accountArray, string $needle): bool {
        if (!in_array($needle, $accountArray)) {
            return false;
        }
        return true;
    }
}