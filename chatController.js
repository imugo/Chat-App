angular.module("myApp")
.config(function ($anchorScrollProvider) {
	$anchorScrollProvider.disableAutoScrolling();
})
.constant("ajaxChatUrl", "https://imugo.com.ng/ChatApp/chat/ajax_chat.php")
.component("chat", {
	templateUrl: "https://imugo.com.ng/views/chat.html",
	controller: function($scope, ngProgressFactory, $timeout, $http, ajaxChatUrl, ajax) {
		
		$scope.progressbar = ngProgressFactory.createInstance();
	
		$scope.progressbar.start();
		
		var bodyElement = $('body');
		
		$(window).scroll(function() {
				var scroll = $(window).scrollTop();
				if (scroll > 410) {
					$('#customNav').addClass("navbar-fixed");
				} else {
					$('#customNav').removeClass("navbar-fixed");
				}
		});
		
		$timeout(function() {
			$('.dropdown-button').dropdown({
			  inDuration: 300,
			  outDuration: 225,
			  constrainWidth: false, 
			  hover: false, 
			  gutter: 0, 
			  belowOrigin: false, 
			  alignment: 'left', 
			  stopPropagation: false
			}
		  );
		}, 0);
		
		$scope.chat = {
			error: {
				status: false,
			},
			chatForm: true,
			msgForm: false,
			logoutBtn: false,
			oneMonthAgo: [],
			oneWeekAgo: [],
			twoDaysAgo: [],
			yesterday: [],
			today: [],
			online: {
				users: [],
				count: 0
			}
		};

		$scope.closeErrorBox = function() {
			$scope.chat.error.status = false;
		};

		var chatData = {
			lastID: 0,
			noActivity: 0
		};

		var working = false;

		$scope.submitUser = function(name) {
		    $scope.progressbar.start();
		    bodyElement.css("opacity", 0.5);
			if (working) {
				return false;
			}
			working = true;
            
           
			var promise = ajax.chatPost(ajaxChatUrl, {'register': {'name': name.replace(/</g, '').replace(/>/g, '')}});
			promise.then(function(response) {
					working = false;

					if (response.data.error) {
						$scope.chat.error.status = true;
						$scope.chat.error.msg = response.data.error;
						$scope.progressbar.complete();
						bodyElement.css("opacity", 1.0);
					}
					else {
						$scope.chat.chatForm = false;
						$scope.chat.msgForm = true;
						$scope.chat.logoutBtn = true;
						$scope.chat.online.count++;
						chatData.name = response.data.name;
						$scope.progressbar.complete();
						bodyElement.css("opacity", 1.0);
					}
			}, function(error) {
				$scope.chat.error.status = true;
				$scope.chat.error.msg = 'Network error. Refresh page';
				bodyElement.css("opacity", 1.0);
				$scope.progressbar.complete();
			});
			
		};

		$scope.submitMsg = function(msg) {

			if (msg.length == 0) {
				$scope.chat.error.status = true;
				$scope.chat.error.msg = "You cannot send an empty message";
				return false;
			}

			if (working) {
				return false;
			}
			working = true;

			var tempId = "t" + Math.round(Math.random()*1000000), 
			d = new Date().toUTCString(),
			params = {
				id: tempId,
				author: chatData.name,
				text: msg.replace(/</g, '').replace(/>/g, ''),
				ts: d
			};

			$scope.chat.today.push(params);

			var promise = ajax.chatPost(ajaxChatUrl, {'submitChat': {'msg': msg.replace(/</g, '').replace(/>/g, '')}});
			$scope.msg = '';
			promise.then(function(response) {
				working = false;

				if (response.data.error) {
					$scope.chat.error.status = true;
					$scope.chat.error.msg = response.data.error;
				}
				else {
					for (var i=0; i<$scope.chat.today.length; i++) {
						if ($scope.chat.today[i].id == tempId) {
							$scope.chat.today[i].id = response.data.insertID;
							break;
						}
					}
				}
			});

		};



		$scope.logout = function() {
		    $scope.progressbar.start();
		    bodyElement.css("opacity", 0.5);
			$scope.chat.chatForm = true;
			$scope.chat.msgForm = false;
			var logOut = ajax.chatGet(ajaxChatUrl, {'logout': ''});
			logOut.then(function(response) {
				$scope.chat.logoutBtn = false;
				$scope.chat.online.count--;
				$scope.chat.online.users = [];
				bodyElement.css("opacity", 1.0);
				$scope.progressbar.complete();
			});
		};

		

		$scope.getChats = function(callback) {

			var promise = ajax.chatGet(ajaxChatUrl, {lastID: chatData.lastID});
			promise.then(function(response) {
                console.log(response.data);
				if (angular.isArray(response.data.oneMonth)) {
					angular.forEach(response.data, function(value, key) {
						if (key == 'oneMonth') {
							if (value.length !== 0) {
								angular.forEach(value, function(item){
									$scope.chat.oneMonthAgo.push(item);
								});
							}
						}
						else if (key == 'oneWeek') {
							if (value.length !== 0) {
								angular.forEach(value, function(item){
									$scope.chat.oneWeekAgo.push(item);
								});
							}
						}
						else if (key == 'twoDays') {
							if (value.length !== 0) {
								angular.forEach(value, function(item){
									$scope.chat.twoDaysAgo.push(item);
								});
							}
						}
						else if (key == 'yesterday') {
							if (value.length !== 0) {
								angular.forEach(value, function(item){
									$scope.chat.yesterday.push(item);
								});
							}
						}
						else if (key == 'today') {
							if (value.length !== 0) {
								angular.forEach(value, function(item){
									var len = $scope.chat.today.length;
										$scope.chat.today.push(item);

								});
								console.log($scope.chat.today);
							}
						}
					});
					chatData.lastID = response.data.maxId;
					chatData.noActivity = 0;

					var nextRequest = 15000;

					$timeout(callback, nextRequest);
				}

				else {
					if (response.data.now.length != 0) {
						for (var i=0; i<response.data.now.length; i++) {
							if (chatData.name != response.data.now[i].author) {
								$scope.chat.today.push(response.data.now[i]);
								Materialize.toast(response.data.now[i].author + ' - ' +response.data.now[i].text, 4000);
							}
						}

						chatData.lastID = response.data.maxId;
						chatData.noActivity = 0;

						var nextRequest = 1000;

						$timeout(callback, nextRequest);	
					}
					else {
						chatData.noActivity++;

						var nextRequest = 1000;

						if (chatData.noActivity > 3) {
							nextRequest = 2000;
						}

						if (chatData.noActivity > 10) {
							nextRequest = 5000;
						}

						if (chatData.noActivity > 10) {
							nextRequest = 15000;
						}

						$timeout(callback, nextRequest);
					}
				}
				
			});

		};

		$scope.getUsers = function(callback) {
			var contract = ajax.chatGet(ajaxChatUrl, {'getUsers': ''});
			contract.then(function(response) {
				var users = [];

				$scope.chat.online.count = response.data.total;
				$scope.chat.online.users = response.data.users;				

				$timeout(callback, 15000);
			});
		};

		// show more functionality
		var pagesShown = 1;
		var pageSize = 5;
		
		$scope.paginationLimit = function(data) {
			return -(pageSize * pagesShown);
		};
		
		$scope.hasMoreItemsToShow = function() {
			return pagesShown < ($scope.chat.today.length / pageSize);
		};
		
		$scope.yesterdayHasMoreItemsToShow = function() {
			return pagesShown < ($scope.chat.yesterday.length / pageSize);
		};
		
		$scope.twoDaysAgoHasMoreItemsToShow = function() {
			return pagesShown < ($scope.chat.twoDaysAgo.length / pageSize);
		};
		
		$scope.oneWeekAgoHasMoreItemsToShow = function() {
			return pagesShown < ($scope.chat.oneWeekAgo.length / pageSize);
		};
		
		$scope.oneMonthAgoHasMoreItemsToShow = function() {
			return pagesShown < ($scope.chat.oneMonthAgo.length / pageSize);
		};
		
		$scope.showMoreItems = function() {
			pagesShown = pagesShown + 1;
		};

		$scope.chatsCount = function() {
			 if (($scope.chat.oneMonthAgo.length + $scope.chat.oneWeekAgo.length + $scope.chat.twoDaysAgo.length + $scope.chat.yesterday.length +
				$scope.chat.today.length) == 0) {
			 	return true;
			 } else {
			 	return false;
			 }
		};

		ajax.chatGet(ajaxChatUrl, {'checkLogged': ''})
		.then(function(response) {
			if (response.data.logged) {
				chatData.name = response.data.loggedAs.name;
				$scope.chat.chatForm = false;
				$scope.chat.msgForm = true;
				$scope.chat.logoutBtn = true;
			}
		});

		(function getChatsTimeoutFun(){
			$scope.getChats(getChatsTimeoutFun);
		})();
		(function getUsersTimeoutFun(){
			$scope.getUsers(getUsersTimeoutFun);
		})();
		$scope.progressbar.complete();
	}, 
});
