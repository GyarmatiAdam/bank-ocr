<?php

namespace Sportradar\BankOCR;

use Sportradar\BankOCR\exceptions\InvalidFileException;

class FileReader {

    /**
     * @param $file
     * @return array
     * @throws InvalidFileException
     */
    public function fileReader($file): array {
        if (filesize($file) === 0) {
            throw new InvalidFileException("This file is empty or does not exists");
        } else {
            return file($file, FILE_IGNORE_NEW_LINES);
        }
    }
}