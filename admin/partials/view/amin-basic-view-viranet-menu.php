<?php
/**
 * صفحه منوی ویرانت با دکمه‌ها
 *
 * @package    Amin_Basic
 * @subpackage Amin_Basic/admin/partials/view
 */

// جلوگیری از دسترسی مستقیم
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// بررسی مجوزهای کاربر
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'شما مجوز دسترسی به این صفحه را ندارید.' ) );
}

// پردازش فرم اگر ارسال شده باشد
if ( isset( $_POST['amin_button_action'] ) ) {
    $action = sanitize_text_field( $_POST['amin_button_action'] );
    $nonce = isset( $_POST['amin_viranet_menu_nonce'] ) ? $_POST['amin_viranet_menu_nonce'] : '';
    
    if ( wp_verify_nonce( $nonce, 'amin_viranet_menu_action' ) ) {
        $viranet_handler = new Amin_Basic_Viranet_Menu( 'amin-basic', '1.0.0' );
        
        switch ( $action ) {
            case 'button_1':
                $result = $viranet_handler->handle_button_1_action();
                break;
            case 'button_2':
                $result = $viranet_handler->handle_button_2_action();
                break;
            default:
                $result = array(
                    'success' => false,
                    'message' => 'عملیات نامعتبر!'
                );
        }
        
        // نمایش پیام نتیجه
        if ( isset( $result ) ) {
            $notice_class = $result['success'] ? 'notice-success' : 'notice-error';
            echo '<div class="notice ' . $notice_class . '"><p>' . esc_html( $result['message'] ) . '</p></div>';
            
            // نمایش داده اضافی اگر وجود دارد
            if ( $result['success'] && isset( $result['data'] ) ) {
                echo '<div class="notice notice-info"><p>نتیجه: ' . esc_html( $result['data'] ) . '</p></div>';
            }
        }
    } else {
        echo '<div class="notice notice-error"><p>خطای امنیتی! لطفا مجدد تلاش کنید.</p></div>';
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">منوی ویرانت</h1>
    <hr class="wp-header-end">
    
    <div class="card">
        <h2 class="title">عملیات سریع ویرانت</h2>
        <p>از این بخش برای انجام عملیات مختلف ویرانت استفاده کنید:</p>
        
        <form method="post" style="margin: 20px 0;">
            <?php wp_nonce_field( 'amin_viranet_menu_action', 'amin_viranet_menu_nonce' ); ?>
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <!-- دکمه اول -->
                <button type="submit" 
                        name="amin_button_action" 
                        value="button_1" 
                        class="button button-primary"
                        style="padding: 12px 24px; font-size: 14px; height: auto;">
                    🚀 دکمه اول ویرانت
                </button>
                
                <!-- دکمه دوم -->
                <button type="submit" 
                        name="amin_button_action" 
                        value="button_2" 
                        class="button button-secondary"
                        style="padding: 12px 24px; font-size: 14px; height: auto;">
                    ⚡ دکمه دوم ویرانت
                </button>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>اطلاعات آخرین اجرا</h3>
        <ul>
            <li><strong>دکمه اول:</strong> 
                <?php echo esc_html( get_option( 'amin_viranet_button_1_last_run', 'هنوز اجرا نشده' ) ); ?>
            </li>
            <li><strong>دکمه دوم:</strong> 
                <?php echo esc_html( get_option( 'amin_viranet_button_2_last_run', 'هنوز اجرا نشده' ) ); ?>
            </li>
        </ul>
    </div>

    <div class="card">
        <h3>راهنمای منوی ویرانت</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h4>دکمه اول</h4>
                <p>برای انجام عملیات خاص اول استفاده می‌شود.</p>
                <ul>
                    <li>شمارش محصولات</li>
                    <li>بررسی موجودی</li>
                    <li>و سایر عملیات...</li>
                </ul>
            </div>
            <div>
                <h4>دکمه دوم</h4>
                <p>برای انجام عملیات خاص دوم استفاده می‌شود.</p>
                <ul>
                    <li>شمارش کاربران</li>
                    <li>بررسی سفارشات</li>
                    <li>و سایر عملیات...</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    margin: 20px 0;
    padding: 25px;
    border: 1px solid #ccd0d4;
    border-radius: 6px;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.card h2, .card h3, .card h4 {
    margin-top: 0;
    color: #1d2327;
}
.button {
    transition: all 0.3s ease;
    min-width: 160px;
    border-radius: 4px;
    font-weight: 600;
}
.button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>