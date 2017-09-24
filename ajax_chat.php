<?php
	
	session_start();

	require '../model/config.php';
	require '../model/database_handler.php';
	require 'business/User.class.php';
	require 'Chat.class.php';
	

	try {
		$response = array();

		if (isset($_POST['register'])) {
			$data = $_POST['register'];
			$name = htmlspecialchars(trim($data['name']));
			$response = Chat::register($name);
		}
		elseif (isset($_POST['submitChat'])) {
			$data = $_POST['submitChat'];
			$chat = htmlspecialchars($data['msg']);
			$response = Chat::submitChat($chat);
		}
		elseif (isset($_GET['logout'])) {
			
			$response = Chat::logout();
		}

		elseif (isset($_GET['checkLogged'])) {
			$response = Chat::checkLogged();
		}

		elseif (isset($_GET['lastID'])) {
			$last_id = (int)$_GET['lastID'];
			$response = Chat::getChats($last_id);
		}

		elseif (isset($_GET['getUsers'])) {
			$response = Chat::getUsers();
		}

		echo json_encode($response);
	}
	catch(Exception $e) {
		die(json_encode(array('error' => $e->getMessage())));	
	}
	
?>