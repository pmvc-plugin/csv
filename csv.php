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

    public function get(
        $file,
        $charset=null,
        $ignore=null
    )
    {
       $content = file_get_contents($file);
       return $this->read($content, $charset, $ignore);
    }

    public function read(
        $content,
        $charset=null,
        $ignore=null
    )
    {
       $csv = new CsvReader(); 
       if (isset($this['col'])) {
            $csv->setColumn($this['col']);
       }
       $csv->read($content, $charset, $ignore);
       $data = [];
       foreach ($csv as $v) {
            $data[] = $v;
       }
       return $data;
    }
}
