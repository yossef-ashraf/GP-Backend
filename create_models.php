<?php

// Define the models directory path
$modelDir = __DIR__ . '/app/Models';

// Create the directory if it doesn't exist
if (!file_exists($modelDir)) {
    mkdir($modelDir, 0777, true);
}

// Define all models with their content
$models = [
    'User' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'date_of_birth',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
    ];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}
EOD,

    'Area' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function shippingValues()
    {
        return $this->hasMany(ShippingValue::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
EOD,

    'Address' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'area_id',
        'state',
        'zip_code',
        'street',
        'building_number',
        'apartment_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
EOD,

    'ShippingValue' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingValue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['area_id', 'value'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
EOD,

    'Product' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'type',
        'sku',
        'price',
        'sale_price',
        'sold_individually',
        'stock_status',
        'stock_qtn',
        'total_sales'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }
}
EOD,

    'Category' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['parent_id', 'data'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
EOD,

    'Coupon' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'discount_value',
        'discount_type',
        'valid_from',
        'valid_to'
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
EOD,

    'Order' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'address_id',
        'payment_method',
        'total_amount',
        'status',
        'tracking_number',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function notes()
    {
        return $this->hasMany(OrderNote::class);
    }
}
EOD,

    'OrderItem' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'variation_id',
        'total_amount',
        'quantity',
        'price',
        'variation_data'
    ];

    protected $casts = [
        'variation_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}
EOD,

    'OrderNote' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_id', 'notes'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
EOD,

    'Cart' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'cart_total'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
EOD,

    'CartItem' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variation_id',
        'total_amount',
        'quantity',
        'price',
        'variation_data'
    ];

    protected $casts = [
        'variation_data' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}
EOD,

    'ProductVariation' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'variation_data',
        'price',
        'sale_price',
        'sku',
        'stock_status',
        'stock_qty'
    ];

    protected $casts = [
        'variation_data' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'variation_id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'variation_id');
    }
}
EOD,

    'ProductCategory' => <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'category_id'
    ];

    /**
     * Get the product that owns the product category.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the category that owns the product category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
EOD
];

// Create each model file
foreach ($models as $modelName => $content) {
    $filePath = $modelDir . '/' . $modelName . '.php';
    file_put_contents($filePath, $content);
    echo "Created: $filePath\n";
}

echo "All model files have been created successfully!\n";