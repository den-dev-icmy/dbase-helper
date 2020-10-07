<?php

namespace dBaseHelper;

/**
 * @author Denis Bushaev aka Dez64ru <dez64ru@gmail.com>
 * Class dBaseHelper
 */
class Handler
{
    const RETURN_INDEXED = 0;
    const RETURN_ASSOC = 1;

    private $encoding = 'UTF-8';

    private $cur = 1;
    private $file;

    public function open(string $path, int $type = DBASE_RDWR, string $encoding = 'UTF-8')
    {

    }

    public function close()
    {
    }

    public function __toArray()
    {
    }

    public function __fromArray()
    {
    }

    public function next()
    {
    }

    public function setCursor()
    {
    }

    public function resetCursor()
    {
    }

}