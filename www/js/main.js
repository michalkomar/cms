$(function() {
	// BOXES
	$('div[id^="referenceBox-"] .box-image').on('click', function(e){
		var box = this;
		var boxId = $(this).data('box-id');
		var detail = $('#referenceBox-detail-'+boxId);
		detail.fadeIn({
			complete: function(){
				var location = $(detail).offset().top;
				$('html, body').animate({
					scrollTop: location - $('nav.navbar-fixed-top').height()
				});
			}
		});

		var boxesId = detail.data('group-id');
		$('[data-box-id="'+boxesId+'"]').find('[id^="referenceBox-images-"]').hide();

	});
	$('.referenceBox-detail-close').on('click', function(e){
		var detail = $(this).closest('[data-role="box-detail-frame"]')
			detail.hide();

		var group = $('#referenceBox-'+$(detail).data('group-id'));

		$('[id^="referenceBox-images-"]').fadeIn();
		$('html, body').animate({
			scrollTop: group.offset().top - $('nav.navbar-fixed-top').height() - 20
		});
	});
	$('.reference-Box-categories').on('click', function(){
		console.log(this);
		$(this).closest('div[id^="referenceBox-"]').find('div[id^="referenceBox-detail-"]').fadeOut();
		$(this).closest('div[id^="referenceBox-"]').find('div[id^="referenceBox-images-"]').fadeIn();
	});

	$('body').addClass('js');

	/**
	 * Display error message.
	 */
	Nette.addError = function(elem, message) {
		$('.netteFormError').remove();

		if (elem.focus) {
			elem.focus();
		}

		var form = $(elem).closest('form');
		var errorsContainer = $(form).find('.form-errors');

		if (message) {
			bootstrap.flashMessage(message, 'danger netteFormError', undefined, errorsContainer);
		}
	};

	$(document).ajaxComplete(function(event, jqXHR) {
		try
		{
			var responseText = $.parseJSON(jqXHR.responseText);
		}
		catch (exc)
		{
			var responseText = jqXHR.responseText;
		}

		typeof responseText.redirect === 'string' ? window.location.replace(responseText.redirect) :null;
	});

	bootstrap.autoClosingAlert();
}).jQuery;

var bootstrap = {
	flashMessage: function(message, type, backlink, errorContainer) {
		if (type === undefined || type === null) {
			type = 'success';
		}
		var template = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' +
				'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span>' +
				'<span class="sr-only">Zavřít</span></button>' + message;

		if (backlink !== undefined)
		{
			template += ' <a href="' + backlink + '" class="ajax">Vzít zpět.</a>';
		}

		template += '</div>';

		var container;
		if (errorContainer !== undefined)
		{
			container = errorContainer;
		}
		else
		{
			container = $('#flashes');
		}

		container.append(template);

		container.on('click', '.ajax', function(e) {
			e.preventDefault();
			$.nette.ajax(this.href, this, e);
		});

		this.autoClosingAlert();
	},
	autoClosingAlert: function() {
		$('.alert-success').not('.not-hide').alert().delay(3000).fadeOut(3000);
		$('.alert-info').not('.not-hide').alert().delay(3000).fadeOut(3000);
		$('.alert-warning').not('.not-hide').alert().delay(9000).fadeOut(3000);
		$('.alert-danger').not('.not-hide').alert().delay(9000).fadeOut(3000);
	}
};

function getHashParams() {

	var hashParams = {};
	var e,
			a = /\+/g,  // Regex for replacing addition symbol with a space
			r = /([^&;=]+)=?([^&;]*)/g,
			d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
			q = window.location.hash.substring(1);

	while (e = r.exec(q))
		hashParams[d(e[1])] = d(e[2]);

	return hashParams;
}

function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(key) {
	var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
	return keyValue ? keyValue[2] : null;
}


