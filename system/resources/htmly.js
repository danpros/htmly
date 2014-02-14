(function ($) {

	$(document).ready(function() {
		$('.post-teaser img, .post-body img').each(function() {
			var currentImage = $(this);
			currentImage.wrap("<a class='img-wrap' title='" + currentImage.attr("alt") + "' data-lightbox='lightbox' href='" + currentImage.attr("src") + "'></a>");
		});
	});

})(jQuery);