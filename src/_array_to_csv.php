<?php
namespace PMVC\PlugIn\csv;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\ArrayToCSV';

class ArrayToCSV
{
    function __invoke(array $arr, array $headers = null)
    {
        if (is_null($headers)) {
            $headers = array_keys(reset($arr));
        }
        $result = $this->_getRow($headers);
        foreach ($arr as $k=>$v) {
            $result.=$this->_getRow($v);
        }
        return $result;
    }

    private function _getRow(array $arr)
    {
        $arr = array_map([$this,'_quote'], $arr);
        $s = join(',', $arr). "\n";
        return $s;
    }

    private function _quote ($s)
    {
        $s = trim($s);
        $s = '"'.str_replace('"', '""', $s).'"';
	return $s;
    }
}
