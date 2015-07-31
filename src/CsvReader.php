<?php
namespace PMVC\PlugIn\csv;

class CsvReader implements \Iterator
{
    public $BOF=false;
    public $EOF=false;
    public $CsvEOF = false;
    public $_values=array();
    public $column=array();
    public $charset;
    public $tmpf;
    public $fp;
    public $fSize;

    public function open($file, $charset='UTF-8', $column=true, $ignore=0)
    {
        $this->tmpf=tempnam('', 'csv');
        $this->charset = $charset;
        $this->fSize=filesize($file)+10;
        if ($this->charset!='UTF-8') {
            $data=file_get_contents($file);
            $data=mb_convert_encoding($data, 'UTF-8', $this->charset);
            $data=stripslashes($data);
            $fp=fopen($this->tmpf, 'w+');
            ftruncate($fp, 0);
            fputs($fp, $data, strlen($data));
            fclose($fp);
            unset($data);
        } else {
            copy($file, $this->tmpf);
        }
        if (!is_null($column)) {
            $this->setColumn($column);
        }
        $this->fp=fopen($this->tmpf, 'r');
        for ($i=0;$i<$ignore;$i++) {
            fgetcsv($this->fp, $this->fSize, ',');
        }
    }
    
    public function close()
    {
        fclose($this->fp);
        unlink($this->tmpf);
    }
    
    public function readCsvLine()
    {
        $data=fgetcsv($this->fp, $this->fSize, ',');
        if ($data) {
            $a = array();
            if (!$this->BOF && $this->column===true) {
                $this->setColumn($data);
                $this->BOF = true;
                $data = $this->readCsvLine();
                return $data;
            }
            if (!$this->BOF) {
                $this->BOF = true;
            }
            foreach ($this->column as $k=>$v) {
                $a[$v] = $this->changeCharset($data[$k]);
            }
            $this->_values[] = $a;
        } else {
            $this->CsvEOF = true;
        }
        return $data;
    }
    
    public function changeCharset(&$string)
    {
        if ($this->charset!='UTF-8') {
            $string=mb_convert_encoding($string, $this->charset, 'UTF-8');
        } 
        return $string;
    }

    public function setColumn($arr)
    {
        $this->column = $arr;
    }
/**
* php5 Iterator <!--- Start
*/
    public function current()
    {
        if (!$this->BOF && !$this->CsvEOF) {
            $this->readCsvLine();
        } else {
            $this->BOF = true;
        }
        $return = current($this->_values);
        return $return;
    }
    
    public function key()
    {
        return key($this->_values);
    }
    
    public function next()
    {
        if (!$this->CsvEOF) {
            $this->readCsvLine();
        }
        return next($this->_values);
    }
    
    public function rewind()
    {
        $this->BOF=false;
        $this->EOF=false;
        return reset($this->_values);
    }
    
    public function valid()
    {
        if (!$this->BOF && !$this->CsvEOF) {
            $this->readCsvLine();
            reset($this->_values);
        }
        if (empty($this->_values)) {
            return false;
        }
        if ($this->next()) {
            prev($this->_values);
            return true;
        } elseif (!$this->EOF) {
            end($this->_values);
            $this->EOF=true;
            return true;
        }
        return false;
    }

/**
* php5 Iterator End --->
*/
}
