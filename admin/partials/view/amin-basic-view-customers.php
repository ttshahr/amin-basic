<?php
 global $title;
 global $customer_list_table;
 ?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo $title; ?></h1>
    <a href="#" class="page-title-action">افزودن مشتری</a>
    <form method="GET">
		<input type="hidden" name="page" value="amin_customers_page"/>
        <?php
		$customer_list_table->views();
		$customer_list_table->search_box('جستجوی مشتری', 'customer_search');
		$customer_list_table->display();
        ?>
	</form>';
</div>';