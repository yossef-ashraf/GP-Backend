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

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Create areas
        $areas = [];
        $areaNames = ['Cairo', 'Giza', 'Alexandria', 'Mansoura', 'Aswan', 'Port Said', 'Suez', 'Luxor'];
        
        foreach ($areaNames as $name) {
            $areas[] = Area::create(['name' => $name]);
        }

        // 2. Create shipping values for each area
        foreach ($areas as $area) {
            ShippingValue::create([
                'area_id' => $area->id,
                'value' => rand(10, 30)
            ]);
        }

        // 3. Create admin user
        User::create([
            'password' => Hash::make('123'),
            'name' => 'admin',
            'role' => 'admin',
            'email' => 'admin@admin.com'
        ]);

        // 4. Create regular users
        $users = User::factory()->count(20)->create([
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
                'state' => 'Egypt',
                'zip_code' => rand(10000, 99999),
                'street' => fake()->streetName(),
                'building_number' => rand(1, 100),
                'apartment_number' => rand(1, 50)
            ]);
        }

        // 6. Create book categories (reduced to 6)
        $categories = [];
        $categoryNames = [
            'Fiction',
            'Non-Fiction',
            'Educational',
            'Children',
            'Religious',
            'History'
        ];
        
        foreach ($categoryNames as $data) {
            $categories[] = Category::create([
                'data' => $data,
                'image' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2030&auto=format&fit=crop',
            ]);
        }

        // 7. Create book products
        $bookTitles = [
            'The Great Adventure',
            'Science of Success',
            'Learning Python',
            'Ancient History',
            'Religious Studies',
            'Time Management',
            'Emotional Intelligence',
            'Modern Physics',
            'English in 30 Days',
            'Arabian Nights',
            '7 Habits of Success',
            'Art of War',
            'Think Outside the Box',
            'Les Misérables',
            'Crime and Punishment'
        ];

        $authors = [
            'John Smith',
            'Mary Johnson',
            'Robert Brown',
            'Sarah Wilson',
            'Michael Davis',
            'Emma Taylor',
            'David Anderson',
            'Stephen King',
            'Robert Kiyosaki',
            'Victor Hugo',
            'Fyodor Dostoevsky',
            'George Orwell',
            'Paulo Coelho'
        ];

        $products = [];
        foreach ($bookTitles as $index => $title) {
            $price = rand(50, 300);
            $salePrice = rand(80, 100) / 100 * $price;
            
            $product = Product::create([
                'image' => 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?q=80&w=1000',
                'author' => $authors[$index % count($authors)],
                'slug' => 'book-' . ($index + 1),
                'type' => 'simple',
                'sku' => 'BOOK-' . rand(1000, 9999),
                'price' => $price,
                'sale_price' => $salePrice,
                'stock_status' => ['in_stock', 'out_of_stock'][rand(0, 1)],
                'stock_qty' => rand(5, 100),
            ]);

            $randomCategories = collect($categories)->random(rand(1, 2));
            $product->categories()->attach($randomCategories);

            if (rand(0, 10) > 7) {
                $variations = [
                    'Paperback' => ['price_modifier' => -20, 'sku_suffix' => 'STD'],
                    'Hardcover' => ['price_modifier' => 50, 'sku_suffix' => 'DLX'],
                    'E-Book' => ['price_modifier' => -50, 'sku_suffix' => 'EBOOK'],
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

        // 8. Create coupons
        $couponCodes = ['BOOK10', 'READER20', 'WELCOME15', 'SUMMER30'];
        $discountTypes = ['percentage', 'fixed'];
        
        foreach ($couponCodes as $index => $code) {
            $discountType = $discountTypes[$index % 2];
            $discountValue = $discountType === 'percentage' ? rand(10, 30) : rand(20, 50);
            
            Coupon::create([
                'name' => 'Coupon ' . $code,
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

        // 9. Create orders (from March until now)
        $startDate = Carbon::create(2024, 3, 1);
        $endDate = now();

        foreach ($users as $user) {
            // Create 2-5 orders per user
            $orderCount = rand(2, 5);
            
            for ($i = 0; $i < $orderCount; $i++) {
                $couponId = rand(0, 10) > 7 ? Coupon::inRandomOrder()->first()?->id : null;
                $area_id = Area::inRandomOrder()->first();
                
                // Generate random date between March 1st and now
                $orderDate = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));
                
                $order = Order::create([
                    'user_id' => $user->id,
                    'area_id' => $area_id->id,
                    'notes' => 'Order Note',
                    'tracking_number' => 'TRK-' . $user->id . '-' . rand(1000, 9999),
                    'shipping_cost' => $area_id->shippingValues()->first()->value ?? 5,
                    'address' => 'Address-' . $user->id,
                    'coupon_id' => $couponId,
                    'payment_method' => ['cash', 'credit_card'][rand(0, 1)],
                    'status' => ['pre-pay', 'pending', 'completed', 'cancelled', 'payed'][rand(0, 4)],
                    'total_amount' => 0,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);

                $totalAmount = 0;
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

                if (rand(0, 1)) {
                    OrderNote::create([
                        'order_id' => $order->id,
                        'notes' => ['Order shipped', 'Processing order', 'Please contact before delivery'][rand(0, 2)],
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);
                }
            }
        }
    }
}