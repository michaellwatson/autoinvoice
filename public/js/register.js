var base_url = base_url;
console.log(base_url);

$(document).ready(function () {

	$(".form-resend").submit(function (e) {
		//alert('hit');
		e.preventDefault();
		//var l = Ladda.create($('#submitButton'));
		var l = Ladda.create(document.querySelector('#resendButton'));
		l.start();
		var url = base_url+"login/resendCheck";

		$.ajax({
			type: "POST",
			url: url,
			data: $(".form-resend").serialize(), // serializes the form's elements.
			dataType: 'json',
			success: function (data) {
				if (data.status == 1) {
					$('#successMessageResend').html(data.msg);
					$('#successMessageResend').show();
					//window.scrollTo(0, 0);
				} else {
					//alert(data.msg);
					$('#warningMessageResend').html(data.msg);
					$('#warningMessageResend').show();
					setTimeout(function () {
						$('#warningMessageResend').fadeOut();
					}, 2000);
				}
				l.stop();
			}
		});
	});

	$(".form-forgot").submit(function (e) {
		//alert('hit');
		e.preventDefault();
		//var l = Ladda.create($('#submitButton'));
		var l = Ladda.create(document.querySelector('#forgotButton'));
		l.start();
		var url = base_url+"login/forgotPasswordCheck";

		$.ajax({
			type: "POST",
			url: url,
			data: $(".form-forgot").serialize(), // serializes the form's elements.
			dataType: 'json',
			success: function (data) {
				if (data.status == 1) {
					$('#successMessageForgot').html(data.msg);
					$('#successMessageForgot').show();
				} else {
					//alert(data.msg);
					$('#warningMessageForgot').html(data.msg);
					$('#warningMessageForgot').show();
					setTimeout(function () {
						$('#warningMessageForgot').fadeOut();
					}, 2000);
				}
				l.stop();
			}
		});
	});

	$(".form-signin").submit(function (e) {
		//alert('hit');
		e.preventDefault();
		//var l = Ladda.create($('#submitButton'));
		var l = Ladda.create(document.querySelector('#submitButton'));
		l.start();
		var url = base_url+"login";

		$.ajax({
			type: "POST",
			url: url,
			data: $(".form-signin").serialize(), // serializes the form's elements.
			dataType: 'json',
			success: function (data) {
				if (data.status == 1) {
					if (data.redirect !== undefined) {
						window.location = base_url+'dashboard';
					} else {
						$('.form-signin').hide();

						$('#successMessageLogin').html(data.msg);
						$('#successMessageLogin').show();
						//window.scrollTo(0, 0);
					}
				} else {
					//alert(data.msg);
					$('#warningMessageLogin').html(data.msg);
					$('#warningMessageLogin').show();
					setTimeout(function () {
						$('#warningMessageLogin').fadeOut();
					}, 2000);
					l.stop();
					//window.scrollTo(0, 0);
				}
			}
		});
	});

	$(".form-register").submit(function (e) {
		//alert('hit');
		e.preventDefault();
		//var l = Ladda.create($('#submitButton'));
		var l = Ladda.create(document.querySelector('#submitButton'));
		l.start();
		var url = base_url+"register/register_user";

		$.ajax({
			type: "POST",
			url: url,
			data: $(".form-register").serialize(), // serializes the form's elements.
			dataType: 'json',
			success: function (data) {
				if (data.status == 1) {
					window.location = base_url+'register/confirm';

				} else {

					$('#warningMessageLogin').html(data.msg);
					$('#warningMessageLogin').show();
					setTimeout(function () {
						$('#warningMessageLogin').fadeOut();
					}, 2000);
					l.stop();
				}
			}
		});
	});
});