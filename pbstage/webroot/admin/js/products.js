$(document).ready(function () {
	var baseUrl = window.location.origin;
	//get ajax call here
	$.ajax({url: baseUrl+"/pb/admin/pgs/notifications", success: function(result){
        $(".notifications-menu").html(result);
    }});
});
