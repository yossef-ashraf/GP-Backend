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
                'value' => rand(10, 50)
            ]);
        }

            User::create([
            'password' => Hash::make('123'),
            'name' => 'admin',
            'email' => 'admin@admin.com'
        ]);

        // 3. Create users
        $users = User::factory()->count(5)->create([
            'password' => Hash::make('password'),
            'name' => fn() => fake()->name(),
            'email' => fn() => fake()->unique()->safeEmail(),
            'phone' => fn() => fake()->phoneNumber(),
            'gender' => fn() => fake()->randomElement(['male', 'female']),
            'date_of_birth' => fn() => fake()->date(),
        ]);

        // 4. Create addresses for users
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

        // 5. Create categories
        $categories = [];
        $categoryNames = ['إلكترونيات', 'ملابس', 'أثاث', 'أطعمة', 'كتب'];
        
        foreach ($categoryNames as $data) {
            $categories[] = Category::create(['data' => $data]);
        }

        // 6. Create products
        $products = [];
        for ($i = 1; $i <= 10; $i++) {
            $product = Product::create([
                'slug' => 'product-' . $i,
                'type' => ['simple', 'variable'][rand(0, 1)],
                'sku' => 'SKU-' . rand(1000, 9999),
                'price' => rand(100, 1000),
                'sale_price' => rand(50, 900),
                'stock_status' => ['in_stock', 'out_of_stock'][rand(0, 1)],
                'stock_qty' => rand(0, 100),
            ]);

            // Convert array to collection and get random categories
            $randomCategories = collect($categories)->random(rand(1, 3));
            $product->categories()->attach($randomCategories);

            // Create variations if product is variable
            if ($product->type === 'variable') {
                for ($j = 1; $j <= rand(1, 3); $j++) {
                    ProductVariation::create([
                        'product_id' => $product->id,
                        'slug' => $product->slug . '-variation-' . $j,
                        'regular_price' => $product->price + rand(-50, 50),
                        'sale_price' => $product->sale_price + rand(-30, 30),
                        'sku' => $product->sku . '-VAR-' . $j,
                        'manage_stock' => 'yes',
                        'stock_status' => $product->stock_status,
                        'stock_qty' => rand(0, 50),
                        'total_sales' => 0,
                        'backorder_limit' => rand(5, 20),
                        // 'variation_data' => [
                        //     'color' => ['أحمر', 'أزرق', 'أخضر'][rand(0, 2)],
                        //     'size' => ['صغير', 'متوسط', 'كبير'][rand(0, 2)]
                        // ]
                    ]);
                }
            }

            $products[] = $product;
        }

        // 8. Create orders
        foreach ($users as $user) {
            for ($i = 1; $i <= rand(1, 3); $i++) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'address_id' => $user->addresses->first()->id,
                    'coupon_id' =>  null,
                    'payment_method' => ['cash' => 1, 'credit_card' => 2][['cash', 'credit_card'][rand(0, 1)]],
                    'status' => [
                        'pre-pay' => 1, 
                        'pending' => 2, 
                        'completed' => 3, 
                        'cancelled' => 4, 
                        'payed' => 5
                    ][['pre-pay', 'pending', 'completed', 'cancelled', 'payed'][rand(0, 4)]],
                    'total_amount' => 0, // Will be updated later
                ]);

                $totalAmount = 0;

                // Add order items
                for ($j = 1; $j <= rand(1, 5); $j++) {
                    $product = $products[array_rand($products)];
                    $variation = $product->variations && $product->variations->isNotEmpty() ? 
                        $product->variations->random() : null;
                    
                    $quantity = rand(1, 3);
                    $price = $variation ? $variation->sale_price : $product->price;
                    $itemTotal = $price * $quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variation_id' => $variation?->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total_amount' => $itemTotal,
                        'variation_data' => $variation?->variation_data
                    ]);

                    $totalAmount += $itemTotal;
                }

                // Apply coupon discount if exists
                if ($order->coupon) {
                    if ($order->coupon->discount_type === 'percentage') {
                        $totalAmount *= (1 - ($order->coupon->discount_value / 100));
                    } else {
                        $totalAmount = max(0, $totalAmount - $order->coupon->discount_value);
                    }
                }

                $order->update(['total_amount' => $totalAmount]);

                // Add order notes
                if (rand(0, 1)) {
                    OrderNote::create([
                        'order_id' => $order->id,
                        'notes' => ['تم الشحن', 'جاري التجهيز', 'ملاحظة خاصة'][rand(0, 2)]
                    ]);
                }
            }
        }
    }
}