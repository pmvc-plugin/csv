<?php
namespace PMVC\PlugIn\csv;

use Iterator;

class CsvReader implements Iterator
{
    public $BOF=false;
    public $EOF=false;
    public $CsvEOF = false;
    public $_values=array();
    public $column=array();
    public $charset;
    public $fp;
    public $fSize;

    public function read($data, $charset=null, $ignore=null)
    {
        if (is_null($charset)) {
            $charset='UTF-8';
        }
        if (is_null($ignore)) {
            $ignore = 0;
        }
        $this->charset = $charset;
        if ($this->charset!='UTF-8') {
            $data=mb_convert_encoding($data, 'UTF-8', $this->charset);
            $data=stripslashes($data);
        }
        $this->fSize = strlen($data)+10;
        $this->fp = tmpfile(); 
        fwrite($this->fp, $data, $this->fSize);
        fseek($this->fp, 0);
        for ($i=0;$i<$ignore;$i++) {
            //strip ignore row
            fgetcsv($this->fp, $this->fSize, ',');
        }
    }
    
    public function __destruct()
    {
        if (is_resource($this->fp)) {
            fclose($this->fp);
        }
    }
    
    public function readCsvLine()
    {
        $data=fgetcsv($this->fp, $this->fSize, ',');
        if ($data) {
            if (!$this->BOF && !$this->column) {
                $this->setColumn($data);
                $this->BOF = true;
                $data = fgetcsv($this->fp, $this->fSize, ',');
            }
            $a = [];
            foreach ($this->column as $k=>$v) {
                if (isset($data[$k])) {
                    $a[$v] = trim($data[$k]);
                }
            }
            $this->_values[] = $a;
        } else {
            $this->CsvEOF = true;
        }
        return $data;
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
        $this->readCsvLine();
        $this->BOF = true;
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
