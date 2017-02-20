<?php
namespace PMVC\PlugIn\csv;

\PMVC\l(__DIR__.'/src/CsvReader.php');

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\csv';

class csv extends \PMVC\PlugIn
{
    public function init()
    {
        ini_set('auto_detect_line_endings', true);
    }

    public function get(
        $file,
        $charset=null,
        $ignore=null
    ) {
    
        $content = file_get_contents($file);
        return $this->read($content, $charset, $ignore);
    }

    public function read(
        $content,
        $charset=null,
        $ignore=null,
        $callback=null
    ) {
    
        $csv = new CsvReader(); 
        if (isset($this['col'])) {
            $csv->setColumn($this['col']);
        }
        $csv->read($content, $charset, $ignore);
        $data = [];
        foreach ($csv as $v) {
            if (is_callable($callback)) {
                $k = null;
                $continue = call_user_func_array($callback,[&$v, &$k]);
                if (is_null($v)) {
                    continue;
                }
                if ($k) {
                    $data[$k] = $v;
                } else {
                    $data[] = $v;
                }
                if (!$continue) {
                    break;
                }
            } else {
                $data[] = $v;
            }
        }
        return $data;
    }
}
