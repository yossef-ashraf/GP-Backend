<?php

// Configuration
$filamentResourcesPath = 'app/Filament/Resources/';
$filamentPagesPath = 'app/Filament/Resources/Pages/';
$filamentRelationManagersPath = 'app/Filament/Resources/RelationManagers/';
$filamentWidgetsPath = 'app/Filament/Widgets/';

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
    'users' => [
        'id' => 'integer',
        'name' => 'varchar',
        'email' => 'varchar',
        'password' => 'varchar',
        'phone' => 'varchar',
        'gender' => 'varchar',
        'date_of_birth' => 'date',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
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
        'stock_qtn' => 'integer',
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
        'stock_qtn' => 'integer',
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

// Create top-level directories if they don't exist
foreach ([$filamentResourcesPath, $filamentPagesPath, $filamentRelationManagersPath, $filamentWidgetsPath] as $path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

// Generate Filament resources
foreach ($tables as $tableName => $columns) {
    $resourceName = ucfirst(str_singular($tableName)) . 'Resource';
    $modelName = ucfirst(str_singular($tableName));
    $modelPagesPath = $filamentPagesPath . $modelName . '/';
    $modelRelationManagersPath = $filamentRelationManagersPath . $modelName . '/';

    // Create model-specific directories
    foreach ([$modelPagesPath, $modelRelationManagersPath] as $path) {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    // Generate Resource file
    $resourceContent = generateFilamentResource($tableName, $columns);
    if (!file_put_contents($filamentResourcesPath . $resourceName . '.php', $resourceContent)) {
        echo "Failed to write resource file: {$filamentResourcesPath}{$resourceName}.php\n";
        continue;
    }

    // Generate List page
    $listPageContent = generateListPage($resourceName, $modelName);
    if (!file_put_contents($modelPagesPath . "List{$modelName}.php", $listPageContent)) {
        echo "Failed to write list page file: {$modelPagesPath}List{$modelName}.php\n";
    }

    // Generate Create page
    $createPageContent = generateCreatePage($resourceName, $modelName);
    if (!file_put_contents($modelPagesPath . "Create{$modelName}.php", $createPageContent)) {
        echo "Failed to write create page file: {$modelPagesPath}Create{$modelName}.php\n";
    }

    // Generate Edit page
    $editPageContent = generateEditPage($resourceName, $modelName);
    if (!file_put_contents($modelPagesPath . "Edit{$modelName}.php", $editPageContent)) {
        echo "Failed to write edit page file: {$modelPagesPath}Edit{$modelName}.php\n";
    }

    // Generate View page
    $viewPageContent = generateViewPage($resourceName, $modelName);
    if (!file_put_contents($modelPagesPath . "View{$modelName}.php", $viewPageContent)) {
        echo "Failed to write view page file: {$modelPagesPath}View{$modelName}.php\n";
    }

    // Generate Relation Managers for foreign keys
    foreach ($columns as $columnName => $columnType) {
        if (str_ends_with($columnName, '_id')) {
            $relationName = str_replace('_id', '', $columnName);
            $relationManagerContent = generateRelationManager($resourceName, $relationName, $tables, $modelName);
            $relationManagerName = ucfirst(str_singular($relationName)) . 'RelationManager';
            if (!file_put_contents($modelRelationManagersPath . $relationManagerName . '.php', $relationManagerContent)) {
                echo "Failed to write relation manager file: {$modelRelationManagersPath}{$relationManagerName}.php\n";
            }
        }
    }

    // Generate Chart Widget for tables with numeric data
    if (hasNumericData($columns)) {
        $chartWidgetContent = generateChartWidget($resourceName, $tableName);
        if (!file_put_contents($filamentWidgetsPath . "{$modelName}ChartWidget.php", $chartWidgetContent)) {
            echo "Failed to write chart widget file: {$filamentWidgetsPath}{$modelName}ChartWidget.php\n";
        }
    }
}

echo "Filament v3 resources generated successfully!\n";

// Helper functions
function generateFilamentResource($tableName, $columns) {
    $resourceName = ucfirst(str_singular($tableName)) . 'Resource';
    $modelName = ucfirst(str_singular($tableName));
    $modelNamespace = "App\\Models\\{$modelName}";
    $resourceLabel = ucfirst(str_replace('_', ' ', $tableName));
    $navigationIcon = getIconForTable($tableName);

    $formFields = [];
    $tableColumns = [];
    $infolistEntries = [];
    $relations = [];

    foreach ($columns as $columnName => $columnType) {
        // Form fields and table columns
        if ($columnName !== 'id' && !str_ends_with($columnName, '_at')) {
            if (str_ends_with($columnName, '_id')) {
                $relationName = str_replace('_id', '', $columnName);
                $relations[] = $relationName;
                $formFields[] = "            Forms\Components\Select::make('{$columnName}')\n                ->relationship('".str_singular($relationName)."', 'name')\n                ->required()\n                ->searchable()";
                $tableColumns[] = "                Tables\Columns\TextColumn::make('".str_singular($relationName).".name')\n                    ->label('".ucfirst(str_replace('_', ' ', $relationName))."')\n                    ->searchable()\n                    ->sortable()";
                $infolistEntries[] = "                        Components\TextEntry::make('".str_singular($relationName).".name')\n                            ->label('".ucfirst(str_replace('_', ' ', $relationName))."')";
                continue;
            }

            $fieldType = getFilamentFieldType($columnType);
            $field = "            {$fieldType}::make('{$columnName}')";

            if ($columnType === 'text') {
                $field .= "\n                ->columnSpanFull()";
            }
            if (in_array($columnName, ['name', 'title', 'email'])) {
                $field .= "\n                ->required()\n                ->maxLength(255)";
                if ($columnName === 'email') {
                    $field .= "\n                ->email()";
                }
            }
            if ($columnType === 'bool') {
                $field .= "\n                ->inline(false)";
            }
            if ($columnType === 'float' || $columnType === 'integer') {
                $field .= "\n                ->numeric()";
            }
            $formFields[] = $field;

            if ($columnType === 'varchar' || $columnType === 'text') {
                $tableColumns[] = "                Tables\Columns\TextColumn::make('{$columnName}')\n                    ->searchable()\n                    ->sortable()";
                $infolistEntries[] = "                        Components\TextEntry::make('{$columnName}')\n                            ->label('".ucfirst(str_replace('_', ' ', $columnName))."')";
            } elseif ($columnType === 'integer' || $columnType === 'float') {
                $tableColumns[] = "                Tables\Columns\TextColumn::make('{$columnName}')\n                    ->numeric()\n                    ->sortable()";
                $infolistEntries[] = "                        Components\TextEntry::make('{$columnName}')\n                            ->label('".ucfirst(str_replace('_', ' ', $columnName))."')";
            } elseif ($columnType === 'bool') {
                $tableColumns[] = "                Tables\Columns\IconColumn::make('{$columnName}')\n                    ->boolean()\n                    ->sortable()";
                $infolistEntries[] = "                        Components\IconEntry::make('{$columnName}')\n                            ->label('".ucfirst(str_replace('_', ' ', $columnName))."')\n                            ->boolean()";
            }
        } elseif ($columnName === 'id') {
            $tableColumns[] = "                Tables\Columns\TextColumn::make('id')\n                    ->numeric()\n                    ->sortable()";
            $infolistEntries[] = "                        Components\TextEntry::make('id')\n                            ->label('ID')";
        } elseif (str_ends_with($columnName, '_at') && $columnType === 'timestamp') {
            $tableColumns[] = "                Tables\Columns\TextColumn::make('{$columnName}')\n                    ->dateTime()\n                    ->sortable()\n                    ->toggleable(isToggledHiddenByDefault: true)";
            $infolistEntries[] = "                        Components\TextEntry::make('{$columnName}')\n                            ->label('".ucfirst(str_replace('_', ' ', $columnName))."')\n                            ->dateTime()";
        }
    }

    $formFieldsCode = implode(",\n", $formFields);
    $tableColumnsCode = implode(",\n", $tableColumns);
    $infolistEntriesCode = implode(",\n", $infolistEntries);

    $relationsCode = '';
    if (!empty($relations)) {
        $relationsCode = "\n    public static function getRelations(): array\n    {\n        return [\n";
        foreach ($relations as $relation) {
            $relationModel = ucfirst(str_singular($relation));
            $relationsCode .= "            RelationManagers\\{$modelName}\\{$relationModel}RelationManager::class,\n";
        }
        $relationsCode .= "        ];\n    }";
    }

    $content = <<<EOT
<?php

namespace App\Filament\Resources;

use {$modelNamespace};
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class {$resourceName} extends Resource
{
    protected static ?string \$model = {$modelName}::class;

    protected static ?string \$navigationIcon = '{$navigationIcon}';

    protected static ?string \$modelLabel = '{$resourceLabel}';

    public static function form(Form \$form): Form
    {
        return \$form
            ->schema([
{$formFieldsCode}
            ])->columns(12);
    }

    public static function table(Table \$table): Table
    {
        return \$table
            ->columns([
{$tableColumnsCode}
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function infolist(Infolist \$infolist): Infolist
    {
        return \$infolist
            ->schema([
                Components\Section::make('{$resourceLabel} Details')
                    ->schema([
{$infolistEntriesCode}
                    ])
                    ->columns(2),
            ]);
    }

{$relationsCode}

    public static function getPages(): array
    {
        return [
            'index' => Pages\\{$modelName}\\List{$modelName}::route('/'),
            'create' => Pages\\{$modelName}\\Create{$modelName}::route('/create'),
            'view' => Pages\\{$modelName}\\View{$modelName}::route('/{record}'),
            'edit' => Pages\\{$modelName}\\Edit{$modelName}::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
EOT;

    return $content;
}

function generateListPage($resourceName, $modelName) {
    $pluralModelName = str_plural($modelName);
    $content = <<<EOT
<?php

namespace App\Filament\Resources\Pages\\{$modelName};

use App\Filament\Resources\\{$resourceName};
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class List{$modelName} extends ListRecords
{
    protected static string \$resource = {$resourceName}::class;

    protected function getHeaderActions(): array
    {
        return [
            \\Filament\\Actions\\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Records'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder \$query) => \$query->whereNotNull('created_at')),
        ];
    }
}
EOT;

    return $content;
}

function generateCreatePage($resourceName, $modelName) {
    $content = <<<EOT
<?php

namespace App\Filament\Resources\Pages\\{$modelName};

use App\Filament\Resources\\{$resourceName};
use Filament\Resources\Pages\CreateRecord;

class Create{$modelName} extends CreateRecord
{
    protected static string \$resource = {$resourceName}::class;
}
EOT;

    return $content;
}

function generateEditPage($resourceName, $modelName) {
    $content = <<<EOT
<?php

namespace App\Filament\Resources\Pages\\{$modelName};

use App\Filament\Resources\\{$resourceName};
use Filament\Resources\Pages\EditRecord;

class Edit{$modelName} extends EditRecord
{
    protected static string \$resource = {$resourceName}::class;

    protected function getHeaderActions(): array
    {
        return [
            \\Filament\\Actions\\DeleteAction::make(),
            \\Filament\\Actions\\ForceDeleteAction::make(),
            \\Filament\\Actions\\RestoreAction::make(),
        ];
    }
}
EOT;

    return $content;
}

function generateViewPage($resourceName, $modelName) {
    $content = <<<EOT
<?php

namespace App\Filament\Resources\Pages\\{$modelName};

use App\Filament\Resources\\{$resourceName};
use Filament\Resources\Pages\ViewRecord;

class View{$modelName} extends ViewRecord
{
    protected static string \$resource = {$resourceName}::class;

    protected function getHeaderActions(): array
    {
        return [
            \\Filament\\Actions\\EditAction::make(),
        ];
    }
}
EOT;

    return $content;
}

function generateRelationManager($resourceName, $relationName, $tables, $modelName) {
    $relationModel = ucfirst(str_singular($relationName));
    $relationPlural = str_plural($relationName);
    $relationTable = str_plural($relationName);
    $columns = $tables[$relationTable] ?? ['name' => 'varchar'];

    $formFields = [];
    $tableColumns = [];
    foreach ($columns as $columnName => $columnType) {
        if ($columnName === 'id' || str_ends_with($columnName, '_at')) continue;
        $fieldType = getFilamentFieldType($columnType);
        $field = "                Forms\\Components\\{$fieldType}::make('{$columnName}')";
        if ($columnType === 'varchar') {
            $field .= "\n                    ->maxLength(255)";
        }
        if (in_array($columnName, ['name', 'title'])) {
            $field .= "\n                    ->required()";
        }
        $formFields[] = $field;
        $tableColumns[] = "                Tables\\Columns\\TextColumn::make('{$columnName}')\n                    ->searchable()\n                    ->sortable()";
    }
    $formFieldsCode = implode(",\n", $formFields);
    $tableColumnsCode = implode(",\n", $tableColumns);

    $content = <<<EOT
<?php

namespace App\Filament\Resources\RelationManagers\\{$modelName};

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class {$relationModel}RelationManager extends RelationManager
{
    protected static string \$relationship = '{$relationPlural}';

    public function form(Form \$form): Form
    {
        return \$form
            ->schema([
{$formFieldsCode}
            ]);
    }

    public function table(Table \$table): Table
    {
        return \$table
            ->recordTitleAttribute('name')
            ->columns([
{$tableColumnsCode}
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\\Actions\\CreateAction::make(),
                Tables\\Actions\\AttachAction::make(),
            ])
            ->actions([
                Tables\\Actions\\EditAction::make(),
                Tables\\Actions\\DetachAction::make(),
                Tables\\Actions\\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\\Actions\\DetachBulkAction::make(),
                Tables\\Actions\\DeleteBulkAction::make(),
            ]);
    }
}
EOT;

    return $content;
}

function generateChartWidget($resourceName, $tableName) {
    $modelName = str_replace('Resource', '', $resourceName);
    $modelNamespace = "App\\Models\\{$modelName}";

    $content = <<<EOT
<?php

namespace App\Filament\Widgets;

use {$modelNamespace};
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class {$modelName}ChartWidget extends ChartWidget
{
    protected static ?string \$heading = '{$modelName} Statistics';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        \$data = Trend::model({$modelName}::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => '{$modelName} Count',
                    'data' => \$data->map(fn (TrendValue \$value) => \$value->aggregate),
                ],
            ],
            'labels' => \$data->map(fn (TrendValue \$value) => \$value->date),
        ];
    }
}
EOT;

    return $content;
}

function hasNumericData($columns) {
    foreach ($columns as $columnType) {
        if (in_array($columnType, ['integer', 'float'])) {
            return true;
        }
    }
    return false;
}

function getFilamentFieldType($columnType) {
    $mapping = [
        'integer' => 'TextInput',
        'varchar' => 'TextInput',
        'text' => 'Textarea',
        'timestamp' => 'DateTimePicker',
        'date' => 'DatePicker',
        'float' => 'TextInput',
        'bool' => 'Toggle',
    ];

    return $mapping[$columnType] ?? 'TextInput';
}

function getIconForTable($tableName) {
    $icons = [
        'users' => 'heroicon-o-users',
        'products' => 'heroicon-o-shopping-bag',
        'categories' => 'heroicon-o-tag',
        'orders' => 'heroicon-o-shopping-cart',
        'addresses' => 'heroicon-o-map-pin',
        'roles' => 'heroicon-o-shield-exclamation',
        'payments' => 'heroicon-o-credit-card',
        'settings' => 'heroicon-o-cog',
        'default' => 'heroicon-o-document-text',
    ];

    return $icons[$tableName] ?? $icons['default'];
}

function str_singular($value) {
    $singular = [
        '/(quiz)zes$/i' => '$1',
        '/(matr)ices$/i' => '$1ix',
        '/(vert|ind)ices$/i' => '$1ex',
        '/^(ox)en$/i' => '$1',
        '/(alias|status|address)es$/i' => '$1',
        '/([octop|vir])i$/i' => '$1us',
        '/(cris|ax|test)es$/i' => '$1is',
        '/(shoe)s$/i' => '$1',
        '/(o)es$/i' => '$1',
        '/(bus)es$/i' => '$1',
        '/([m|l])ice$/i' => '$1ouse',
        '/(x|ch|ss|sh)es$/i' => '$1',
        '/(m)ovies$/i' => '$1ovie',
        '/(s)eries$/i' => '$1eries',
        '/([^aeiouy]|qu)ies$/i' => '$1y',
        '/([lr])ves$/i' => '$1f',
        '/(tive)s$/i' => '$1',
        '/(hive)s$/i' => '$1',
        '/([^f])ves$/i' => '$1fe',
        '/(^analy)ses$/i' => '$1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '$1$2sis',
        '/([ti])a$/i' => '$1um',
        '/(n)ews$/i' => '$1ews',
        '/s$/i' => '',
    ];

    foreach ($singular as $rule => $replacement) {
        if (preg_match($rule, $value)) {
            return preg_replace($rule, $replacement, $value);
        }
    }

    return $value;
}

function str_plural($value) {
    $plural = [
        '/(quiz)$/i' => '$1zes',
        '/([m|l])ouse$/i' => '$1ice',
        '/(matr|vert|ind)ix|ex$/i' => '$1ices',
        '/(x|ch|ss|sh)$/i' => '$1es',
        '/([^aeiouy]|qu)y$/i' => '$1ies',
        '/(hive)$/i' => '$1s',
        '/(?:([^f])fe|([lr])f)$/i' => '$1$2ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '$1a',
        '/(buffal|tomat)o$/i' => '$1oes',
        '/(bu)s$/i' => '$1ses',
        '/(alias|status|address)$/i' => '$1es',
        '/(octop|vir)us$/i' => '$1i',
        '/(ax|test)is$/i' => '$1es',
        '/s$/i' => 's',
        '/$/' => 's',
    ];

    foreach ($plural as $rule => $replacement) {
        if (preg_match($rule, $value)) {
            return preg_replace($rule, $replacement, $value);
        }
    }

    return $value;
}