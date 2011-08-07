<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mediainfo {
	
	public $ci;
	public $mediainfo_exec;
	
	function __construct(array $params = array()) {
		$this->ci =& get_instance();
		$this->ci->config->load('mediainfo');
	}
	
	public function getFileInfo($filepath,$format='array') {
		
		$mediainfo_exec = $this->ci->config->item('mediainfo_exec_path');

		if (empty($format)) $format = 'array';
		
		if ($format=='html') {
			$result = shell_exec("\"$mediainfo_exec\" \"$filepath\" --Output=HTML");
		}
		else if ($format=='array') {
			$shellresult = shell_exec("\"$mediainfo_exec\" \"$filepath\"");
			// parse the results
			$result = array();
			$section = '';
			foreach (explode("\n",$shellresult) as $line) {
				if (preg_match('@^\w+$@i',$line)) {
					// section
					$section = $line;
					$result[$section] = array();
				}
				else if (preg_match('@^(.*?)\s*:\s(.*)$@i',$line,$matches)) {
					// value in the current section
					$result[$section][$matches[1]] = $matches[2];
				}
				else {
					// skip line
				}
			}
		}
		else {
			throw new Exception('argument exception: invalid format');
		}
		return $result;
	}
	
}