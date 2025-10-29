<?php
 global $title;
 global $product_list_table;
 ?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo $title; ?></h1>
    <a href="#" class="page-title-action">افزودن محصول</a>
    <form method="GET">
		<input type="hidden" name="page" value="amin_products_page"/>
        <?php
		$product_list_table->views();
		$product_list_table->search_box('جستجوی محصول', 'product_search');
		$product_list_table->display();
        ?>
	</form>';
</div>';