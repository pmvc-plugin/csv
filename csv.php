<?php
namespace PMVC\PlugIn\csv;

\PMVC\l(__DIR__.'/src/CsvReader.php');

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\csv';

class csv extends \PMVC\PlugIn
{
    public function init()
    {
       ini_set('auto_detect_line_endings',TRUE);
    }

    public function get($file)
    {
       $content = file_get_contents($file);
       return $this->read($content);
    }

    public function read($content)
    {
       $csv = new CsvReader(); 
       $csv->read($content);
       $data = [];
       foreach ($csv as $v) {
            $data[] = $v;
       }
       return $data;
    }
}
