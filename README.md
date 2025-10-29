#ساختار درختی


amin-basic/
├── 📄 amin-basic.php                                     💡 فایل اصلی - ثبت هوک‌ها، تعریف ثابت‌ها
├── 📄 index.php                                          🔒 محافظت از پوشه - جلوگیری از دسترسی مستقیم
│
├── 📁 includes/                                          🧠 هسته اصلی افزونه
│   ├── 📄 index.php                                      🔒 محافظت از پوشه
│   ├── 📄 class-amin-basic.php                           🧠 هسته مرکزی - مدیریت وابستگی‌ها و هوک‌ها
│   ├── 📄 class-amin-basic-activator.php                 ⚡ فعال‌سازی - ایجاد جدول‌های دیتابیس
│   ├── 📄 class-amin-basic-deactivator.php               🚫 غیرفعال‌سازی - پاک‌سازی موقت
│   ├── 📄 class-amin-basic-loader.php                    🔗 مدیریت هوک‌های وردپرس
│   ├── 📄 class-amin-basic-i18n.php                      🌍 بین‌المللی‌سازی (استفاده نشده)
│   ├── 📄 class-amin-basic-category-rest.php             📦 REST API دسته‌بندی‌ها
│   ├── 📄 class-amin-basic-product-rest.php              🏷️ REST API محصولات
│   ├── 📄 class-amin-basic-customer-rest.php             👥 REST API مشتریان
│   ├── 📄 class-amin-basic-product-variation-rest.php    🔄 REST API محصولات متغیر
│   ├── 📄 class-amin-basic-attribute-rest.php            ⚙️ REST API ویژگی‌ها
│   ├── 📄 class-amin-basic-attribute-term-rest.php       🏷️ REST API مقادیر ویژگی‌ها
│   └── 📄 class-amin-basic-order-rest.php                🧾 REST API سفارشات
│
├── 📁 admin/                                             🎛️ بخش مدیریت وردپرس
│   ├── 📄 index.php                                      🔒 محافظت از پوشه
│   ├── 📄 class-amin-basic-admin.php                     🎛️ مدیریت استایل/اسکریپت‌های admin
│   ├── 📄 class-amin-basic-admin-menu.php                📋 منوهای مدیریت
│   ├── 📁 partials/
│   │   ├── 📄 index.php                                  🔒 محافظت از پوشه
│   │   ├── 📁 metabox/
│   │   │   └── 📄 amin-basic-metabox.php                 📊 متاباکس محصولات - نمایش اطلاعات امین
│   │   ├── 📁 ListTable/
│   │   │   ├── 📄 Category_List_Table.php                  نمایش اختصاصی لیست دسته بندی ها
│   │   │   ├── 📄 Customer_List_Table.php                  نمایش اختصاصی لیست مشتریان
│   │   │   └── 📄 Product_List_Table.php                   نمایش لیست محصولات
│   │   ├── 📁 widget/
│   │   │   └── 📄 amin-basic-widget.php                    خالی
│   ├── 📁 css/                                           🎨 استایل‌های مدیریت (خالی)
│   ├── 📁 js/                                            ⚡ اسکریپت‌های مدیریت (خالی)
│   └── 📁 images/                                        ⚡ تصویر
│
├── 📁 public/                                            🌐 بخش عمومی سایت
│   ├── 📄 index.php                                      🔒 محافظت از پوشه
│   ├── 📄 class-amin-basic-public.php                    🌐 ثبت REST API + فیلترهای front-end
│   ├── 📁 partials/
│   │   └── 📄 amin-basic-public-display.php              🎭 ظاهر front-end (خالی)
│   ├── 📁 css/                                           🎨 استایل‌های front-end (خالی)
│   │   └── 📄 amin-basic-public.css                      ⚡ اسکریپت‌های front-end (خالی)
│   └── 📁 js/
│       └── 📄 amin-basic-public.js                       ⚡ اسکریپت‌های front-end (خالی)
├── 📁 libs/                                              📚 کتابخانه‌های خارجی
│   └── 📄 notificator.php                                📢 سیستم ارسال نوتیفیکیشن
├── 📁 logs/                                              📝 ذخیره لاگ‌های روزانه
└── 📁 languages/


🗃️ دیتابیس‌های ایجاد شده:

📊 wp_abp_attributes
├── attribute_id (PK)                                     🔗 ارتباط با ووکامرس
├── abpCode                                               🔑 کد ویژگی در امین  
└── abpRecordId                                           🔑 شناسه رکورد در امین

📊 wp_amin_basic_new_order_statuses
├── id (PK)                                               🔑 آی‌دی رکورد
├── gateway                                               💳 درگاه پرداخت
└── order_status                                          📦 وضعیت سفارش



# افزونه اتصال ووکامرس به نرم‌افزار حسابداری امین

این افزونه یک پل ارتباطی بین فروشگاه ووکامرس و نرم‌افزار حسابداری امین ایجاد می‌کند.

## 🎯 هدف افزونه

اتصال دوطرفه بین ووکامرس و نرم‌افزار امین برای سینک خودکار داده‌ها

## 🔄 جریان داده‌ها

### ۱. جهت اصلی: امین به ووکامرس

امین → HTTP Request → WordPress → create_item() → create_product() → ووکامرس

### ۲. فیلترینگ محتوا:
- فقط محصولات و دسته‌بندی‌هایی نمایش داده می‌شوند که `abpTypeShow = 2` داشته باشند
- سیستم فیلتر خودکار در front-end و REST API

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

## ⚠️ محدودیت‌ها و پیش‌نیازها

### پیش‌نیازها:
- ووکامرس فعال شده
- REST API وردپرس فعال
- PHP 7.2 یا بالاتر

### محدودیت‌های فعلی:
- اتصال یک‌طرفه (امین → ووکامرس)
- نیاز به تنظیم دستی در نرم‌افزار امین برای ارسال درخواست‌ها

## 🐛 عیب‌یابی

### مشاهده لاگ‌ها:
- لاگ‌های روزانه در `wp-content/plugins/amin-basic/logs/`
- فرمت: `YYYY-MM-DD.txt`

### تست endpoint ها:

# ایجاد محصول
curl -X POST https://yoursite.com/wp-json/wc/api/products/create \
  -H "Content-Type: application/json" \
  -d '{"code": 123, "name": "Test Product", "typeShow": 2, "recordID": "test-123"}'


🎯 نحوه کار فایل REST محصولات:
php
class Amin_Basic_Products_Rest extends WC_REST_Products_Controller
از کلاس اصلی محصولات ووکامرس ارث‌بری کرده

endpoint های اختصاصی برای امین اضافه می‌کنه