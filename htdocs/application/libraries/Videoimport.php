<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Videoimport {
	
	public $ci;
	public $lib_basepath;
	public $stats;
	public $results;
	
	public function __construct(array $params=array()) {
		$this->ci =& get_instance();
		$this->ci->load->helper('file');
		$this->ci->load->library('Imdbapi');
		$this->ci->load->library('Mediainfo');
		$this->ci->load->library('Videoindex');
		$this->ci->config->load('videogallery');
		$this->lib_basepath = $this->ci->config->item('video_path_r');
		$this->results = array();
	}
	
	public function getResults() {
		return implode('', $this->results);		
	}
	
	public function writeResult($result) {
		$this->results[] = $result;
	}
	
	public function importFolder($folderPath) {
		$this->writeResult("<h3>importFolder(\"$folderPath\")</h3>");
		// get the real folder path
		$folder_path = realpath($folderPath);
		$this->writeResult("library path: $this->lib_basepath<br />");
		$this->writeResult("real import path: $folder_path<br />");
		// init stats
		$this->stats = array();
		$this->stats['files_imported'] =  
		$this->stats['files_skipped'] = 0;
		// import all items in the folder
		$this->_importFolderRecursive($folder_path);
		// persist changes in the index
		$this->ci->videoindex->update();
		$item_count = $this->ci->videoindex->itemCount();
		$this->writeResult("index file updated. the library contains now $item_count items.<br />");
		$this->writeResult("import done. {$this->stats['files_imported']} files imported, {$this->stats['files_skipped']} skipped.");
	}
	
	private function _importFolderRecursive($folderPath) {
		$items = array_slice(scandir($folderPath),2);
		foreach ($items as $item) {
			$item_path = $folderPath.'/'.$item;
			if (is_dir($item_path)) {
				$this->writeResult("browsing directory: $item_path<br />");
				$this->_importFolderRecursive($item_path);
			}
			else {
				$this->writeResult("importing file: $item_path<br />");
				// get media info to check whether item is a valid movie
				$media_info = $this->ci->mediainfo->getFileInfo($item_path);
				if (empty($media_info)) {
					// skip file
					$this->writeResult("no media info available. file skipped.<br />");
					$this->stats['files_skipped']++;
					continue;
				}
				if (false) {
				//if (!preg_match('@^MPEG-4@i', $media_info['Video']['Format'])) {
				//if ($media_info['General']['Format'] != 'MPEG-4') {
					// skip file
					$this->writeResult("file is not a MPEG-4 file. file skipped.<br />");
					$this->stats['files_skipped']++;
					continue;
				}
				// build the required names and paths
				$path_info = pathinfo($item_path);
				$filename = $path_info['basename'];
				$library_path = $this->lib_basepath.$filename;
				// extract the basename (f.e. Inception for a file called 'Inception.m4v')
				$title = $path_info['filename'];
				$this->writeResult("assuming that the movie is called '$title'<br />");
				// try to get imdb info from the filename
				$this->writeResult("querying imdb for '$title'..."); 
				$imdb_info = $this->ci->imdbapi->byTitle($title);
				if (empty($imdb_info)) {
					$this->writeResult("no results for '$title'<br />");
				}
				else {
					$this->writeResult("query success<br />");
				}
				// import movie into library
				// if the file already exists in the library then skip file
				$this->writeResult("adding file to library...<br />");
				if (file_exists($library_path)) { // exists in the library?
					// skip file
					$this->writeResult("$library_path already exists. file skipped.<br />");
					$this->stats['files_skipped']++;
					continue;
				}
				rename($item_path, $library_path); // move file to library
				if (file_exists($library_path)) {
					$this->writeResult("import successful. filepath is now: $library_path<br />");
				}
				else {
					$this->writeResult("import failed.<br />");
				}
				// add file to index
				$this->ci->videoindex->addItem($title, $filename, $media_info, $imdb_info);
				$this->writeResult("file added to index<br />");
				// update statistics
				$this->stats['files_imported']++;
			} /* ! is_dir($item_path) */
			flush();
		} /* foreach ($items as $item) */
	}
}