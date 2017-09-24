<?php

	class User {

		public static function IsAuthenticated() {

			if (!(isset($_SESSION['name']))) {
				return 0;
			}
			else {
				return 1;
			}
		}	
		
		public static function GetLoginInfo($name) {
			$sql = 'CALL webchat_users_get_login_info(:name)';
			
			$params = array (':name' => $name);
			
			return DatabaseHandler::GetRow($sql, $params);
		}
		
		public static function IsValid($name) {
			$user = self::GetLoginInfo($name);
			
			if (empty($user['id'])) {

				self::Add($name);	
				$user_name = $name;
				$_SESSION['name'] = $user_name;
				return 0;
			}else {
				return 2;
			}
		}
		
		public static function Add($name) {
			$sql = 'CALL webchat_users_add_user(:name)';
			
			$params = array(':name' => $name);
			
			DatabaseHandler::Execute($sql, $params);	
		}
		
		public static function Logout() {
			
			$name = self::GetCurrentUser();

			if (is_null(self::DeleteOneUser($name))) {
				$_SESSION = array();
				unset($_SESSION);
				return 0;
			}	
		}
		
		public static function GetCurrentUser() {

			if (self::IsAuthenticated() == 1) {
				return $_SESSION['name'];	
			}
			else {
				return 0;	
			}
		}
		
		public static function InsertMessage($name, $text) {
			$sql = 'CALL webchat_users_insert_message(:name, :text)';
			
			$params = array(':name' => $name, ':text' => $text);
			
			return DatabaseHandler::GetOne($sql, $params);
		}
		
		public static function UpdateUser($name) {
			$sql = 'CALL webchat_users_update_user(:name)';
			
			$params = array(':name' => $name);
			
			DatabaseHandler::Execute($sql, $params);	
		}
		
		public static function DeleteChatLines() {
			$sql = 'CALL webchat_lines_delete_chatlines()';
			
			DatabaseHandler::Execute($sql);	
		}
		
		public static function DeleteUsers() {
			$sql = 'CALL webchat_users_delete_users()';
			
			DatabaseHandler::Execute($sql);	
		}

		public static function DeleteOneUser($name) {

			$sql = 'CALL webchat_users_delete_one_user(:name)';

			$params = array(':name' => $name);

			DatabaseHandler::Execute($sql, $params);
		}
		
		public static function GetUsers() {
			$sql = 'CALL webchat_users_get_users()';
			
			return DatabaseHandler::GetAll($sql);	
		}
		
		public static function CountUsers() {
			$sql = 'CALL webchat_users_count_users()';
			
			return DatabaseHandler::GetOne($sql);	
		}
		
		public static function GetTodayChatLines() {
			$sql = 'CALL webchat_lines_get_today_chatlines()';
			
			return DatabaseHandler::GetAll($sql);	
		}
		
		public static function GetYesterdayChatLines() {
			$sql = 'CALL webchat_lines_get_yesterday_chatlines()';
			
			return DatabaseHandler::GetAll($sql);	
		}

		public static function GetTwoDaysAgoChatLines() {
			$sql = 'CALL webchat_lines_get_two_days_ago_chatlines()';
			
			return DatabaseHandler::GetAll($sql);	
		}

		public static function GetOneWeekAgoChatLines() {
			$sql = 'CALL webchat_lines_get_one_week_ago_chatlines()';
			
			return DatabaseHandler::GetAll($sql);	
		}

		public static function GetOneMonthAgoChatLines() {
			$sql = 'CALL webchat_lines_get_one_month_ago_chatlines()';
			
			return DatabaseHandler::GetAll($sql);	
		}

		public static function GetNowChatLines($lastID) {
			$sql = 'CALL webchat_lines_get_now_chatlines(:last_id)';

			$params = array(':last_id' => $lastID);

			return DatabaseHandler::GetAll($sql, $params);
		}

		public static function GetMaxID() {
			$sql = 'CALL webchat_lines_get_max_id()';

			return DatabaseHandler::GetOne($sql);
		}

	}
?>