function callAjax(postUrl, postData, funcToCall) {
	$.ajax({
		type: "POST",
		url: postUrl,
		data: postData
	}).done(function(o) {
		funcToCall(o);
	});
}