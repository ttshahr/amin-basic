<?php
 global $title;
 global $category_list_table;
 ?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo $title; ?></h1>
    <a href="#" class="page-title-action">افزودن دسته بندی</a>
    <form method="GET">
		<input type="hidden" name="page" value="amin_categories_page"/>
        <?php
		$category_list_table->views();
		$category_list_table->search_box('جستجوی دسته بندی', 'category_search');
		$category_list_table->display();
        ?>
	</form>';
</div>';