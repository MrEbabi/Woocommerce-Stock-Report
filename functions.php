if( !function_exists('show_specific_product_quantity') ) {

    function show_specific_product_quantity( $atts ) {

        // Shortcode Attributes
        $atts = shortcode_atts(
            array(
                'id' => '', // Product ID argument
            ),
            $atts,
            'product_qty'
        );

        if( empty($atts['id'])) return;

        $stock_quantity = 0;

        $product_obj = wc_get_product( intval( $atts['id'] ) );
        $stock_quantity = $product_obj->get_stock_quantity();

        if( $stock_quantity > 0 ) return $stock_quantity;
		else return 0;

    }

    add_shortcode( 'product_qty', 'show_specific_product_quantity' );

}

add_action( 'admin_menu', 'my_admin_menu' );

function my_admin_menu() {
	add_menu_page( 'Detailed Stock Reports', 'Stock', 'manage_options', 'myplugin/myplugin-admin-page.php', 'myplguin_admin_page', 'dashicons-tickets', 51  );
}

function myplguin_admin_page(){
	?>
	<style>
	#stocks {
	  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
	  border-collapse: collapse;
	  width: 66%;
	}

	#stocks td, #stocks th {
	  border: 1px solid #ddd;
	  padding: 8px;
	}

	#stocks tr:nth-child(even){background-color: #f2f2f2;}

	#stocks tr:hover {background-color: #ddd;}

	#stocks th {
	  padding-top: 12px;
	  padding-bottom: 12px;
	  text-align: left;
	  background-color: #4CAF50;
	  color: white;
	}
	</style>
	<div class="wrap">
		<h2>Stock Quantity and Stock-In-Trade Report</h2>
		<br>
	</div>
	<?php
//the products that will be shown in the table are limited by 100. You may change this value to extend the table.
	$args = array(
		'limit' => 100,
		'stock_status' => "instock"
	);
	$products = wc_get_products( $args );
	$productSum = count($products);
	$stockSum = 0;
	$variationSum = 0;
	echo "<h3>Total Products: $productSum </h3>";
	echo "<table id='stocks'>";
	echo "<tr>";
	echo "<th>";
	echo "Product Name";
	echo "</th>";
	echo "<th>";
	echo "Stock Quantity";
	echo "</th>";
	echo "<th>";
	echo "Stock-In-Trade";
	echo "</th>";
	foreach($products as $product)
	{
		if($product->is_type('simple'))
		{
			$stock = $product->stock;
			$price = $product->price;
			$stockPrice = $price * $stock;
			$stockSum += $stockPrice;
			$variationSum += $stock;
			echo "<tr>";
			echo "<td>";
			echo $product->name;
			echo "</td>";
			echo "<td>";
			echo $stock;
			echo "</td>";
			echo "<td>";
			echo "$stockPrice £";
			echo "</td>";
		}
		else
		{
			$variations = $product->get_children();	
			foreach($variations as $variation){
				$stock = do_shortcode( '[product_qty id="'.$variation.'"]' );
				$price = $product->price;
				$stockPrice = $price * $stock;
				$stockSum += $stockPrice;
				$variationSum++;
				echo "<tr>";
				echo "<td>";
				echo get_the_title($variation);
				echo "</td>";
				echo "<td>";
				echo $stock;
				echo "</td>";
				echo "<td>";
				echo "$stockPrice £";
				echo "</td>";
			}
		}
		echo "</tr>";
	}
	echo "<td>";
	echo "<center><b> * TOTAL : *</b></center>";
	echo "</td>";
	echo "<td>";
	echo "<b> $variationSum </b>";
	echo "</td>";
	echo "<td>";
	echo "<b> $stockSum £</b>";
	echo "</td>";
	echo "</table>";
}
