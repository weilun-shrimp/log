<?php

namespace Weilun\Log;

use Exception;

class Log
{
    protected string $folder = '';

    /**
     * Reference : https://www.php.net/manual/en/function.mkdir.php
     * use for create folder permission
     */
    protected int $permission = 0777;

    protected string $file_name = 'log.txt';

    protected string $mode = 'a';

    protected $opened_file = null;

    public function open()
    {
        if ($this->opened_file) return $this;
        //check dir first and create it if not exist
        if (!is_dir($this->folder)) if (!mkdir($this->folder, $this->permission, true)) 
            throw new \Exception(__CLASS__ . ' ' . __FUNCTION__ . " system can't create dir from $this->folder");
        $this->opened_file = fopen("$this->folder/$this->file_name", $this->mode);
        if ($this->opened_file === false) throw new \Exception(__CLASS__ . ' ' . __FUNCTION__ . " system can't open such the file $this->file_name in $this->folder");
        return $this;
    }

    public function getOpenedFile()
    {
        return $this->opened_file;
    }

    public function setFolder(string $v)
    {
        if ($v == $this->folder) return $this;
        $this->folder = $v;
        $this->close(); // flush opened resource
        return $this;
    }

    public function appendFolder(string $v)
    {
        if (!$v) return $this;
        $this->folder .= $v;
        $this->close(); // flush opened resource
        return $this;
    }

    public function getFolder()
    {
        return $this->folder;
    }

    public function setFileName(string $v)
    {
        if (!$v or $v == $this->file_name) return $this;
        $this->file_name = $v;
        $this->close(); // flush opened resource
        return $this;
    }

    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Reference: https://www.php.net/manual/zh/function.fopen.php
     * @throws Exception
     */
    public function setMode(string $v)
    {
        if ($v == $this->mode) return $this;
        if (!isset([
            'r' => null, 'w' => null, 'a' => null, 'x' => null, 'c' => null, 'r+' => null, 
            'w+' => null, 'a+' => null, 'x+' => null, 'c+' => null, 'e' => null
        ][$v])) throw new \Exception(__CLASS__ . ' ' . __FUNCTION__ . ' invalid mode string.');
        $this->mode = $v;
        $this->close(); // flush opened resource
        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function write(string $v)
    {
        $this->open();
        if (!fwrite($this->opened_file, date('Y-m-d H:i:s') . " $v " . PHP_EOL)) {
            $this->close();
            throw new \Exception(__CLASS__ . ' ' . __FUNCTION__ . " system can't write the content \"$v\" in the such the file $this->file_name in $this->folder");
        }
        return $this;
    }

    public function writeOnce(string $v)
    {
        $this->write($v);
        $this->close();
        return $this;
    }

    public function close()
    {
        if (!$this->opened_file) return $this;
        if (!fclose($this->opened_file)) throw new \Exception(__CLASS__ . ' ' . __FUNCTION__ . " system can't close such the file $this->file_name in $this->folder");
        $this->opened_file = null; // flush opened resource
        return $this;
    }

    public function __destruct()
    {
        $this->close();
    }
}