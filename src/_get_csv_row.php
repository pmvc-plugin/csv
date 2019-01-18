<?php

namespace PMVC\PlugIn\csv;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\GetCsvRow';

class GetCsvRow
{
    public function __invoke(array $cols)
    {
        $cols = array_map([$this,'_quote'], $cols);
        $s = join(',', $cols). "\n";
        return $s;
    }

    private function _quote ($s)
    {
        $s = trim($s);
        $s = '"'.str_replace('"', '""', $s).'"';
	return $s;
    }
}
