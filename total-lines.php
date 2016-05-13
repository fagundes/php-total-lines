<?php

if ($argc < 2) {
    file_put_contents('php://stderr', 'Usage: total-lines <path-1> [<path-2> ... <path-n>]' . PHP_EOL);
    exit(1);
}

function count_lines(SplFileObject $fileObject)
{
    $fileObject->seek($fileObject->getSize());

    return $fileObject->key()+1;
}

$directories = array();

for ($i = 1; $i < $argc; ++$i) {

    if (!file_exists($argv[$i])) {
        file_put_contents('php://stderr', sprintf('File or directory "%s" doesn\'t exist', $argv[$i]) . PHP_EOL);
        exit(1);
    }

    $directories[] = $argv[$i];
}

$linesTotal = 0;

foreach ($directories as $path) {
    if (is_dir($path)) {
        $it  = new RecursiveDirectoryIterator($path);
        $it2 = new RecursiveIteratorIterator($it);

        /**
         * @var SplFileInfo $fileInfo
         */
        foreach ($it2 as $fileInfo) {
            if (!$fileInfo->isDir()) {
                $linesTotal += count_lines($fileInfo->openFile());
            }
        }
    }
    else {
        $fileInfo = new SplFileInfo($path);
        $linesTotal += count_lines($fileInfo->openFile());
    }
}

echo $linesTotal . PHP_EOL;
