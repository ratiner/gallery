<?php
class io
{
	public function getdirs($path) {
		$arr = array();
	
		foreach (new DirectoryIterator($path) as $file)
		{
			if($file->isDot()) continue;
			if($file->isDir())
			{
				$arr[$file->getFilename()] = filemtime($path . '/' . $file);
			}
		}
	
		arsort($arr);
		return array_keys($arr);
	}
}
?>