<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Area;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderNote;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ShippingValue;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create areas
        $areas = [];
        $areaNames = ['القاهرة', 'الجيزة', 'الإسكندرية', 'المنصورة', 'أسوان'];
        
        foreach ($areaNames as $name) {
            $areas[] = Area::create(['name' => $name]);
        }

        // 2. Create shipping values for each area
        foreach ($areas as $area) {
            ShippingValue::create([
                'area_id' => $area->id,
                'value' => rand(10, 30) // تكلفة شحن معقولة للكتب
            ]);
        }

        // 3. Create admin user
        User::create([
            'password' => Hash::make('123'),
            'name' => 'admin',
            'email' => 'admin@admin.com'
        ]);

        // 4. Create regular users
        $users = User::factory()->count(5)->create([
            'password' => Hash::make('password'),
            'name' => fn() => fake()->name(),
            'email' => fn() => fake()->unique()->safeEmail(),
            'phone' => fn() => fake()->phoneNumber(),
            'gender' => fn() => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fn() => fake()->date(),
        ]);

        // 5. Create addresses for users
        foreach ($users as $user) {
            Address::create([
                'user_id' => $user->id,
                'area_id' => $areas[array_rand($areas)]->id,
                'state' => 'مصر',
                'zip_code' => rand(10000, 99999),
                'street' => fake()->streetName(),
                'building_number' => rand(1, 100),
                'apartment_number' => rand(1, 50)
            ]);
        }

        // 6. Create book categories
        $categories = [];
        $categoryNames = [
            'روايات عربية',
            'روايات مترجمة',
            'كتب أطفال',
            'كتب تعليمية',
            'كتب دينية',
            'كتب تاريخية',
            'كتب علمية',
            'كتب تنمية بشرية',
            'كتب سياسية'
        ];
        
        foreach ($categoryNames as $data) {
            $categories[] = Category::create(['data' => $data]);
        }

        // 7. Create subcategories
        $subcategories = [
            'روايات عربية' => ['روايات بوليسية', 'روايات رومانسية', 'روايات خيال علمي'],
            'روايات مترجمة' => ['روايات إنجليزية', 'روايات فرنسية', 'روايات روسية'],
            'كتب أطفال' => ['قصص مصورة', 'قصص تعليمية', 'ألعاب ذكاء'],
            'كتب تعليمية' => ['مناهج دراسية', 'لغات أجنبية', 'برمجة وتكنولوجيا']
        ];

        $categoryMap = collect($categories)->mapWithKeys(function ($category) {
            return [$category->data => $category];
        });

        foreach ($subcategories as $parentName => $subNames) {
            $parent = $categoryMap[$parentName];
            foreach ($subNames as $subName) {
                Category::create([
                    'parent_id' => $parent->id,
                    'data' => $subName
                ]);
            }
        }

        // 8. Create book products
        $bookTitles = [
            'عودة للزمن الجميل',
            'رحلة إلى المريخ',
            'أساسيات البرمجة بلغة بايثون',
            'تاريخ مصر القديمة',
            'قصص الأنبياء',
            'فن إدارة الوقت',
            'الذكاء العاطفي',
            'مبادئ الفيزياء الحديثة',
            'تعلم اللغة الإنجليزية في 30 يوم',
            'ألف ليلة وليلة',
            'العادات السبع للناس الأكثر فعالية',
            'فن الحرب',
            'التفكير خارج الصندوق',
            'رواية البؤساء',
            'رواية الجريمة والعقاب'
        ];

        $authors = [
            'نجيب محفوظ',
            'طه حسين',
            'أحمد خالد توفيق',
            'يوسف زيدان',
            'إبراهيم عيسى',
            'علاء الأسواني',
            'غسان كنفاني',
            'ستيفن كوفي',
            'روبرت كيوساكي',
            'فيكتور هوجو',
            'فيودور دوستويفسكي',
            'جورج أورويل',
            'باولو كويلو'
        ];

        $products = [];
        foreach ($bookTitles as $index => $title) {
            $price = rand(50, 300);
            $salePrice = rand(80, 100) / 100 * $price; // 80-100% of original price
            
            $product = Product::create([
                'slug' => 'book-' . ($index + 1),
                'type' => 'simple', // معظم الكتب بسيطة وليست متغيرة
                'sku' => 'BOOK-' . rand(1000, 9999),
                'price' => $price,
                'sale_price' => $salePrice,
                'stock_status' => ['in_stock', 'out_of_stock'][rand(0, 1)],
                'stock_qty' => rand(5, 100),
            ]);

            // إضافة بيانات إضافية للكتاب (يمكن تخزينها في حقل الوصف أو في جدول منفصل)
            // هنا نفترض أن هناك حقل للوصف في جدول المنتجات
            $authorIndex = $index % count($authors);
            $description = 'المؤلف: ' . $authors[$authorIndex] . '\n';
            $description .= 'عدد الصفحات: ' . rand(100, 500) . '\n';
            $description .= 'سنة النشر: ' . rand(2000, 2023) . '\n';
            $description .= 'الناشر: دار النشر للكتب العربية\n';
            $description .= 'وصف الكتاب: هذا الكتاب يتناول موضوعات متنوعة تهم القارئ العربي.';
            
            // إذا كان هناك حقل للوصف في جدول المنتجات
            // $product->update(['description' => $description]);

            // ربط المنتج بفئة أو أكثر
            $randomCategories = collect($categories)->random(rand(1, 2));
            $product->categories()->attach($randomCategories);

            // إنشاء متغيرات للكتب التي لها إصدارات مختلفة (مثل غلاف عادي وفاخر)
            if (rand(0, 10) > 7) { // 30% من الكتب لها إصدارات متعددة
                $variations = [
                    'غلاف عادي' => ['price_modifier' => -20, 'sku_suffix' => 'STD'],
                    'غلاف فاخر' => ['price_modifier' => 50, 'sku_suffix' => 'DLX'],
                    'نسخة إلكترونية' => ['price_modifier' => -50, 'sku_suffix' => 'EBOOK'],
                ];

                foreach ($variations as $name => $data) {
                    ProductVariation::create([
                        'product_id' => $product->id,
                        'slug' => $product->slug . '-' . strtolower(str_replace(' ', '-', $name)),
                        'price' => $product->price + $data['price_modifier'],
                        'sale_price' => $salePrice + $data['price_modifier'] * 0.9,
                        'sku' => $product->sku . '-' . $data['sku_suffix'],
                     
                        'stock_status' => $product->stock_status,
                        'stock_qty' => rand(5, 30),

                    ]);
                }
            }

            $products[] = $product;
        }

        // 9. Create coupons for bookstore
        $couponCodes = ['BOOK10', 'READER20', 'WELCOME15', 'SUMMER30'];
        $discountTypes = ['percentage', 'fixed'];
        
        foreach ($couponCodes as $index => $code) {
            $discountType = $discountTypes[$index % 2];
            $discountValue = $discountType === 'percentage' ? rand(10, 30) : rand(20, 50);
            
            Coupon::create([
                'name' => 'كوبون ' . $code,
                'code' => $code,
                'discount_value' => $discountValue,
                'discount_type' => $discountType,
                'valid_from' => now(),
                'valid_to' => now()->addMonths(rand(1, 3)),
                'is_active' => true,
                'usage_limit' => rand(10, 100),
                'usage_count' => 0,
                'min_order_amount' => rand(100, 200)
            ]);
        }

        // 10. Create orders
        foreach ($users as $user) {
            for ($i = 1; $i <= rand(1, 3); $i++) {
                // اختيار كوبون عشوائي أحيانًا
                $couponId = rand(0, 10) > 7 ? Coupon::inRandomOrder()->first()?->id : null;
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'address_id' => $user->addresses->first()->id,
                    'coupon_id' => $couponId,
                    'payment_method' => ['cash', 'credit_card'][rand(0, 1)],
                    'status' => ['pre-pay', 'pending', 'completed', 'cancelled', 'payed'][rand(0, 4)],
                    'total_amount' => 0, // سيتم تحديثه لاحقًا
                ]);

                $totalAmount = 0;

                // إضافة عناصر الطلب (الكتب)
                $orderBooksCount = rand(1, 5);
                $selectedProducts = collect($products)->random($orderBooksCount);
                
                foreach ($selectedProducts as $product) {
                    $variation = null;
                    if ($product->variations && $product->variations->count() > 0 && rand(0, 1)) {
                        $variation = $product->variations->random();
                    }
                    
                    $quantity = rand(1, 3);
                    $price = $variation ? $variation->price : $product->price;
                    $itemTotal = $price * $quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variation_id' => $variation?->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total_amount' => $itemTotal,
                        'variation_data' => null
                    ]);

                    $totalAmount += $itemTotal;
                }

                // تطبيق خصم الكوبون إذا وجد
                if ($order->coupon_id) {
                    $coupon = Coupon::find($order->coupon_id);
                    if ($coupon->discount_type === 'percentage') {
                        $totalAmount *= (1 - ($coupon->discount_value / 100));
                    } else {
                        $totalAmount = max(0, $totalAmount - $coupon->discount_value);
                    }
                }

                $order->update(['total_amount' => $totalAmount]);

                // إضافة ملاحظات للطلب
                if (rand(0, 1)) {
                    OrderNote::create([
                        'order_id' => $order->id,
                        'notes' => ['تم شحن الكتب', 'جاري تجهيز الطلب', 'يرجى الاتصال قبل التوصيل'][rand(0, 2)]
                    ]);
                }
            }
        }
    }
}