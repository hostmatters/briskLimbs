<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

if (isset($_POST['code'])) {
	$path = $_POST['file'];
	$code = $_POST['code'];

	if (file_exists($path)) {
		if (file_put_contents($path, $code)) {
			$response = array('status' => 'success', 'message' => 'File updated successfully');
		} else {
			$response = array('status' => 'success', 'message' => 'Unable to update file');
		}
	} else {
		$response = array('status' => 'error', 'message' => 'Path not found');
	}

	sendJsonResponse($response);
}

if (isset($_POST['file'])) {
	$path = $_POST['file'];
	if (file_exists($path)) {
		$response = array('status' => 'success', 'code' => file_get_contents($path));
	} else {
		$response = array('status' => 'error', 'message' => 'Path not found');
	}

	sendJsonResponse($response);
}

if (isset($_POST['list'])) {
	$path = $_POST['list'];
	if (file_exists($path)) {
		if (is_dir($path)) {
			$list = listFiles($path, false, 2);
			$response = array('status' => 'success', 'contents' => $list, 'code' => file_get_contents(current($list)));
		} else {
			$response = array('status' => 'error', 'contents' => false, 'message' => 'Invalid directory path');
		}
	} else {
		$response = array('status' => 'error', 'contents' => false, 'message' => 'Path not found');
	}

	sendJsonResponse($response);
}

$section = isset($_GET['section']) ? $_GET['section'] : false;
if ($section == 'addons' || $section == 'skins') {
	$items = listFiles(CORE_DIRECTORY . '/' . $section);
	if (is_dir(current($items))) {
		$parameters['subItems'] = listFiles(current($items), false, 2);
	}
}

$parameters['_title'] = 'Editor - Dashboard';
$parameters['mainSection'] = $section;
$parameters['section'] = $section;
$parameters['items'] = isset($items) ? $items : false;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('editor.html', $parameters);