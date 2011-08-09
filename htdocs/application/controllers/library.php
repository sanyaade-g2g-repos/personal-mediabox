<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Library extends CI_Controller {
	
	public function __construct(array $params = array()) {
		parent::__construct($params);
	}
	
	public function listlibrary() {
		$this->load->library('Videoindex');
		$items = $this->videoindex->getItems('/Index/Item');
		$groupBy = $this->input->post('groupby','title');
		
		usort($items, array('Library', '_sortby_title_asc'));
		
		$method = '_groupby_'.$groupBy;
		if (method_exists($this, $method)) {
			$groups = $this->$method($items);
		}
		else {
			$groups = $this->_groupby_title($items);
		}
		
		$this->load->view('library_listlibrary', array(
			'Groups' => $groups
			,'Title' => $this->_getTitle($groupBy)
			,'SortControlId' => 'sortlibrary'
		));
	}
	
	private function _getTitle($groupBy) {
		if ($groupBy == 'artist')
			return 'Alle Filme nach Schauspielern';
		else if ($groupBy == 'tags')
			return 'Alle Filme nach Tags';
		else if ($groupBy == 'year')
			return 'Alle Filme nach Jahren';
		else if ($groupBy == 'title')
			return 'Alle Filme nach Titel';
		else
			return 'Alle Filme';
	}
	
	private function _groupby_artist($items) {
		$groups = array();
		foreach ($items as $item) {
			$artists = $item['Artists'];
			if (count($artists) > 0) {
				for ($i = 0; $i < $artists->count(); $i++) {
					$group = strval($artists->Artist[$i]);
					// Prüfen ob die Gruppierung bereits existiert
					if (!isset($groups[$group]))
					{
						$groups[$group] = array();
					}
					$groups[$group][] = $item;
				}
			}
			else {
				$groups['n/a'][] = $item;
			}
		}
		ksort($groups);
		return $groups;
	}

	private function _groupby_tags($items) {
		$groups = array();
		foreach ($items as $item) {
			$tags = $item['Tags'];
			if (count($tags) > 0) {
				for ($i = 0; $i < $tags->count(); $i++) {
					$group = strval($tags->Tag[$i]);
					// Prüfen ob die Gruppierung bereits existiert
					if (!isset($groups[$group]))
					{
						$groups[$group] = array();
					}
					$groups[$group][] = $item;
				}
			}
			else {
				$groups['n/a'][] = $item;
			}
		}
		ksort($groups);
		return $groups;
	}
	
	private function _groupby_title($items) {
		$groups = array();
		foreach ($items as $item) {
			$group = strtoupper(substr($item['Title'],0,1));
			// Prüfen ob die Gruppierung bereits existiert
			if (!isset($groups[$group]))
			{
				$groups[$group] = array();
			}
			$groups[$group][] = $item;
		}
		ksort($groups);
		return $groups;
	}
	
	private function _groupby_year($items) {
		$groups = array();
		foreach ($items as $item) {
			$group = intval($item['Year']);
			if ($group == 0) {
				$group = 'n/a';
			}
			// Prüfen ob die Gruppierung bereits existiert
			if (!isset($groups[$group]))
			{
				$groups[$group] = array();
			}
			$groups[$group][] = $item;
		}
		ksort($groups);
		return $groups;
	}
	
	static function _sortby_title_asc($a, $b) {
		return strcasecmp($a['Title'],$b['Title']);
	}

}