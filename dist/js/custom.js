/* ----- Preloader ----- */
function preloaderLoad() {
    if($('.preloader').length){
        $('.preloader').delay(300).fadeOut(400);
    }
}

/* ----- Send Announcement ----- */
function sendAnnouncement(url) {
	prof = !$("#a-check-prof").is(':checked') ? null :$("#a-sel-prof").val();
	students = !$("#a-check-stud").is(':checked') ? null : $("#a-sel-stud").val();
	message = $("#a-message").val();

	$(".preloader").show();
	$.ajax({
		url: url,
		type: "post",
		data: { prof: prof, students: students, message: message },
		success: function(response){
			console.log(response);
			var result = $.parseJSON(response);
			console.log(result["status"]);

			$("#a-alert-message").html('');
			if (result["status"]) {
				$("#a-alert-message").html('<div class="alert alert-success">' + result['message'] + '</div>');
				resetAnnouncement();
			} else {
				$("#a-alert-message").html('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			$(".preloader").hide();
		}
	});
}

function resetAnnouncement() {
	$("#a-sel-stud option:selected").removeAttr('selected');
	$("#a-check-stud").prop('checked', false);
	$("#a-sel-stud").attr('disabled', '');

	$("#a-check-prof").prop('checked', false);
	$("#a-sel-prof option:selected").removeAttr('selected');
	$("#a-sel-prof").attr('disabled', '');

	$("#a-message").val('');
}

function isCheckProf() {
	$("#a-check-prof").on("change", function(){
		if ($(this).is(':checked')) {
			$("#a-sel-prof").removeAttr('disabled');
		} else {
			$("#a-sel-prof").attr('disabled', '');
		}
	});
}

function isCheckStud() {
	$("#a-check-stud").on("change", function(){
		if ($(this).is(':checked')) {
			$("#a-sel-stud").removeAttr('disabled');
		} else {
			$("#a-sel-stud").attr('disabled', '');
		}
	});
}

/* ----- Send Grouping ----- */
function sendGrouping(url) {
	students = $("#g-sel-stud").val();
	message = $("#g-message").val();

	$(".preloader").show();
	$.ajax({
		url: url,
		type: 'post',
		data: { students: students, message: message},
		success: function(response) {
			console.log(response);
			var result = $.parseJSON(response);
			console.log(result["status"]);

			$("#g-alert-message").html('');
			if (result["status"]) {
				$("#g-alert-message").html('<div class="alert alert-success">' + result['message'] + '</div>');
				$("#g-sel-stud option:selected").removeAttr('selected');
				$("#g-message").val('');
			} else {
				$("#g-alert-message").html('<div class="alert alert-danger">' + result['message'] + '</div>');
			}
			$(".preloader").hide();
		}

	});
}