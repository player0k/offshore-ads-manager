(function ($) {
	/**
	 * Initialize all tabs on page loaded 
	 */
	fwEvents.on('fw:options:init', function (data) {
		fwEvents.trigger('fw:options:init:tabs', {
			$elements: $('.fw-settings-form')
		});
		fwEvents.trigger('fw:options:tabs:initialized', {
				$tabCategories: $('#fw-options-box-ads_categories_tab'),
				$tabBanners:$('#fw-options-box-ads_banners_tab')
			}
		)
	});

	var initialized = false;
	fwEvents.on('fw:options:tabs:initialized', function (data) {
		if (initialized) {
			return false;
		}
		initialized = true;

		var $categories = data.$tabCategories.find('.fw-backend-option-type-multi-select-extended input');

		/**
		 * send trigger from categories tab to banners tab on category changes 
		 */
		$categories.each(function (id, element) {
			var $element = $(element);
			$element.on('blur', function () {
				var term = $element.data('selectize').getValue(),
					position = $element.data('position'),
					$banner = $('input.fw-option-type-multi-select-extended[data-position="'+ position +'"][data-type="banner"]');

				if ($element.val() != term) {
					fwEvents.trigger('fw:options:multi-select-extended:term:change', {$element: $banner, value: term, population: 'banner', source: '["'+ PhpVar.bannerPostType + '"]'});
				}
			});
		});

		/**
		 * hide alert on tab switch
		 */
		$('[href="#fw-options-tab-sub_tab_2"]').on('click',function () {
			$('.message-box').hide();
		});
	});

	/*
	 * on remove item from banner's category select trigger change term
	 */
	fwEvents.on('fw:options:multi-select-extended:item:remove', function (data) {
		if (data.$element.data('type') == 'category') {

			var term = 0,
				position = data.$element.data('position'),
				$banner = $('input.fw-option-type-multi-select-extended[data-position="'+ position +'"][data-type="banner"]');

				fwEvents.trigger('fw:options:multi-select-extended:term:change', {$element: $banner, value: term, population: 'banner', source: '["'+ PhpVar.bannerPostType + '"]'});
		}
	});

	/**
	 * Display the alert box on change category
	 */
	fwEvents.on('fw:options:multi-select-extended:term:change', function (data) {
		var $msgBox = $('#fw-options-tab-offshore-ads-manager').parent().find('.message-box'),
			html = '<div class="message-box alert-box"><a href="#" onclick="this.parentNode.style.display = \'none\';" class="close" data-dismiss="alert">×</a>'+ PhpVar.alertMessage +'</div>';
		if ($msgBox.length) {
			$msgBox.show();
		} else {
			$('#fw-options-tab-offshore-ads-manager').before(html);
		}
	});


})(jQuery);
