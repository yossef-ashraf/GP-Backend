<?php

// Configuration
$migrationsPath = 'database/migrations/';
$modelsPath = 'app/Models/';

// Database schema
$tables = [
    'areas' => [
        'id' => 'integer',
        'name' => 'varchar',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'addresses' => [
        'id' => 'integer',
        'user_id' => 'integer',
        'area_id' => 'integer',
        'state' => 'varchar',
        'zip_code' => 'varchar',
        'street' => 'varchar',
        'building_number' => 'varchar',
        'apartment_number' => 'varchar',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'roles' => [
        'id' => 'integer',
        'name' => 'varchar',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'role_permission' => [
        'id' => 'integer',
        'role_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'users' => [
        'id' => 'integer',
        'role_id' => 'integer',
        'name' => 'varchar',
        'email' => 'varchar',
        'password' => 'varchar',
        'phone' => 'varchar',
        'gender' => 'varchar',
        'date_of_birth' => 'date',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'shipping_values' => [
        'id' => 'integer',
        'area_id' => 'integer',
        'value' => 'float',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'products' => [
        'id' => 'integer',
        'slug' => 'varchar',
        'type' => 'varchar',
        'sku' => 'varchar',
        'price' => 'float',
        'sale_price' => 'float',
        'sold_individually' => 'integer',
        'stock_status' => 'varchar',
        'stock_qty' => 'integer',
        'total_sales' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'product_variations' => [
        'id' => 'integer',
        'slug' => 'varchar',
        'product_id' => 'integer',
        'regular_price' => 'float',
        'sale_price' => 'float',
        'manage_stock' => 'varchar',
        'stock_status' => 'varchar',
        'stock_qty' => 'integer',
        'total_sales' => 'integer',
        'backorder_limit' => 'integer',
        'sku' => 'varchar',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'product_images' => [
        'id' => 'integer',
        'image' => 'varchar',
        'product_id' => 'integer',
        'variation_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ],
    'product_reviews' => [
        'id' => 'integer',
        'user_id' => 'integer',
        'product_id' => 'integer',
        'rating' => 'integer',
        'comment' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ],
    'attributes' => [
        'id' => 'integer',
        'data' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'attribute_values' => [
        'id' => 'integer',
        'attribute_id' => 'integer',
        'data' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'product_attributes' => [
        'id' => 'integer',
        'product_id' => 'integer',
        'attribute_id' => 'integer',
    ],
    'product_attribute_values' => [
        'id' => 'integer',
        'product_id' => 'integer',
        'attribute_id' => 'integer',
        'attribute_value_id' => 'integer',
    ],
    'variation_attribute_values' => [
        'id' => 'integer',
        'variation_id' => 'integer',
        'attribute_value_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'categories' => [
        'id' => 'integer',
        'parent_id' => 'integer',
        'data' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'product_categories' => [
        'id' => 'integer',
        'category_id' => 'integer',
        'product_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'branches' => [
        'id' => 'integer',
        'data' => 'text',
        'phone' => 'varchar',
        'area_id' => 'integer',
        'is_master' => 'bool',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'product_branches' => [
        'id' => 'integer',
        'branch_id' => 'integer',
        'product_id' => 'integer',
        'variation_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'payment_methods' => [
        'id' => 'integer',
        'name' => 'varchar',
        'status' => 'varchar',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'orders' => [
        'id' => 'integer',
        'user_id' => 'integer',
        'coupon_id' => 'integer',
        'address_id' => 'integer',
        'payment_method_id' => 'integer',
        'total_amount' => 'float',
        'status' => 'varchar',
        'notes' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'order_items' => [
        'id' => 'integer',
        'order_id' => 'integer',
        'product_id' => 'integer',
        'variation_id' => 'integer',
        'total_amount' => 'float',
        'quantity' => 'integer',
        'price' => 'integer',
        'variation_data' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'order_notes' => [
        'id' => 'integer',
        'order_id' => 'integer',
        'notes' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'carts' => [
        'id' => 'integer',
        'user_id' => 'integer',
        'cart_total' => 'float',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'cart_items' => [
        'id' => 'integer',
        'cart_id' => 'integer',
        'product_id' => 'integer',
        'variation_id' => 'integer',
        'total_amount' => 'float',
        'quantity' => 'integer',
        'price' => 'integer',
        'variation_data' => 'text',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
    'coupons' => [
        'id' => 'integer',
        'name' => 'varchar',
        'code' => 'varchar',
        'discount_value' => 'float',
        'discount_type' => 'varchar',
        'valid_from' => 'timestamp',
        'valid_to' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ],
];

// Create directories if they don't exist
if (!file_exists($migrationsPath)) {
    mkdir($migrationsPath, 0777, true);
}
if (!file_exists($modelsPath)) {
    mkdir($modelsPath, 0777, true);
}

// Migration stub template
$migrationStub = <<<'EOT'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class {{class}} extends Migration
{
    public function up()
    {
        {{up}}
    }

    public function down()
    {
        {{down}}
    }
}
EOT;

// Generate migrations
foreach ($tables as $tableName => $columns) {
    $migrationContent = generateMigration($tableName, $columns, $migrationStub);
    $migrationFileName = date('Y_m_d_His') . '_create_' . $tableName . '_table.php';
    
    // Increment timestamp for next migration
    sleep(1);
    
    file_put_contents($migrationsPath . $migrationFileName, $migrationContent);
    
    // Generate model
    $modelContent = generateModel($tableName, $columns);
    $modelFileName = ucfirst(str_singular($tableName)) . '.php';
    file_put_contents($modelsPath . $modelFileName, $modelContent);
}

echo "Migrations and models generated successfully!\n";

// Helper functions
function generateMigration($tableName, $columns, $stub) {
    $upMethod = "Schema::create('{$tableName}', function (Blueprint \$table) {\n";
    $upMethod .= "            \$table->id();\n";
    
    foreach ($columns as $columnName => $columnType) {
        if ($columnName === 'id') continue;
        
        $method = getMigrationMethod($columnType);
        $upMethod .= "            \$table->{$method}('{$columnName}')";
        
        if (str_contains($columnName, '_at') && $columnType === 'timestamp') {
            $upMethod .= "->nullable()";
        }
        
        $upMethod .= ";\n";
    }
    
    $upMethod .= "        });";
    
    $downMethod = "Schema::dropIfExists('{$tableName}');";
    
    $className = 'Create' . ucfirst(str_replace('_', '', $tableName)) . 'Table';
    
    $content = str_replace(['{{class}}', '{{up}}', '{{down}}'], [$className, $upMethod, $downMethod], $stub);
    
    return $content;
}

function generateModel($tableName, $columns) {
    $modelName = ucfirst(str_singular($tableName));
    
    $fillable = [];
    $casts = [];
    $dates = [];
    $relations = [];
    
    // Basic model stub
    $content = "<?php\n\nnamespace App\\Models;\n\n";
    $content .= "use Illuminate\\Database\\Eloquent\\Model;\n";
    
    // Add SoftDeletes if table has deleted_at
    if (isset($columns['deleted_at'])) {
        $content .= "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n";
    }
    
    $content .= "\nclass {$modelName} extends Model\n{\n";
    
    // Add SoftDeletes trait if needed
    if (isset($columns['deleted_at'])) {
        $content .= "    use SoftDeletes;\n\n";
    }
    
    $content .= "    protected \$table = '{$tableName}';\n\n";
    $content .= "    protected \$fillable = [\n";
    
    // Add fillable fields (excluding timestamps and id)
    foreach ($columns as $columnName => $columnType) {
        if (!in_array($columnName, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
            $fillable[] = "'{$columnName}'";
        }
    }
    
    $content .= "        " . implode(",\n        ", $fillable) . "\n";
    $content .= "    ];\n\n";
    
    // Add casts for specific fields
    if (!empty($casts)) {
        $content .= "    protected \$casts = [\n";
        foreach ($columns as $columnName => $columnType) {
            if ($columnType === 'bool') {
                $casts[] = "'{$columnName}' => 'boolean'";
            } elseif ($columnType === 'float') {
                $casts[] = "'{$columnName}' => 'float'";
            } elseif ($columnType === 'integer') {
                $casts[] = "'{$columnName}' => 'integer'";
            }
        }
        $content .= "        " . implode(",\n        ", $casts) . "\n";
        $content .= "    ];\n\n";
    }
    
    // Add dates
    $dates = [];
    foreach ($columns as $columnName => $columnType) {
        if (str_contains($columnName, '_at') && $columnType === 'timestamp') {
            $dates[] = "'{$columnName}'";
        }
    }
    
    if (!empty($dates)) {
        $content .= "    protected \$dates = [\n";
        $content .= "        " . implode(",\n        ", $dates) . "\n";
        $content .= "    ];\n\n";
    }
    
    // Add relationships (basic implementation)
    foreach ($columns as $columnName => $columnType) {
        if (str_ends_with($columnName, '_id')) {
            $relatedModel = ucfirst(str_singular(str_replace('_id', '', $columnName)));
            $relationName = str_replace('_id', '', $columnName);
            
            $content .= "    public function {$relationName}()\n";
            $content .= "    {\n";
            $content .= "        return \$this->belongsTo({$relatedModel}::class, '{$columnName}');\n";
            $content .= "    }\n\n";
        }
    }
    
    $content .= "}\n";
    
    return $content;
}

function getMigrationMethod($type) {
    $mapping = [
        'integer' => 'integer',
        'varchar' => 'string',
        'text' => 'text',
        'timestamp' => 'timestamp',
        'date' => 'date',
        'float' => 'float',
        'bool' => 'boolean',
    ];
    
    return $mapping[$type] ?? 'string';
}

function str_singular($value) {
    // Basic singularization
    $singular = [
        '/(quiz)zes$/i' => '\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias|status)es$/i' => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/(bus)es$/i' => '\1',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1ovie',
        '/(s)eries$/i' => '\1eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/([^f])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(n)ews$/i' => '\1ews',
        '/s$/i' => '',
    ];
    
    foreach ($singular as $rule => $replacement) {
        if (preg_match($rule, $value)) {
            return preg_replace($rule, $replacement, $value);
        }
    }
    
    return $value;
}