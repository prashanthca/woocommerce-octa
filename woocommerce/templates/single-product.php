<?php
/**
 * @package WooCommerce Octa
 */
global $product;
global $product_id;
if(!is_a($product,'WC_Product')){
	$product = wc_get_product(get_the_ID());
	$product_id = "product_".$product->get_id();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html($product->get_title()); ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="https://fonts.googleapis.com/css?family=Google+Sans+Text&display=swap" rel="stylesheet"/>
	<link href="<?php echo WC_OCTA_PLUGIN_URL."assets/css/styles.css"; ?>" rel="stylesheet"/>
	<script src="https://cdn.jsdelivr.net/gh/jquery/jquery@latest/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
	<script src="<?php echo WC_OCTA_PLUGIN_URL."assets/js/script-main.js"; ?>"></script>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		<div class="container">
			<a class="navbar-brand" href="#">Octa Product Page Test</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">				
			</div>
		</div>
	</nav>
	<main class="main-content">
		<div class="wc-octa-product" id="<? echo $product_id; ?>">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<div class="wc-octa-product-main-image-container mt-3 p-4">
							<img class="wc-octa-product-main-image mx-auto d-block" src="<? echo wp_get_attachment_image_url($product->get_image_id(), "full"); ?>">
						</div>
					</div>
					<div class="col-md-6">
						<?php
							$available_variations = array_map(function ($val) {
								return array("id"=>$val["variation_id"], "image" => $val["image"]["url"], "is_in_stock" => $val["is_in_stock"], "color" => $val["attributes"]["attribute_color"], "size" => $val["attributes"]["attribute_size"], "price_html" => $val["price_html"], "hexcolor" => $val["custom_color"]);
							}, $product->get_available_variations("array"));
							$variations_data = array("variations" => array());
							foreach($product->get_attributes() as $key => $value) {
								$variations_data["variations"][$key] = array();
								foreach($value->get_options() as $option) {
									$data = array("key" => $key, "option" => $option);
									$filtered_variations = array_filter($available_variations, 
										function($v, $k) {
											global $data;
											return $v[$data["key"]] == $data["option"];
										}, ARRAY_FILTER_USE_BOTH);
									$variations_data["variations"][$key][$option] = array();
									foreach($filtered_variations as $k => $v) {
										array_push($variations_data["variations"][$key][$option], $v);
									}
								}
							}
						?>
						<div class="product-info">
							<h2><?php echo esc_html($product->get_title()); ?></h2>
						</div>
						<div class="variations-switches">
							<div id="wc-octa-product-colors">
								<div id="wc-octa-product-colors-group" role="group">
									<?php
									echo '<input type="hidden" name="wc-octa-product-color">';
									foreach($variations_data["variations"]["color"] as $k => $v) {
										echo '<label class="btn" for="'.$k.'" style="background-color: '.$v[0]["hexcolor"].';outline: none"></label>';
									}
									?>
								</div>							
							</div>
							<div id="wc-octa-product-sizes">
								<div id="wc-octa-product-sizes-group" role="group">
									<?php
									echo '<input type="hidden" name="wc-octa-product-size">';
									foreach($variations_data["variations"]["size"] as $k => $v) {
										echo '<label class="btn" for="'.$k.'">'.$k.'</label>';
									}
									?>
								</div>
							</div>
						</div>
						<span class="wc-octa-product-price"><?php echo $product->get_price_html(); ?></span>
					</div>
				</div>
			</div>
			<script type="text/javascript">
				(function($){
					var <? echo $product_id; ?>_data = <?echo json_encode($variations_data); ?>;
					wc_octa_render_product("#<? echo $product_id; ?>", <? echo $product_id; ?>_data);
				})(jQuery);
			</script>
			
		</div>
	</main>
</body>
</html>

