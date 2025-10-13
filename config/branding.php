<?php
/**
 * Branding Configuration
 * 
 * Centralized branding settings for the application
 * Customize these values to match your business branding
 */

class Branding {
    
    private static $settings = null;
    private static $json_file = __DIR__ . '/branding.json';
    
    // Default settings (fallback if JSON file is missing or corrupt)
    private static $defaults = [
        'business_name' => 'Mini Price Hardware',
        'business_name_short' => 'Mini Price',
        'business_tagline' => 'Quality Hardware at Affordable Prices',
        'business_description' => 'Your one-stop shop for hardware, tools, and building materials',
        'business_address' => 'Kampala, Uganda',
        'business_phone' => '+256 XXX XXXXXX',
        'business_email' => 'info@minipricehardware.com',
        'logo_path' => 'images/logo.png',
        'logo_white_path' => 'images/logo-white.png',
        'favicon_path' => 'images/favicon.ico',
        'color_primary' => '#667eea',
        'color_secondary' => '#764ba2',
        'color_success' => '#43e97b',
        'color_danger' => '#f5576c',
        'color_warning' => '#ffa502',
        'color_info' => '#4facfe',
        'app_name' => 'IMS',
        'app_version' => '2.0',
        'currency' => 'UGX',
        'currency_symbol' => 'ugx',
        'date_format' => 'd-m-Y',
        'time_format' => 'H:i:s',
        'facebook_url' => '',
        'twitter_url' => '',
        'instagram_url' => '',
        'timezone' => 'Africa/Kampala',
        'language' => 'en',
        'items_per_page' => 10,
        'low_stock_threshold' => 30,
        'critical_stock_threshold' => 10,
        'expiry_warning_days' => 90,
        'expiry_critical_days' => 30,
    ];
    
    /**
     * Load settings from JSON file
     */
    private static function loadSettings() {
        if (self::$settings !== null) {
            return; // Already loaded
        }
        
        // Try to load from JSON file
        if (file_exists(self::$json_file)) {
            $json_content = file_get_contents(self::$json_file);
            $settings = json_decode($json_content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($settings)) {
                // Merge with defaults to ensure all keys exist
                self::$settings = array_merge(self::$defaults, $settings);
                return;
            }
        }
        
        // Fallback to defaults
        self::$settings = self::$defaults;
    }
    
    /**
     * Save settings to JSON file
     * @param array $settings Settings to save
     * @return bool Success status
     */
    public static function saveSettings($settings) {
        // Merge with current settings to preserve unmodified values
        self::loadSettings();
        $newSettings = array_merge(self::$settings, $settings);
        
        // Save to JSON file with pretty print
        $json = json_encode($newSettings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if ($json === false) {
            return false;
        }
        
        $result = file_put_contents(self::$json_file, $json);
        
        if ($result !== false) {
            // Reload settings
            self::$settings = $newSettings;
            return true;
        }
        
        return false;
    }
    
    /**
     * Get a branding setting
     * @param string $key Setting key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public static function get($key, $default = null) {
        self::loadSettings();
        return isset(self::$settings[$key]) ? self::$settings[$key] : $default;
    }
    
    /**
     * Get all branding settings
     * @return array
     */
    public static function getAll() {
        self::loadSettings();
        return self::$settings;
    }
    
    /**
     * Set a branding setting (runtime only, not persistent)
     * @param string $key Setting key
     * @param mixed $value Setting value
     */
    public static function set($key, $value) {
        self::loadSettings();
        self::$settings[$key] = $value;
    }
    
    /**
     * Get business name
     * @param bool $short Get short version
     * @return string
     */
    public static function getBusinessName($short = false) {
        self::loadSettings();
        return $short ? self::$settings['business_name_short'] : self::$settings['business_name'];
    }
    
    /**
     * Get formatted currency
     * @param float $amount Amount to format
     * @param bool $includeSymbol Include currency symbol
     * @return string
     */
    public static function formatCurrency($amount, $includeSymbol = true) {
        self::loadSettings();
        $formatted = number_format($amount, 2);
        return $includeSymbol ? self::$settings['currency_symbol'] . ' ' . $formatted : $formatted;
    }
    
    /**
     * Get formatted date
     * @param string $date Date to format
     * @param string $format Custom format (optional)
     * @return string
     */
    public static function formatDate($date, $format = null) {
        self::loadSettings();
        $format = $format ?: self::$settings['date_format'];
        return date($format, strtotime($date));
    }
    
    /**
     * Get logo path
     * @param bool $white Get white version
     * @return string
     */
    public static function getLogo($white = false) {
        self::loadSettings();
        return $white ? self::$settings['logo_white_path'] : self::$settings['logo_path'];
    }
    
    /**
     * Get primary color
     * @return string
     */
    public static function getPrimaryColor() {
        self::loadSettings();
        return self::$settings['color_primary'];
    }
    
    /**
     * Generate CSS variables for branding colors
     * @return string CSS style tag with variables
     */
    public static function getColorVariablesCSS() {
        return "
        <style>
            :root {
                --brand-primary: {$this->$settings['color_primary']};
                --brand-secondary: {$this->$settings['color_secondary']};
                --brand-success: {$this->$settings['color_success']};
                --brand-danger: {$this->$settings['color_danger']};
                --brand-warning: {$this->$settings['color_warning']};
                --brand-info: {$this->$settings['color_info']};
            }
        </style>
        ";
    }
    
    /**
     * Get application title for page
     * @param string $pageTitle Specific page title
     * @return string
     */
    public static function getPageTitle($pageTitle = '') {
        self::loadSettings();
        $businessName = self::$settings['business_name_short'];
        return $pageTitle ? "{$pageTitle} - {$businessName} IMS" : "{$businessName} - Inventory Management System";
    }
    
    /**
     * Check if stock is low
     * @param int $stock Current stock level
     * @return string 'critical', 'low', 'normal'
     */
    public static function getStockStatus($stock) {
        self::loadSettings();
        if ($stock <= self::$settings['critical_stock_threshold']) {
            return 'critical';
        } elseif ($stock <= self::$settings['low_stock_threshold']) {
            return 'low';
        }
        return 'normal';
    }
    
    /**
     * Get stock badge class
     * @param int $stock Current stock level
     * @return string Bootstrap badge class
     */
    public static function getStockBadgeClass($stock) {
        $status = self::getStockStatus($stock);
        switch ($status) {
            case 'critical':
                return 'badge-danger';
            case 'low':
                return 'badge-warning';
            default:
                return 'badge-success';
        }
    }
    
    /**
     * Get stock badge text
     * @param int $stock Current stock level
     * @return string
     */
    public static function getStockBadgeText($stock) {
        $status = self::getStockStatus($stock);
        switch ($status) {
            case 'critical':
                return 'Out of Stock';
            case 'low':
                return 'Low Stock';
            default:
                return 'In Stock';
        }
    }
    
    /**
     * Check if product is expiring soon
     * @param string $expiryDate Expiry date
     * @return array ['status' => 'expired|critical|warning|normal', 'days' => days_remaining]
     */
    public static function getExpiryStatus($expiryDate) {
        self::loadSettings();
        
        if (empty($expiryDate)) {
            return ['status' => 'normal', 'days' => null];
        }
        
        $now = time();
        $expiry = strtotime($expiryDate);
        $daysRemaining = floor(($expiry - $now) / (60 * 60 * 24));
        
        if ($daysRemaining < 0) {
            return ['status' => 'expired', 'days' => $daysRemaining];
        } elseif ($daysRemaining <= self::$settings['expiry_critical_days']) {
            return ['status' => 'critical', 'days' => $daysRemaining];
        } elseif ($daysRemaining <= self::$settings['expiry_warning_days']) {
            return ['status' => 'warning', 'days' => $daysRemaining];
        }
        
        return ['status' => 'normal', 'days' => $daysRemaining];
    }
}

// Set timezone
date_default_timezone_set(Branding::get('timezone', 'Africa/Kampala'));

