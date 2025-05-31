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
use Carbon\Carbon;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_EG');

        // 1. Create areas
        $areaNames = ['القاهرة', 'الجيزة', 'الإسكندرية', 'المنصورة', 'أسوان', 'بورسعيد', 'السويس', 'الأقصر'];
        $areas = collect($areaNames)->map(function ($name) {
            return Area::create(['name' => $name]);
        });

        // 2. Create shipping values for each area
        $areas->each(function ($area) {
            ShippingValue::create([
                'area_id' => $area->id,
                'value' => rand(10, 30)
            ]);
        });

        // 3. Create admin user
        User::create([
            'password' => Hash::make('123'),
            'name' => 'admin',
            'role' => 'admin',
            'email' => 'admin@admin.com'
        ]);

        // 4. Create regular users
        $users = User::factory()->count(20)->create()->each(function ($user) use ($faker, $areas) {
            $user->update([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'gender' => $faker->randomElement(['male', 'female']),
                'date_of_birth' => $faker->date(),
                'password' => Hash::make('password'),
            ]);

            // 5. Create address for user
            Address::create([
                'user_id' => $user->id,
                'area_id' => $areas->random()->id,
                'state' => 'مصر',
                'zip_code' => rand(10000, 99999),
                'street' => $faker->streetName,
                'building_number' => rand(1, 100),
                'apartment_number' => rand(1, 50)
            ]);
        });

        // 6. Create book categories
        $categoryNames = [
            'روايات',
            'كتب دينية',
            'أدب عربي',
            'أدب عالمي',
            'كتب أطفال',
            'تاريخ',
            'علوم',
            'تنمية بشرية'
        ];
        $categories = collect($categoryNames)->map(function ($name) {
            return Category::create([
                'data' => $name,
                'image' => 'categories/default.jpg'
            ]);
        });

        // 7. Create book products
        $bookTitles = [
            'أولاد حارتنا',
            'الخبز الحافي',
            'في قلبي أنثى عبرية',
            'قواعد العشق الأربعون',
            'الأسود يليق بك',
            'رجال في الشمس',
            'اللص والكلاب',
            'الطريق',
            'زقاق المدق',
            'السر',
            'العادات السبع للناس الأكثر فعالية',
            'الرجال من المريخ والنساء من الزهرة',
            'مئة عام من العزلة',
            'الخيميائي',
            '1984'
        ];

        $authors = [
            'نجيب محفوظ',
            'محمد شكري',
            'خولة حمدي',
            'إليف شافاق',
            'أحلام مستغانمي',
            'غسان كنفاني',
            'ستيفن كوفي',
            'جورج أورويل',
            'باولو كويلو',
            'غابرييل غارسيا ماركيز',
            'جون غراي'
        ];

        $products = collect($bookTitles)->map(function ($title, $index) use ($authors, $categories) {
            $price = rand(50, 300);
            $salePrice = rand(80, 100) / 100 * $price;

            $product = Product::create([
                'image' => 'products/default.jpg',
                'author' => $authors[$index % count($authors)],
                'slug' => 'book-' . ($index + 1),
                'type' => 'simple',
                'sku' => 'BOOK-' . rand(1000, 9999),
                'price' => $price,
                'sale_price' => $salePrice,
                'stock_status' => ['in_stock', 'out_of_stock'][rand(0, 1)],
                'stock_qty' => rand(5, 100),
            ]);

            $product->categories()->attach($categories->random(rand(1, 2)));

            if (rand(0, 10) > 7) {
                $variations = [
                    'غلاف عادي' => ['price_modifier' => -20, 'sku_suffix' => 'STD'],
                    'غلاف فاخر' => ['price_modifier' => 50, 'sku_suffix' => 'DLX'],
                    'كتاب إلكتروني' => ['price_modifier' => -50, 'sku_suffix' => 'EBOOK'],
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

            return $product;
        });

        // 8. Create coupons
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

        // 9. Create orders
        $startDate = Carbon::create(2024, 3, 1);
        $endDate = now();

        $users->each(function ($user) use ($products, $startDate, $endDate, $areas) {
            $orderCount = rand(2, 5);

            for ($i = 0; $i < $orderCount; $i++) {
                $couponId = rand(0, 10) > 7 ? Coupon::inRandomOrder()->first()?->id : null;
                $area = $areas->random();

                $orderDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));

                $order = Order::create([
                    'user_id' => $user->id,
                    'area_id' => $area->id,
                    'notes' => 'ملاحظة الطلب',
                    'tracking_number' => 'TRK-' . $user->id . '-' . rand(1000, 9999),
                    'shipping_cost' => $area->shippingValues()->first()->value ?? 5,
                    'address' => 'عنوان-' . $user->id,
                    'coupon_id' => $couponId,
                    'payment_method' => ['cash', 'credit_card'][rand(0, 1)],
                    'status' => ['pre-pay', 'pending', 'completed', 'cancelled', 'payed'][rand(0, 4)],
                    'total_amount' => 0,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                $totalAmount = 0;
                $orderBooksCount = rand(1, 5);
                $selectedProducts = $products->random($orderBooksCount);

                foreach ($selectedProducts as $product) {
                    $variation = $product->variations()->exists() && rand(0, 1) ? $product->variations()->inRandomOrder()->first() : null;

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
                        'variation_data' => null,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);

                    $totalAmount += $itemTotal;
                }

                if ($order->coupon_id) {
                    $coupon = Coupon::find($order->coupon_id);
                    if ($coupon->discount_type === 'percentage') {
                        $totalAmount *= (1 - ($coupon->discount_value / 100));
                    } else {
                        $totalAmount = max(0, $totalAmount - $coupon->discount_value);
                    }
                }

                $order->update([
                    'total_amount' => $totalAmount,
                    'updated_at' => $orderDate,
                ]);

                //  if (rand(0, 1)) {
                //     OrderNote::create([
                //     'order_id' => $order->id,
                //     'note' => 'هذا طلب يحتوي على كتب مميزة جداً!',
                //     'created_at' => $orderDate->copy()->addMinutes(rand(1, 60)),
                //     'updated_at' => $orderDate->copy()->addMinutes(rand(1, 60)),
                // ]);

                // }
            }
        });
    }
}

