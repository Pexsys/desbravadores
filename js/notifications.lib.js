function updateNotifications(){
	jsLIB.ajaxCall( true, jsLIB.rootDir+"rules/notifications.php", { MethodName : 'getNotifications' },
		function(data, jqxhr) {
			if (data.result === true){
				$("#notifyAlerts>ul").html(data.html);
				$("#notifyAlertsBadge>span").html(data.qt);
				$("#notifyAlertsBadge").visible(data.qt > 0);
				$("#notifyAlerts").show();
			} else {
				$("#notifyAlerts").hide();
			}
		}
	);
}