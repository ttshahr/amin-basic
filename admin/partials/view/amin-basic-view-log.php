<h1><?php echo get_admin_page_title(); ?></h1>
<?php
    if( isset( $_POST['deletelogfile'] ) ){
        $count = delete_log();
        echo '<script type="text/javascript"> alert("تعداد '. $count .' فایل حذف شد") </script>'; 
        exit;
    }
?>
<pre>
    <form action="" method="POST">
        <button type="submit" name="deletelogfile" class="button button-primary" >حذف لاگ</button>
        <textarea style="width: 800px;height: 600px;direction: ltr;">
        <?php
            read_log();
        ?>
        </textarea>
    </form>
</pre>