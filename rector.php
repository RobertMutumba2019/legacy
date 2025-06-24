<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    // Directories to scan and refactor
    $rectorConfig->paths([
        __DIR__ . '/ajax',  
        __DIR__ . '/classes',  
        __DIR__ . '/DataTables',
         __DIR__ . '/import file',
         __DIR__ . '/PHPMailer-master',
          __DIR__ . '/storeRequisitions',
         __DIR__ . '/classes',
          __DIR__ . '/index.php',
         __DIR__ . '/Admin_functioncalls.php',

     
        
    ]);

    // Apply sets of refactoring rules
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PHP_82,
    ]);
};
