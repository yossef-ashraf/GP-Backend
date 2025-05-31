<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Area;

class ReportController extends Controller
{
    use ApiResponseTrait;

    public function salesReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now());
        $groupBy = $request->input('group_by', 'day'); // day, week, month

        // تحويل التواريخ إلى كائنات Carbon
        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // إحصائيات عامة
        $generalStats = [
            'total_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount'),
            'average_order_value' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->avg('total_amount'),
            'cancelled_orders' => Order::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'cancelled')
                ->count(),
        ];

        // المبيعات حسب الفترة الزمنية
        $salesByPeriod = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // أفضل المنتجات مبيعاً
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_amount) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // المبيعات حسب المناطق
        $salesByArea = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->join('areas', 'orders.area_id', '=', 'areas.id')
            ->select(
                'areas.name as area_name',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy('areas.id', 'areas.name')
            ->orderByDesc('total_sales')
            ->get();

        // المبيعات حسب طرق الدفع
        $salesByPaymentMethod = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->groupBy('payment_method')
            ->get();

        // المبيعات حسب الفئات
        $salesByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('product_categories', 'products.id', '=', 'product_categories.product_id')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'categories.data as category_name',
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_amount) as total_sales')
            )
            ->groupBy('categories.id', 'categories.data')
            ->orderByDesc('total_sales')
            ->get();

        return $this->successResponse([
            'general_stats' => $generalStats,
            'sales_by_period' => $salesByPeriod,
            'top_products' => $topProducts,
            'sales_by_area' => $salesByArea,
            'sales_by_payment_method' => $salesByPaymentMethod,
            'sales_by_category' => $salesByCategory,
        ], 'Sales report generated successfully');
    }

    public function inventoryReport()
    {
        // المنتجات منخفضة المخزون
        $lowStockProducts = Product::where('stock_qty', '<', 10)
            ->select('id', 'name', 'stock_qty', 'price')
            ->get();

        // المنتجات الأكثر طلباً
        $mostOrderedProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_ordered'),
                'products.stock_qty'
            )
            ->groupBy('products.id', 'products.name', 'products.stock_qty')
            ->orderByDesc('total_ordered')
            ->limit(10)
            ->get();

        // المنتجات التي لم يتم طلبها
        $neverOrderedProducts = Product::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('order_items')
                ->whereRaw('order_items.product_id = products.id');
        })
        ->select('id', 'name', 'stock_qty', 'price')
        ->get();

        return $this->successResponse([
            'low_stock_products' => $lowStockProducts,
            'most_ordered_products' => $mostOrderedProducts,
            'never_ordered_products' => $neverOrderedProducts,
        ], 'Inventory report generated successfully');
    }

    public function customerReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now());

        // أفضل العملاء
        $topCustomers = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', '!=', 'cancelled')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get();

        // العملاء الجدد
        $newCustomers = DB::table('users')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('id', 'name', 'email', 'created_at')
            ->orderByDesc('created_at')
            ->get();

        // متوسط قيمة الطلب لكل عميل
        $averageOrderValue = DB::table('orders')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->select(
                'user_id',
                DB::raw('AVG(total_amount) as average_order_value'),
                DB::raw('COUNT(*) as total_orders')
            )
            ->groupBy('user_id')
            ->get();

        return $this->successResponse([
            'top_customers' => $topCustomers,
            'new_customers' => $newCustomers,
            'average_order_value' => $averageOrderValue,
        ], 'Customer report generated successfully');
    }
} 