$(document).ready(function(){
	$("#myCarousel").carousel();
	//notifyMe();
});
/*
function renotification(options){
	var n = new Notification("Ola nenenzinho!",options);
	window.setTimeout(renotification, 30000, options);
}
function notifyMe() {
	var options = {
		body: "Teste do Pexinho"
	};
	
	// Let's check if the browser supports notifications
	if (!("Notification" in window)){
		//alert("This browser does not support system notifications");
	
	// Let's check whether notification permissions have already been granted
	} else if (Notification.permission === "granted"){
		// If it's okay let's create a notification
		renotification(options);
		
	// Otherwise, we need to ask the user for permission
	} else if (Notification.permission !== 'denied') {
		Notification.requestPermission(function (permission){
			// If the user accepts, let's create a notification
			if (permission === "granted"){
				renotification(options);
			}
		});
	}
	
	// Finally, if the user has denied notifications and you 
	// want to be respectful there is no need to bother them any more.
}
*/