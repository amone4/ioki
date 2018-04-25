<?php
/**
 * function is used to upload a file
 * @param  string $tag       the name of the input field that was used to upload the file
 * @param  int $size      the allowed size of the file in bytes
 * @param  array $format    the allowed formats for the file
 * @param  string $filename  the base name of the file after upload
 * @param  string $directory the directory in which the file will be stored
 * @param  string $error	the error generated from the function
 * @return boolean            returns true if the file was uploaded successfully, otherwise false
 */
function uploadFile($tag,$size,$format,$filename,$directory,&$error=null) {
	@$imageFileType = array_pop(explode(".", strtolower($_FILES[$tag]["name"])));
	$target_file =  $directory.'/'.$filename.'.'.$imageFileType;
	// Check if image file is a actual image or fake image
	if(getimagesize($_FILES[$tag]["tmp_name"]) !== false) {
		// Check if file already exists
		if (!file_exists($target_file)) {
			// Check file size
			if ($_FILES[$tag]["size"] <= $size) {
				// Allow certain file formats
				$check = false;
				foreach ($format as $key => $value) {
					if ($imageFileType == $value) {
						$check = true;
					}
				}
				if ($check) {
					// if everything is ok, try to upload file
					if (move_uploaded_file($_FILES[$tag]["tmp_name"], $target_file)) {
						return true;
					} else {
						$error = 'There was an error uploading your file';
					}
				} else {
					$error = 'The allowed formats are ';
					foreach ($format as $key => $value) {
						$error .= $value.', ';
					}
					chop($error, ', ');
				}
			} else {
				$error = 'Your file is larger than '.($size/1000).' KBs';
			}
		} else {
			$error = 'File already exists';
		}
	} else {
		$error = 'Upload a valid file';
	}
	return false;
}