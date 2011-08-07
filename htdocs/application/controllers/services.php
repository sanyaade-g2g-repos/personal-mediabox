<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Services extends CI_Controller {
	
	/*
	 * Updates the library index.
	 */
	function rebuildindex() {
		
	}
	
	/**
	 * Importiert neue Dateien aus dem import Folder.
	 */
	public function import() {
		$this->load->library('Videoimport');
		$this->config->load('videogallery');
		$import_path = $this->config->item('default_import_path');
		$this->videoimport->importFolder($import_path);
		
		$this->load->view('services_import', array('Results' => $this->videoimport->getResults()));		
	}
	
	public function upgradeIndex($version) {
		$this->load->library('Videoindex');
		if ($version == 'v2'){
			$this->videoindex->upgrade(new IndexItemUpgrade_v2());
		}
		$this->videoindex->update();
	}
}