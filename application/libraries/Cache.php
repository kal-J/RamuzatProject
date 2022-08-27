<?php

class Cache{
	//class vars
	// Number of seconds a page should remain cached for
	var $cache_expires;
	// Path to the cache folder
	var $cache_folder;
	// Let's begin, first work out the cached filename
	var $cachefile; //path to file
	var $cachefile_created; //bool created or not
	var $cache_valid; //bool valid or not
	function __construct($expires =0) {
		if($expires != 0){
			$this->cache_expires = $expires;
		}else{
			$this->cache_expires = 43200;
		}
	} 
	
	function runCache($file_path=''){
		// Path to the cache folder
		$this->cache_folder = FCPATH . "myassets" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR;
		$this->cachefile = $this->cache_folder . $file_path;
		ob_start();
	}

	// Checks whether the page has been cached or not
	function isValid() {
		$this->cachefile_created = (file_exists($this->cachefile)) ? @filemtime($this->cachefile) : 0;
		return (time() - $this->cache_expires) < $this->cachefile_created;
	}

	// Reads from a cached file
	function readCache() {
		return file_get_contents($this->cachefile);
	}

	// Writes to a cached file
	function writeCache($content_to_write) {
		$fp = fopen($this->cachefile, 'w');
		fwrite($fp, $content_to_write);
		fclose($fp);
	}
	function unlinkFile($file_path){
		$cachefile = $this->cache_folder . $file_path;
		if(file_exists($cachefile)){
			unlink($cachefile);
		}
		
	}
	function createCacheFile($contents){
		if (file_exists($this->cachefile)) {
			$diff_in_secs = (time() - $this->cache_expires*60) - filemtime($this->cachefile);
			if ($diff_in_secs < 0) {
			   unlink($this->cachefile);
			   $this->writeCache($contents);
			}
		} else {
			$this->writeCache($contents);
		}
	}
}
?>
