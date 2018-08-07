function updateNotifications(){
	jsLIB.ajaxCall({
		type: "GET",
		url: jsLIB.rootDir+"rules/notifications.php",
		data: { MethodName : 'getNotifications' },
		success: function(data, jqxhr) {
			$("#notifyTasks").hide();
			$("#notifyAlerts").hide();
			if (data.result === true){
				data.notifying.forEach((item, i, arr) => {
					$(`#${item.id}>ul`).html(item.html);
					$(`#${item.id}Badge>span`).html(item.qt);
					$(`#${item.id}Badge`).visible(item.qt > 0);
					$(`#${item.id}`).visible(item.qt > 0);
				});
			}
		}
	});
}