<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Imdbapi {
	
	public $ci;
	
	public function __construct(array $param =array()) {
		$this->ci =& get_instance();
		$this->ci->config->load('imdbapi');
	}
	
	public function byTitle($title) {
		$response = file_get_contents('http://www.imdbapi.com/?'.http_build_query(array('t'=>$title)));
		$imdbData = json_decode($response, true);
		if ($imdbData['Response'] != 'True') {
			//show_error('IMDb API result was: '.$imdbData['Response']);
			return array();
		}
		return $imdbData;
	}
	
	public function byId($id) {
		$response = file_get_contents('http://www.imdbapi.com/?'.http_build_query(array('i'=>$id)));
		$imdbData = json_decode($response, true);
		if ($imdbData['Response'] != 'True') {
			show_error('IMDb API result was: '.$imdbData['Response']);
			return array();
		}
		return $imdbData;
	}
	
	/**
	 * download the movie poster from IMDb
	 * do not send a referrer to prevent the request from being declined
	 */
	public function getPoster($imdbInfo, $destFile) {
		if (empty($imdbInfo)) {
			return false;
		}
		$preview_img_data = file_get_contents($imdbInfo['Poster']);
		file_put_contents($destFile, $preview_img_data);
		return true;
	}
	
	/**
	 * Gets the url to the specified title ID on imdb.com 
	 */
	public function makeUrl($id) {
		return str_replace('{id}',$id, $this->ci->config->item('imdb_movielink_tpl'));
	}
	
}