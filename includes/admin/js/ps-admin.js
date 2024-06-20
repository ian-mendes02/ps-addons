(function($) {
	function list_item(product) {
		return $(`
			<div class='ps-list-item ps-product-option' data-product-id="${product.id}" data-product-slug="${product.slug}" data-product-sku="${product.sku}" data-product-name="${product.name}">
				<div class='ps-img-container' style="background-image:url(${product.img_url});"></div>
				<p>${product.name}</p>
			</div>
		`);
	}

	function product_tile(product) {
		return $(`
			<div class='ps-included-product' data-product-id="${product.id}" data-product-slug="${product.slug}" >
				<span>${product.name}</span>
				<div class="ps-remove-selected-product">
					<svg xmlns="http://www.w3.org/2000/svg" width="12px" height="12px" viewBox="0 0 512 512"><path fill="#ffffff" d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c-9.4 9.4-9.4 24.6 0 33.9l47 47-47 47c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l47-47 47 47c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-47-47 47-47c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-47 47-47-47c-9.4-9.4-24.6-9.4-33.9 0z"/></svg>
				</div>
			</div>
		`);
	}

	function parse_wp_ajax(response) {
		return JSON.parse(response.slice(0, -1));
	}

	function print_status(style, message) {
		const markers = {warning: {icon: '&#9888;', color: '#ffba00'}, info: {icon: '&#x2714;', color: '#0986bf'}, error: {icon: '$#x2755;', color: 'rgb(238,44,130)'}};
		$('#ps-status-message-container').append($(`
	<div class="ps-status-message" style="background-color:${markers[style].color};">
		${markers[style].icon + ' ' + message}.
	</div>
`));
	}

	function handle_submit_discount(e) {

		e.preventDefault();
		$('#ps-status-message-container').empty();

		var name = $('#name')?.val();
		var type = $('#type')?.val();
		var value = $('#value')?.val().replace(',', '.');
		var included_products = window.ps_selected_products || {};

		if (!name || name == '') print_status('warning', 'O campo \'Nome\' não pode ficar em branco');
		else if (!type || type == '') print_status('warning', 'O campo \'Tipo\' não pode ficar em branco');
		else if (!value || value == '') print_status('warning', 'O campo \'Valor\' não pode ficar em branco');
		else if (!included_products || included_products == {}) print_status('warning', 'Nenhum produto foi selecionado');
		else $.post(ps_ajax.ajax_url, {
			action: 'update_discounts',
			data: {
				name: name,
				type: type,
				value: value,
				included_products: JSON.stringify(included_products)
			},
		}, () => location.reload());
	}

	function remove_selected_product(e) {
		var product = $(e.currentTarget).parent();
		var slug = product.attr('data-product-slug').replace('-', '_');
		if (window.ps_selected_products && window.ps_selected_products[slug]) {
			delete window.ps_selected_products[slug];
		}
		product.remove();
	}

	function handle_product_select(e) {
		if (!window['ps_selected_products']) {
			window['ps_selected_products'] = {};
		}
		let product = $(e.currentTarget);
		let product_data = {
			id: product.attr('data-product-id'),
			slug: product.attr('data-product-slug'),
			sku: product.attr('data-product-sku'),
			name: product.attr('data-product-name')
		};
		var _slug = product_data.slug.replace('-', '_');
		if (!window.ps_selected_products[_slug]) {
			window.ps_selected_products[_slug] = product_data;
			$('#ps-selected-products').append(product_tile(product_data));
			$('.ps-remove-selected-product').on('click', remove_selected_product);
		}
	}

	function handle_search_input() {

		$('#ps-products-container').empty();

		if ($('#product-name-input').val().length > 2) {
			$.post(ps_ajax.ajax_url, {
				action: 'product_lookup',
				query: $('#product-name-input').val(),
				limit: 5
			}, null).done((res) => {
				var results = parse_wp_ajax(res);
				for (let result of results) $('#ps-products-container').append(list_item(result));
				$('.ps-product-option').on('click', handle_product_select);
			});
		}
	}

	function delete_discount(e) {
		var discount_id = $(e.target).closest('.ps-discount-tile')[0].getAttribute('data-discount-id');
		$.post(ps_ajax.ajax_url, {
			action: 'delete_discount',
			discount_id: discount_id,
		}, () => location.reload());
	}

	$('#ps-member-discounts').on('submit', handle_submit_discount);
	$('#product-name-input').on('input', handle_search_input);
	$('.ps-button.ps-delete-discount').on('click', delete_discount);

}(jQuery));