<?php

class ProfitCalculator {
    private $dbcon;
    
    public function __construct() {
        $this->dbcon = getDB();
    }
    
    /**
     * Calculate daily profit for a specific date
     * Only includes products with valid buying prices
     */
    public function getDailyProfit($date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $pdo = $this->dbcon->connect();
        
        // Get all sales for the day with product details
        // Only include products with valid buying prices
        $sql = "SELECT 
                    i.invoice_no,
                    i.product_name,
                    i.order_qty,
                    i.price_per_item,
                    p.buying_price
                FROM invoices i
                INNER JOIN products p ON i.product_name = p.product_name
                WHERE DATE(i.created_at) = :date 
                AND p.buying_price > 0";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalRevenue = 0;
        $totalCost = 0;
        
        foreach ($sales as $sale) {
            $revenue = $sale['order_qty'] * $sale['price_per_item'];
            $cost = $sale['order_qty'] * $sale['buying_price'];
            
            $totalRevenue += $revenue;
            $totalCost += $cost;
        }
        
        $profit = $totalRevenue - $totalCost;
        
        return [
            'date' => $date,
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'profit' => $profit,
            'sales_count' => count($sales)
        ];
    }
    
    /**
     * Calculate monthly profit for a specific month
     * Only includes products with valid buying prices
     */
    public function getMonthlyProfit($year = null, $month = null) {
        if (!$year) $year = date('Y');
        if (!$month) $month = date('m');
        
        $pdo = $this->dbcon->connect();
        
        // Get all sales for the month with product details
        // Only include products with valid buying prices
        $sql = "SELECT 
                    i.invoice_no,
                    i.product_name,
                    i.order_qty,
                    i.price_per_item,
                    p.buying_price
                FROM invoices i
                INNER JOIN products p ON i.product_name = p.product_name
                WHERE YEAR(i.created_at) = :year 
                AND MONTH(i.created_at) = :month
                AND p.buying_price > 0";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalRevenue = 0;
        $totalCost = 0;
        
        foreach ($sales as $sale) {
            $revenue = $sale['order_qty'] * $sale['price_per_item'];
            $cost = $sale['order_qty'] * $sale['buying_price'];
            
            $totalRevenue += $revenue;
            $totalCost += $cost;
        }
        
        $profit = $totalRevenue - $totalCost;
        
        return [
            'year' => $year,
            'month' => $month,
            'revenue' => $totalRevenue,
            'cost' => $totalCost,
            'profit' => $profit,
            'sales_count' => count($sales)
        ];
    }
    
    /**
     * Get profit trend for the last 7 days
     * Only includes products with valid buying prices
     */
    public function getWeeklyProfitTrend() {
        $pdo = $this->dbcon->connect();
        
        $sql = "SELECT 
                    DATE(i.created_at) as sale_date,
                    SUM(i.order_qty * i.price_per_item) as daily_revenue,
                    SUM(i.order_qty * p.buying_price) as daily_cost
                FROM invoices i
                INNER JOIN products p ON i.product_name = p.product_name
                WHERE i.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND p.buying_price > 0
                GROUP BY DATE(i.created_at)
                ORDER BY sale_date";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $trend = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($trend as $day) {
            $result[] = [
                'date' => $day['sale_date'],
                'revenue' => $day['daily_revenue'],
                'cost' => $day['daily_cost'],
                'profit' => $day['daily_revenue'] - $day['daily_cost']
            ];
        }
        
        return $result;
    }
    
    /**
     * Get top selling products by profit
     * Only includes products with valid buying prices
     */
    public function getTopProfitableProducts($limit = 10) {
        $pdo = $this->dbcon->connect();
        
        $sql = "SELECT 
                    i.product_name,
                    SUM(i.order_qty) as total_sold,
                    SUM(i.order_qty * i.price_per_item) as total_revenue,
                    SUM(i.order_qty * p.buying_price) as total_cost,
                    (SUM(i.order_qty * i.price_per_item) - SUM(i.order_qty * p.buying_price)) as total_profit
                FROM invoices i
                INNER JOIN products p ON i.product_name = p.product_name
                WHERE p.buying_price > 0
                GROUP BY i.product_name
                ORDER BY total_profit DESC
                LIMIT :limit";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get statistics about products with/without buying prices
     */
    public function getBuyingPriceStats() {
        $pdo = $this->dbcon->connect();
        
        $sql = "SELECT 
                    COUNT(*) as total_products,
                    COUNT(CASE WHEN buying_price > 0 THEN 1 END) as products_with_buying_price,
                    COUNT(CASE WHEN buying_price = 0 OR buying_price IS NULL THEN 1 END) as products_without_buying_price,
                    ROUND((COUNT(CASE WHEN buying_price > 0 THEN 1 END) / COUNT(*)) * 100, 2) as percentage_with_buying_price
                FROM products";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>




