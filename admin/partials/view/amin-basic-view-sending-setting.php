<h1><?php echo get_admin_page_title(); ?></h1>
<?php   
    if( isset( $_POST['saveSendingSetting'] ) ){

        update_option( 'notUpdateNameKala', $_POST['notUpdateNameKala'] );
        update_option( 'notUpdateNameGroupKala', $_POST['notUpdateNameGroupKala'] );

        echo '<script type="text/javascript"> alert("تنظیمات ذخیره شد") </script>'; 
    }
    
    $notUpdateNameKala = get_option('notUpdateNameKala');       
    $notUpdateNameGroupKala = get_option('notUpdateNameGroupKala');
    
?>

<form action="" method="POST">
    
    <label for="notUpdateNameKala">در ارسال کالاهای اصلاحی به سایت، نام کالا اصلاح نشود.</label>
    <input type="checkbox" name="notUpdateNameKala" id="notUpdateNameKala" 
        <?php if ($notUpdateNameKala == 'on') { echo 'checked';} ?>>
    <br/>
    <br/>
    <label for="notUpdateNameGroupKala">در ارسال گروه کالاهای اصلاحی به سایت، نام گروه کالا اصلاح نشود.</label>
    <input type="checkbox" name="notUpdateNameGroupKala" id="notUpdateNameGroupKala" 
        <?php if ($notUpdateNameGroupKala == 'on') { echo 'checked';} ?>>
    <br/>
    <br/>
    <button type="submit" name="saveSendingSetting" class="button button-primary" >ذخیره تنظیمات</button>

</form>
