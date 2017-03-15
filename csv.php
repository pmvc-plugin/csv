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

    public function get() {
        $args = func_get_args();
        if (is_file($args[0])) {
            $args[0] = file_get_contents($args[0]);
        }
        return call_user_func_array(
            [
                $this,
                '_read'
            ],
            $args
        );
    }

    /**
     * @params int $startRow start from zero
     */
    private function _read(
        $content,
        $charset=null,
        $startZeroRow=null,
        $callback=null
    ) {
    
        $csv = new CsvReader(); 
        if (isset($this['col'])) {
            $csv->setColumn($this['col']);
        }
        $csv->read($content, $charset, $startZeroRow);
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
