<?php
namespace PMVC\PlugIn\csv;

\PMVC\l(__DIR__.'/src/CsvReader.php');

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\csv';

class csv extends \PMVC\PlugIn
{
    public function get($file)
    {
       ini_set('auto_detect_line_endings',TRUE);
       $csv = new CsvReader(); 
       $csv->open($file);
       $data = array();
       foreach ($csv as $v) {
            $data[] = $v;
       }
       $csv->close();
       return $data;
    }
}
