<?php

namespace Sportradar\BankOCR;

use Sportradar\BankOCR\exceptions\InvalidFileException;

class CharNumberWriter {

    /**
     * @param $accountMultiArray
     * @param $fileToWrite
     * @throws InvalidFileException
     */
    public function validatedFileWriter(array $accountMultiArray, $fileToWrite): void {
        foreach ($accountMultiArray as $accountArray) {
            if (file_exists($fileToWrite)) {
                $file = fopen($fileToWrite, "a");
                $stringTag = CharNumberValidator::writerValidator($accountArray);
                fwrite($file, implode("", $accountArray) . $stringTag . "\n");
                fclose($file);
            } else {
                throw new InvalidFileException("This file does not exists");
            }

        }
    }
}