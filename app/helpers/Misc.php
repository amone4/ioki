<?php

defined('_INDEX_EXEC') or die('Restricted access');

class Misc {

	public static function validateLogin() {
		return isset($_SESSION['user']) && !empty($_SESSION['user']);
	}

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
	public static function uploadFile($tag,$size,$format,$filename,$directory,&$error=null) {
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

							// error messages
						} else $error = 'There was an error uploading your file';
					} else {
						$error = 'The allowed formats are ';
						foreach ($format as $key => $value) {
							$error .= $value.', ';
						}
						chop($error, ', ');
					}
				} else $error = 'Your file is larger than '.($size/1000).' KBs';
			} else $error = 'File already exists';
		} else $error = 'Upload a valid file';
		return false;
	}

	public static function toArray($input) {
		if (!is_array($input)) {
			if (empty($input)) return [];

			$temp[0] = $input;
			unset($input);
			$input = $temp;
		}
		return $input;
	}

	public static function writeMessage($message, $file = 'message.txt') {
		if ($con = fopen(APPROOT . '/' . $file, 'w')) {
			fwrite($con, $message);
			fclose($con);
			return true;
		}
		return false;
	}

	public static function sendOTP($data) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "http://2factor.in/API/V1/" . OTP_API_KEY . "/SMS/" . $data['phone'] . "/" . $data['otp'],
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => array(
				"content-type: application/x-www-form-urlencoded"
			),
		));

		curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		return !$err;
	}
}