// Render function called from the main page. This sets up constraints for each of the type of values ('color', 'size', etc.)
// It also sets up click handlers for each of the buttons
function wc_octa_render_product(id, data) {
	$(id).data("variations", data.variations);
	Object.keys(data.variations).forEach(function(e) {
		wc_octa_setup_constraints($(id+" #wc-octa-product-"+e+"s-group [name='wc-octa-product-"+e+"']"), e, id);
		$(id+" #wc-octa-product-"+e+"s-group label").click(function() {
			$(id+" #wc-octa-product-"+e+"s-group [name='wc-octa-product-"+e+"']").val($(this).attr("for"));
			$(id+" #wc-octa-product-"+e+"s-group label").removeClass("active");
			$(this).addClass("active");
			$(id+" #wc-octa-product-"+e+"s-group [name='wc-octa-product-"+e+"']").trigger("change");
		});
	});
}

// Setup constraints by listening to the change event on each of the product attribute value
function wc_octa_setup_constraints(element, selector, id) {
	var variations = $(id).data("variations");
	element.on("change", function(){
		var value = element.val();
		switch(selector) {
			case "color":
				var size_value = $(id+" #wc-octa-product-sizes-group [name='wc-octa-product-size']").val();
				var selected = variations[selector][value].find(function(e){ return e.size == size_value; });
				if(typeof(selected) != "undefined") {
					$(".wc-octa-product .wc-octa-product-main-image").attr("src", selected.image);
					$(".wc-octa-product .wc-octa-product-price").html(selected.price_html);
					wc_octa_update_constraints("size", id, variations[selector][value]);
				}
				else {
					wc_octa_reset_constraints("size", id, variations[selector][value]);
				}
				break;
			case "size":
				var color_value = $(id+" #wc-octa-product-colors-group [name='wc-octa-product-color']").val();
				var selected = variations[selector][value].find(function(e){ return e.color == color_value; });
				if(typeof(selected) != "undefined") {
					$(".wc-octa-product .wc-octa-product-main-image").attr("src", selected.image);
					$(".wc-octa-product .wc-octa-product-price").html(selected.price_html);
				}
				else {
					wc_octa_reset_constraints("color", id, variations[selector][value]);
				}
				break;
			default:
				break;
		}
		element.siblings("label").removeClass("active");
		element.siblings("label[for="+value+"]").addClass("active");
	});
}

function wc_octa_reset_constraints(selector, id, variations) {
	$(id+" #wc-octa-product-"+selector+"s-group label").removeClass("active");
	$(id+" #wc-octa-product-"+selector+"s-group label").removeClass("disabled");
	var valid_values = variations.map(function(e) { return e[selector];});
	$(id+" #wc-octa-product-"+selector+"s-group label").each(function(){
		if(!valid_values.includes($(this).attr("for"))) {
			$(this).addClass("disabled");
		}
	});
	$(id+" #wc-octa-product-"+selector+"s-group label:not('.disabled'):eq(0)").trigger("click");
}
function wc_octa_update_constraints(selector, id, variations) {
	$(id+" #wc-octa-product-"+selector+"s-group label").removeClass("disabled");
	var valid_values = variations.map(function(e) { return e[selector];});
	$(id+" #wc-octa-product-"+selector+"s-group label").each(function(){
		if(!valid_values.includes($(this).attr("for"))) {
			$(this).addClass("disabled");
		}
	});
}