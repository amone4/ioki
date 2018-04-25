<?php
function sendOTP($data) {
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => "http://2factor.in/API/V1/" . APIKEY . "/SMS/" . $data['phone'] . "/" . $data['otp'],
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