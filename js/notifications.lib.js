function updateNotifications(){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/notifications.php",
		data: { MethodName : 'getNotifications' },
		callBackSucess: function(data, jqxhr) {
			if (data.result === true){
				$("#notifyAlerts>ul").html(data.html);
				$("#notifyAlertsBadge>span").html(data.qt);
				$("#notifyAlertsBadge").visible(data.qt > 0);
				$("#notifyAlerts").show();
			} else {
				$("#notifyAlerts").hide();
			}
		}
	});
}