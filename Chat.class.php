<?php
	class Chat {

		public static function register($name) {
			$name = htmlspecialchars(trim($name));
			if (!$name) {
				throw new Exception('Fill in all the required fields.');	
			}
			
			if (User::IsValid($name) == 2) {
				throw new Exception('Nick is in use');
			}
			else {
				return array(
					'status' => 1,
					'name' => $name
				);
			}
		}
		
		public static function checkLogged() {
			$response = array('logged' => false);
			
			if (User::GetCurrentUser()) {
				$name = User::GetCurrentUser();
				$response['logged'] = true;
				$response['loggedAs'] = array(
					'name' => $name,
				);
			}
			
			return $response;
		}
		
		public static function logout() {
			if (User::Logout() == 0) {	
				return array('status' => 1);
			}
		}
		
		public static function submitChat($chatText) {

			if (!User::GetCurrentUser()) {
				throw new Exception('You are not logged in.');	
			}
			
			if (!$chatText) {
				throw new Exception("You haven't entered a message");	
			}
			
			$name = User::GetCurrentUser();
			
			$insert_id = User::InsertMessage($name, $chatText);
			
			return array(
				'status' => 1,
				'insertID' => $insert_id
			);
		}
		
		public static function getUsers() {
			
			if (User::GetCurrentUser()) {
				$name = User::GetCurrentUser();
				if (User::GetLoginInfo($name)) {
					User::UpdateUser($name);	
				}
				else {
					User::Add($name);
				}
			}
			
			User::DeleteUsers();
			
			$users = User::GetUsers();
			$total = User::CountUsers();
			
			if (is_null($total)) {
				$total = 0;
			}

			return array(
				'users' => $users,
				'total' => $total
			);
		}
		
		public static function getChats($lastID){

			$result = array();
			$lastID = (int)$lastID;

			if ($lastID == 0) {
				$oneMonth = User::GetOneMonthAgoChatLines();
				$oneWeek = User::GetOneWeekAgoChatLines();
				$twoDays = User::GetTwoDaysAgoChatLines();
				$yesterday = User::GetYesterdayChatLines();
				$today = User::GetTodayChatLines();
				$maxId = User::GetMaxID();

				if (is_null($maxId)) {
					$maxId = 0;
				}

				$result = array(
					'oneMonth'=> $oneMonth,
					'oneWeek' => $oneWeek,
					'twoDays' => $twoDays,
					'yesterday' => $yesterday,
					'today' => $today,
					'maxId' => $maxId
				);
			}
			
			else {
				$now = User::GetNowChatLines($lastID);
				$maxId = User::GetMaxID();



				$result = array(
					'now' => $now,
					'maxId' => $maxId
				);
			}
			
			// for ($i=0; $i<count($result); $i++) {
			// 	$result[$i]['time'] = array(
			// 		'hours' => gmdate('H', strtotime($result[$i]['ts'])),
			// 		'minutes' => gmdate('i', strtotime($result[$i]['ts']))
			// 	);	
			// }
						
			return $result;
		}
	}
?>