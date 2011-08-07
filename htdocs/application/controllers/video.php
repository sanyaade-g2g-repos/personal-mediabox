<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Video extends CI_Controller {
	
	public function __construct(array $params = array()) {
		parent::__construct($params);
		$this->config->load('videogallery');
	} 
	
	/***
	 * watch a video
	 * @param v    name of the video to be streamed 
	 */
	public function watch() {
		
		$v = $this->input->get('v');
		
		$this->load->library('Videoindex');
		$video = $this->videoindex->getByTitle($v);
		if (empty($video)) {
			show_error($v.' does not exist in the library');
			return;
		}
		
		$this->load->view('video_watch', $video);
		
	}
	
	public function edit() {
		
		$v = $this->input->get('v');
		
		$this->load->library('Videoindex');
		$video = $this->videoindex->getByTitle($v);
		if (empty($video)) {
			show_error($v.' does not exist in the library');
			return;
		}
		
		$this->load->view('video/editform', $media_info);
	}
	
	public function getMediaInfo() {
		
		$v = $this->input->get('v');
		
		$this->load->library('Videoindex');
		$video = $this->videoindex->getByTitle($v);
		if (empty($video)) {
			show_error($v.' does not exist in the library');
			return;
		}
		
		// read extended information about the video using mediainfo
		$this->load->library('Mediainfo');
		$media_info = $this->mediainfo->getFileInfo($video['FilePath']);
		$media_info['Item'] = $video;
		$this->load->view('video_mediainfo', $media_info);
	}
	
	/***
	 * search videos and display a list of search results
	 * @param k    keyword to search for
	 * @param s    source to search in, valid values are 'index' and 'filesystem'  
	 */
	public function search() {
		
		$this->load->view('video_search');
		//$this->load->view('video_results');
	}
	
	/***
	 * performs a search and return results
	 */
	public function results() {
		// get the keyword
		$keyword = $this->input->post('k');
		
		// abort if no keyword given
		if (!$keyword) {
			show_error('no keyword given');
		}

		// search the index
		$this->load->library('Videoindex');
		$results = $this->videoindex->search($keyword);
		
		// return the search results
		foreach ($results as $resultNode) {
			//$data = print_r($resultNode,true);
			$this->load->view('video_result.php',$resultNode);
		}
		//$this->output->set_output(print_r($results,true));
	}
	
	/***
	 * Aktualisiert einen Film mit Informationen aus der IMDb
	 */
	public function updateFromImdb() {
		$this->load->library('Videoindex');
		$this->load->library('Imdbapi');
		// get the parameter (video title)
		$video_title = $this->input->get('v');
		$no_retry = $this->input->get('noretry', 0);
		// get the movie from the index
		$index_item = $this->videoindex->getByTitle($video_title);
		if (empty($index_item)) {
			show_error($video_title.' does not exist in the index');
			return;
		}
		// get movie info from IMDb either by movie id (by-id mode)
		// or by movie title (default mode)
		if ($this->input->post('movieid')) {
			$movie_id = $this->input->post('movieid');
			$imdb_info = $this->imdbapi->byId($movie_id);
		}
		else {
			$imdb_info = $this->imdbapi->byTitle($video_title);
		}
		// fallback: display a "enter imdb id" form if no imdb information 
		// is available
		if (empty($imdb_info)) {
			if ($no_retry) {
				$this->load->view('video_queryimdb_notupdated', array(
					'movie_title' => $video_title
				));
			}
			else {
				$this->load->view('video_queryimdb_tryagain', array(
					'movie_title' => $video_title
				));
			}
			return;
		}
		// update the item in the index
		$this->videoindex->updateItemFromImdb($index_item, $imdb_info);
		$this->videoindex->update();

		$this->load->view('video_queryimdb_success', array(
			'movie_title' => $video_title
		));
	}

	/**
	 * Neue Datei hochladen und in den Index aufnehmen
	 */
	public function upload() {

		// get the movie directory path
		$movie_path_r = $this->config->item('movie_path_r','videogallery');
		
		// move uploaded files to video folder
		foreach (array_keys($_FILES) as $file_id) {
			
			// get the corresponding input values
			$title = $this->input->post($file_id.'title');
			$filename = $this->input->post($file_id.'filename');
			
			// move the uploaded file to the destination
			$filepath = BASEPATH.$movie_path_r.$filename;
			if (!is_uploaded_file($_FILES[$file_id]['tmp_name'])) {
				show_error($_FILES[$file_id]['tmp_name'].' is not an uploaded file');
				continue;
			}
			move_uploaded_file($_FILES[$file_id]['tmp_name'], $filepath);

			// get media info from file
			$media_info = $this->mediainfo->getFileInfo($filepath);
			
			// get movie info from IMDb
			$imdb_info = $this->imdbapi->byId($title);
			if (empty($imdb_info)) {
				$imdb_info = $this->imdbapi->query($title);
			}
			
			// add movie to index
			$this->videoindex->addItem($title, $filename, $media_info, $imdb_info);
			
		}
		// update the changes in the videoindex
		$this->videoindex->update();
		
	}
	
	public function advancedinfo() {
		$this->load->library('mediainfo');
		$this->load->library('videoindex');
		$v = $this->input->post('v');
		$item = $this->videoindex->getByTitle($v);
		if (!empty($item)) {
			$media_info = $this->mediainfo->getFileInfo($item['FilePath']);
			$this->load->view('video_advancedinfo_video', $media_info['Video']);
			$this->load->view('video_advancedinfo_audio', $media_info['Audio']);
		}
		else {
			$this->load->view('video_advancedinfo_notavailable', $item);
		}
	}

	public function removeItem() {
		$this->load->library('videoindex');
		$title = $this->input->post('id');
		$this->videoindex->removeItem($title);
		// save changes to the index
		$this->videoindex->update();		
	}
	
}