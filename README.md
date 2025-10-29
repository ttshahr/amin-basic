amin-basic/
├── 📄 amin-basic.php (فایل اصلی)
├── 📁 includes/
│   ├── 📄 class-amin-basic.php (کلاس اصلی)
│   ├── 📄 class-amin-basic-activator.php
│   ├── 📄 class-amin-basic-deactivator.php
│   ├── 📄 class-amin-basic-loader.php
│   ├── 📄 class-amin-basic-i18n.php
│   ├── 📄 class-amin-basic-category-rest.php
│   ├── 📄 class-amin-basic-product-rest.php
│   ├── 📄 class-amin-basic-customer-rest.php
│   ├── 📄 class-amin-basic-product-variation-rest.php
│   ├── 📄 class-amin-basic-attribute-rest.php
│   ├── 📄 class-amin-basic-attribute-term-rest.php
│   └── 📄 class-amin-basic-order-rest.php
├── 📁 admin/
│   ├── 📄 class-amin-basic-admin.php
│   ├── 📄 class-amin-basic-admin-menu.php
│   ├── 📁 partials/
│   │   └── 📁 metabox/
│   │       └── 📄 amin-basic-metabox.php
│   ├── 📁 css/
│   └── 📁 js/
├── 📁 public/
│   ├── 📄 class-amin-basic-public.php
│   ├── 📁 css/
│   └── 📁 js/
├── 📁 libs/
│   └── 📄 notificator.php
├── 📁 logs/ (ایجاد شده هنگام فعالیت)
└── 📁 languages/


🗃️ دیتابیس‌های ایجاد شده:

wp_abp_attributes
├── attribute_id (int, PK)
├── abpCode (int) 
└── abpRecordId (varchar(36))

wp_amin_basic_new_order_statuses
├── id (int, PK)
├── gateway (varchar(255))
└── order_status (varchar(255))



# افزونه اتصال ووکامرس به نرم‌افزار حسابداری امین

این افزونه یک پل ارتباطی بین فروشگاه ووکامرس و نرم‌افزار حسابداری امین ایجاد می‌کند.

## 🎯 هدف افزونه

اتصال دوطرفه بین ووکامرس و نرم‌افزار امین برای سینک خودکار داده‌ها

## 🔄 جریان داده‌ها

### ۱. جهت اصلی: امین → ووکامرس



**داده‌های منتقل شده:**
- 🏷️ **محصولات** (ایجاد، ویرایش، حذف، تنظیم موجودی)
- 📦 **دسته‌بندی‌ها**
- 👥 **مشتریان** 
- 🧾 **سفارشات**
- ⚙️ **ویژگی‌ها و مقادیر محصولات**

### ۲. جهت معکوس: ووکامرس → امین
(احتمالاً از طریق متاباکس مدیریتی)

## 📡 endpoint های REST API


POST /wc/api/products/create # ایجاد محصول
POST /wc/api/products/edit # ویرایش محصول
DELETE /wc/api/products/delete # حذف محصول
POST /wc/api/products/setQuantity # تنظیم موجودی

POST /wc/api/categories/[endpoints] # مدیریت دسته‌بندی‌ها
POST /wc/api/customers/[endpoints] # مدیریت مشتریان
POST /wc/api/orders/[endpoints] # مدیریت سفارشات



## 🔑 سیستم شناسایی

افزونه از ۳ شناسه برای سینک استفاده می‌کند:

1. **`abpCode`** - کد اصلی در نرم‌افزار امین
2. **`abpRecordId`** - شناسه یکتای رکورد
3. **`_sku`** - کد فنی در ووکامرس

## 🏗️ معماری افزونه

### ساختار MVC-like:
- **Model**: کلاس‌های REST (مدیریت داده)
- **View**: متاباکس‌ها و ویجت‌های مدیریتی  
- **Controller**: کلاس اصلی و Loader

### کامپوننت‌های اصلی:
- **Amin_Basic** - هسته مرکزی
- **Amin_Basic_Loader** - مدیریت هوک‌ها
- **Amin_Basic_Admin** - بخش مدیریت
- **Amin_Basic_Public** - بخش عمومی و REST API

## ⚙️ ویژگی‌های فنی

- ✅ سازگار با استانداردهای وردپرس
- ✅ استفاده از WC_REST_Controller برای API
- ✅ سیستم لاگ‌گیری پیشرفته
- ✅ مدیریت خطاهای اختصاصی
- ✅ پشتیبانی از محصولات ساده و متغیر
- ✅ فیلتر کردن محتوای اختصاصی

## 🔍 مشکلات شناسایی شده

### فوری:
1. **خطای تایپو در متاباکس** - متغیر `$inbox` به جای `$inBox`
2. **کلید متا با حروف بزرگ** - `abpRecordId` باید به حروف کوچک تبدیل شود
3. **مقدار typeShow** - بررسی تبدیل ناخواسته به 1

### ساختاری:
1. **نام تابع `awdw`** در فایل اصلی - باید به نام معنادار تغییر کند
2. **خطای منطقی در اجرا** - تابع `run_amin_basic()` خارج از هوک

## 🚀 نصب و راه‌اندازی

1. آپلود افزونه در پوشه `wp-content/plugins/`
2. فعال‌سازی از پیشخوان وردپرس
3. پیکربندی از طریق منوی "امین بیسیک"

## 📝 لاگ‌گیری

افزونه به صورت خودکار لاگ روزانه در پوشه `logs/` ایجاد می‌کند.

---

## توسعه‌یافته بر اساس نسخه اصلی علی جانثناری - توسعه ادامه توسط ویرانت ##