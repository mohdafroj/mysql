<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;

/**
 * Admin component
 */
class MediaComponent extends Component
{
	public function getData($path) {
		$dir = new Folder(WWW_ROOT.$path);
		return $dir->read(true);
	}
	
	
	
}
