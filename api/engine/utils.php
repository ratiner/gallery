<?php
function autoload_class_multiple_directory($class_name)
{
	$parts = explode('\\', $class_name);

	# List all the class directories in the array.
	$array_paths = array(
		'engine',
		'modules'
	);

	foreach($array_paths as $path)
	{
		$file = __DIR__ .  sprintf('/../%s/%s.php', $path, implode('/', $parts));
		if(is_file($file)) {
			require_once $file;
			return;
		}

	}
}

function isArrObject($arr)
{
	return array_keys($arr) !== range(0, count($arr) - 1);
}


function UglifyIDs($data) {


	if(!$data)
		return $data;

	if(is_array($data)) {
		if(isArrObject($data)) {
			foreach(array_keys($data) as $field) {
				if($field == 'id' || strpos($field,'_id') !== false) {
					if(strlen($data[$field]) == UUID::$ID_LENGTH)
						$data[$field] = UUID::Encode($data[$field]);
				}
			}
		} else { // list of objects

			for($i=0; $i< count($data); $i++) {
				if(is_array($data[$i]))
					$data[$i] = UglifyIDs($data[$i]);
			}
		}
	}
	return $data;
}

function RetrieveIDs($data) {
	if(!$data)
		return $data;

	if(is_array($data)) {
		if(isArrObject($data)) {
			foreach(array_keys($data) as $field) {
				if($field == 'id' || strpos($field,'_id') !== false) {
					if(strlen($data[$field]) == UUID::$UGLYID_LENGTH) {
						$data[$field] = UUID::Decode($data[$field]);
					}
				}
			}
		} else { // list of objects

			for($i=0; $i< count($data); $i++) {
				if(is_array($data[$i]))
					$data[$i] = RetrieveIDs($data[$i]);
			}
		}
	}
	return $data;
}


spl_autoload_register('autoload_class_multiple_directory');

