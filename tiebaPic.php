<?php
header('content-type:text/html;charset=utf-8');
require('./firedemo.php');
require('./gd.php');
require('./curl.php');
class tiebaPic {
	protected function gd($title){
		tiebaGd($title);
	}
	protected function curl($title){
		tiebaCurl($title);
	}
	public function __construct($title){
		@tiebaDelDir('./pic');
		echo $this->fileSearch($title);
		$this->curl($title);
		$this->gd($title);
	}
	protected function fileSearch($title){
		$title = urlencode($title);
		if(file_exists("./tiebapic/{$title}.jpg")) return realpath("./tiebapic/{$title}.jpg");
	}
}
new TiebaPic($_POST['title']);
