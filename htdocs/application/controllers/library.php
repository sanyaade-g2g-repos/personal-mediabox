<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Library extends CI_Controller {
	
	public function __construct(array $params = array()) {
		parent::__construct($params);
	}
	
	public function listlibrary($sort='abc') {
		$this->load->library('Videoindex');
		$items = $this->videoindex->getItems('/Index/Item');
		
		$orderBy = $this->input->post('by','title');
		$orderDir = $this->input->post('dir', 'asc');
		
		$method = '_sortby_'.$orderBy.'_'.$orderDir;
		if (method_exists('Library', $method)) {
			usort($items, array('Library', $method));
		}
		else {
			usort($items, array('Library', '_sortby_title_asc'));
		}
		
		$this->load->view('library_listlibrary', array(
			'Items' => $items
		));
	}
	
	static function _sortby_title_asc($a, $b) {
		return strcasecmp($a['Title'],$b['Title']);
	}
	
	static function _sortby_title_desc($a, $b) {
		return strcasecmp($b['Title'],$a['Title']);
	}
	
	static function _sortby_year_asc($a, $b) {
		return strcasecmp($a['Year'],$b['Year']);
	}
	
	static function _sortby_year_desc($a, $b) {
		return strcasecmp($b['Year'],$a['Year']);
	}
}