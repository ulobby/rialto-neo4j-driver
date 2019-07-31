<?php
namespace App;

use Nesk\Rialto\AbstractEntryPoint;

class FileSystem extends AbstractEntryPoint
{
    public function __construct()
    {
        parent::__construct(__DIR__.'/FileSystemConnectionDelegate.js');
    }
}