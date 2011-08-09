<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Videoindex {

	public $ci;
	public $index_file_path;
	public $index_xml;
	public $query_fmt;
	
	public function __construct(array $params = array()) {
		$this->ci =& get_instance();
		// load the imdb api
		$this->ci->load->library('Imdbapi');		
		// read the settings
		$this->ci->config->load('videogallery');
		$this->index_file_path = $this->ci->config->item('index_file_path');
		if (!file_exists($this->index_file_path)) {
			// index rebuild required
			show_error('index file not found. rebuild the index and try again');
		}
	}
	
	/***
	 * has the index been loaded?
	 */
	public function is_loaded() {
		return $this->index_xml;
	}
	
	/***
	 * load the index
	 */
	public function load() {
		$errors = array();
		// load the xml file
		$this->index_xml = simplexml_load_file($this->index_file_path);
		if (count($errors)==0) {
			// load xml namespaces
			foreach ($this->index_xml->getNamespaces(true) as $ns=>$nsurl) {
				$this->index_xml->registerXPathNamespace($ns, $nsurl);	
			}
		}	
		else {
			array_unshift($errors, 'index file could not be parsed. rebuild the index and try again.');
			show_error(implode('<br />',$errors));
		}
	}
	
	/***
	 * count the items in the library 
	 */
	public function itemCount() {
		// try to load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// return the item count
		return $this->index_xml->count();
	}
	
	public function getByTitle($title) {
		// try to load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// build the xpath query
		$title = str_replace('\'','\\\'',$title);
		$conditions = array(
			'(Title=\''.$title.'\')'
		);
		$cond_str = implode(' or ', $conditions);
		$query = '/Index/Item['.$cond_str.']';
		// search the index xml
		$results = $this->index_xml->xpath($query);
		$results_a = $this->_extractResults($results);
		if (count($results_a) > 0) {
			return $results_a[0];
		}
		else {
			return array();
		}
	}

	/***
	 * search all items in the index matching the keyword
	 */
	public function search($keyword) {
		// try to load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// escape the keyword for xpath
		$keyword = str_replace('\'','\\\'',$keyword);
		// build the xpath query
		$conditions = array(
			'(contains(Title,\''.$keyword.'\'))'
			,'(contains(Description,\''.$keyword.'\'))'
			,'(contains(Tags/Tag,\''.$keyword.'\'))'
		);
		$cond_str = implode(' or ', $conditions);
		$query = '/Index/Item['.$cond_str.']';
		// search the index xml
		$results = $this->index_xml->xpath($query);
		return $this->_extractResults($results);
	}
	
	private function _extractResults($queryResults) {
		// convert the results
		$result_a = array();
		$i = 0;
		foreach ($queryResults as $resultNode) {
			$result = array();
			foreach ($resultNode->children() as $child => $value) {
				//$result[$child] = $value->asXML();
				$result[$child] = $value[0];
			}
			$result['Index'] = $i;
			$title = str_replace('\'','\\\'',$result['Title']);
			$result['_xpath_'] = '/Index/Item[Title=\''.$title.'\']';
			$result_a[] = $result;
			$i++;
		}
		return $result_a;		
	}
	
	/***
	 * updates an item in the index with data from the imdb
	 * DO NOT forget to persist changes using update()
	 */
	public function updateItemFromImdb($item, $imdbInfo=array()) {
		$this->ci->load->library('Imdbapi');
		// search the item on imdb if needed
		if (empty($imdbInfo)) {
			$imdbInfo = $this->imdbapi->query($item['Title']);
			if (empty($imdbInfo)) {
				throw new Exception('ArgumentError: imdbInfo not specified and movie not found on imdb.com either');
				return;
			}
		}
		// load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// get a reference to the item node in the index xml
		$results = $this->index_xml->xpath($item['_xpath_']);
		// update the index item
		if (count($results) > 0) {
			// get the plot from imdb
			$imdb_href = $this->ci->imdbapi->makeUrl($imdbInfo['ID']);
			$results[0]->Description = $imdbInfo['Plot'];
			// get the poster from imdb
			$poster_info = pathinfo($imdbInfo['Poster']);
			$poster_ext = $poster_info['extension'];
			$preview_url = $results[0]->FileUrl.'preview.'.$poster_ext;
			$preview_file = $results[0]->FilePath.'preview.'.$poster_ext;
			$this->ci->imdbapi->getPoster($imdbInfo, $preview_file);
			$results[0]->PreviewImgUrl = $preview_url;
			$results[0]->ImdbHref = $imdb_href;
			$results[0]->ImdbId = $imdbInfo['ID'];
			$results[0]->Year = $imdbInfo['Year'];
			// update genre tags
			$tags = explode(', ', $imdbInfo['Genre']);
			unset($results[0]->Tags);
			$tags_node = $results[0]->addChild('Tags');
			foreach ($tags as $tag) {
				$tags_node->addChild('Tag', $tag);
			}
			// update artists
			$artists = explode(', ', $imdbInfo['Actors']);
			unset($results[0]->Artists);
			$artists_node = $results[0]->addChild('Artists');
			foreach ($artists as $artist) {
				$artists_node->addChild('Artist', $artist);
			}
		}
	}
	
	/**
	 * Adds a new item to the index
	 * DO NOT forget to persist changes using update()
	 */
	public function addItem($title, $filename, $media_info, $imdb_info) {
		// load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// Dateipfad erzeugen
		$filepath = $_SERVER['DOCUMENT_ROOT'].'/'.$this->ci->config->item('video_path_r');
		//$filepath = 'D:\xampp\myprojects\mediabox\htdocs\videos\\'.$filename;
		$title = basename($filepath, '.m4v');
		$filename = basename($filepath);
		// Neues Item in Index einfügen
		$newItem = $this->index_xml->addChild('Item');
		$newItem->addChild('Title', $title);
		$newItem->addChild('FilePath', $filepath);
		$newItem->addChild('WatchUrl', '/index.php/video/watch?v='.$title);
		$newItem->addChild('FileUrl', '/videos/'.$filename);
		$newItem->addChild('PreviewImgUrl', '');
		$newItem->addChild('Width', $media_info['Video']['Width']);
		$newItem->addChild('Height', $media_info['Video']['Height']);
		$newItem->addChild('Description', empty($imdb_info) ? '' : $imdb_info['Plot']);
		$newItem->addChild('ImdbId', empty($imdb_info) ? '' : $imdb_info['ID']);
		$newItem->addChild('ImdbHref', $this->ci->imdbapi->makeUrl($imdb_info['ID']));
		$newItem->addChild('Year', empty($imdb_info) ? '' : $imdb_info['Year']);
		// update genre tags
		$tags = explode(', ', $imdb_info['Genre']);
		$tags_node = $newItem->addChild('Tags');
		foreach ($tags as $tag) {
			$tags_node->addChild('Tag', $tag);
		}
		// update artists
		$artists = explode(', ', $imdb_info['Actors']);
		$artists_node = $newItem->addChild('Artists');
		foreach ($artists as $artist) {
			$artists_node->addChild('Artist', $artist);
		}
	}
	
	public function update() {
		// update the index file
		if ($this->is_loaded()) {
			file_put_contents($this->index_file_path, $this->index_xml->asXML());
		}
	}
	
	public function getItems($xpath) {
		// try to load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// search the index xml
		$results = $this->index_xml->xpath($xpath);
		return $this->_extractResults($results);
	}
	
	public function removeItem($title) {
		// try to load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// remove the specified item for the index
		for ($i = 0; $i<$this->index_xml->count(); $i++) {
			if (strcmp($title,$this->index_xml->Item[$i]->Title)==0) {
				unset($this->index_xml->Item[$i]);
				break;
			}
		}
	}
	
	public function upgrade($targetVersion) {
		// try to load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		// upgrade the items
		for ($i = 0; $i<$this->index_xml->count(); $i++) {
			$targetVersion->upgrade($this->index_xml->Item[$i]);
		}
		// upgrade the index version
		if (!isset($this->index_xml->attributes()->version)) {
			$this->index_xml->addAttribute('version', $targetVersion->getVersion());
		}
		else {
			$this->index_xml->attributes()->version = $targetVersion->getVersion();
		}
	}
	
	public function updateItem($item, $newValues) {
		// try to load the index if needed
		if (!$this->is_loaded()) {
			$this->load();
		}
		$results = $this->index_xml->xpath($item['_xpath_']);
		if (count($results) == 1) {
			$results[0]->Title = isset($newValues['Title']) ? $newValues['Title'] : $item->Title;
			$results[0]->ImdbId = isset($newValues['ImdbId']) ? $newValues['ImdbId'] : $item->ImdbId;
			$results[0]->Year = isset($newValues['Year']) ? $newValues['Year'] : $item->Year;
			$results[0]->Description = isset($newValues['Description']) ? $newValues['Description'] : $item->Description;
			// TODO: update tags
			unset($results[0]->Tags);
			$tags_node = $results[0]->addChild('Tags');
			foreach ($newValues['Tags'] as $tag) {
				$tags_node->addChild('Tag', $tag);
			}
			// TODO: update artists
			unset($results[0]->Artists);
			$artists_node = $results[0]->addChild('Artists');
			foreach ($newValues['Artists'] as $artist) {
				$artists_node->addChild('Artist', $artist);
			}
			$results[0]->FilePath = isset($newValues['FilePath']) ? $newValues['FilePath'] : $item->FilePath;
			$results[0]->FileUrl = isset($newValues['FileUrl']) ? $newValues['FileUrl'] : $item->FileUrl;
			$results[0]->WatchUrl = isset($newValues['WatchUrl']) ? $newValues['WatchUrl'] : $item->WatchUrl;
			$results[0]->Width = isset($newValues['Width']) ? intval($newValues['Width']) : $item->Width;
			$results[0]->Height = isset($newValues['Height']) ? intval($newValues['Height']) : $item->Height; 
		}
	}
	
}

abstract class IndexItemUpgrade {

	public function __construct() {
		
	}
	
	protected function setChild(& $item, $name, $value=NULL) {
		if (!isset($item->$name)) {
			$item->addChild($name, $value);
		}
	}
	
	public abstract function upgrade(& $item);
}

class IndexItemUpgrade_v2 extends IndexItemUpgrade {
	
	public function __construct() {
		
	}
	
	public function getVersion() {
		return 2;
	}
	
	public function upgrade(& $item) {
		$this->setChild($item, 'Title');
		$this->setChild($item, 'Description');
		$this->setChild($item, 'FilePath');
		$this->setChild($item, 'WatchUrl');
		$this->setChild($item, 'FileUrl');
		$this->setChild($item, 'PreviewImgUrl');
		$this->setChild($item, 'Width');
		$this->setChild($item, 'Height');
		$this->setChild($item, 'Tags');
		$this->setChild($item, 'ImdbHref');
		$this->setChild($item, 'ImdbId');
		$this->setChild($item, 'Year');
		$this->setChild($item, 'Rating', 0);
		$this->setChild($item, 'Artists');
	}
	
}
