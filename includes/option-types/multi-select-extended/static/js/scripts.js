(function ($) {
	var fw_option_multi_select_extended_initialize = function(item) {
		var population = item.attr('data-population');
		var source = item.attr('data-source');
		var limit = parseInt(item.attr('data-limit'));
		var xhr;

			item.selectize({
			maxItems: ( limit > 0 ) ? limit : null,
			delimiter: '/*/',
			valueField: 'val',
			labelField: 'title',
			searchField: 'title',
			options: JSON.parse(item.attr('data-options')),
			create: false,
			onItemRemove: function (value) {
				fwEvents.trigger('fw:options:multi-select-extended:item:remove',{ $element: item, value: value});
			},
			onDropdownClose: function(dropdown) {
				item.blur();
				$(this.$control[0]).removeClass('danger-changes')
			},
			onType: function (value) {
				if (population == 'array') {
					return;
				}

				if (value.length < 2) {
					return;
				}

				this.load(function (callback) {
					xhr && xhr.abort();

					var data = {
						action: 'admin_action_ajax_response',
						data: {
							string: value,
							term: item.attr('data-term'),
							posts: item.attr('data-posts'),
							type: population,
							names: source
						}
					};

					xhr = $.post(
						ajaxurl,
						data,
						function (response) {
							callback(response.data)
						}
					)
				});

			}
		});
	};
	
	fwEvents.on('fw:options:multi-select-extended:term:change', function (data) {
		data.$element.attr('data-term', data.value);

		/**
		 * reinit if population\source was changed
		 */
		if (typeof data.source !== "undefined" && typeof  data.population !== "undefined") {
			if (data.$element.attr('data-population') != data.population || data.$element.attr('data-source') != data.source) {
				data.$element.attr('data-population', data.population);
				data.$element.attr('data-source', data.source);
				data.$element.data('selectize').destroy();
				fw_option_multi_select_extended_initialize(data.$element);
			}
		}

		data.$element.data('selectize').$control[0].className += " danger-changes";
		data.$element.data('selectize').clearOptions();
		}
	);


	fwEvents.on('fw:options:init', function (data) {
		data.$elements
			.find('.fw-option-type-multi-select-extended:not(.initialized)')
			.each(function () {
				fw_option_multi_select_extended_initialize($(this));
			});

		/*
		 * WARNING:
		 *
		 * data.$elements.find is intentionally looked up twice instead of cached
		 * this is done because when fw_option_multi_select_extended_initialize is called
		 * the selectize plugin inserts an element which copies the
		 * `fw-option-type-multi-select` class, thus making the cache invalid.
		 */
		data.$elements
			.find('.fw-option-type-multi-select-extended:not(.initialized)')
			.addClass('initialized');
	});
})(jQuery);
