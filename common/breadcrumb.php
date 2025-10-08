<?php
/**
 * Breadcrumb Component
 * 
 * Usage:
 * include('common/breadcrumb.php');
 * renderBreadcrumb([
 *     ['name' => 'Dashboard', 'url' => 'index.php'],
 *     ['name' => 'Products', 'url' => 'product.php'],
 *     ['name' => 'Edit Product']  // Last item has no URL (active)
 * ]);
 */

function renderBreadcrumb($items = []) {
    if (empty($items)) {
        return;
    }
    
    echo '<nav aria-label="breadcrumb" class="breadcrumb-nav">';
    echo '<ol class="breadcrumb">';
    
    $totalItems = count($items);
    foreach ($items as $index => $item) {
        $isLast = ($index === $totalItems - 1);
        
        if ($isLast) {
            echo '<li class="breadcrumb-item active" aria-current="page">';
            echo '<i class="fas fa-circle breadcrumb-dot"></i>';
            echo htmlspecialchars($item['name']);
            echo '</li>';
        } else {
            echo '<li class="breadcrumb-item">';
            if (isset($item['url']) && !empty($item['url'])) {
                echo '<a href="' . htmlspecialchars($item['url']) . '">';
                if ($index === 0) {
                    echo '<i class="fas fa-home"></i> ';
                }
                echo htmlspecialchars($item['name']);
                echo '</a>';
            } else {
                echo htmlspecialchars($item['name']);
            }
            echo '</li>';
        }
    }
    
    echo '</ol>';
    echo '</nav>';
}
?>

<style>
.breadcrumb-nav {
    margin-bottom: 20px;
}

.breadcrumb {
    background: #ffffff;
    padding: 12px 20px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 0;
    display: flex;
    flex-wrap: wrap;
    list-style: none;
}

.breadcrumb-item {
    font-size: 14px;
    color: #6c757d;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "\f054";
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
    padding: 0 10px;
    color: #dee2e6;
    font-size: 10px;
}

.breadcrumb-item a {
    color: #667eea;
    text-decoration: none;
    transition: all 0.2s ease;
    font-weight: 500;
}

.breadcrumb-item a:hover {
    color: #764ba2;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #495057;
    font-weight: 600;
}

.breadcrumb-dot {
    font-size: 6px;
    margin-right: 8px;
    color: #667eea;
}

.breadcrumb-item i.fa-home {
    margin-right: 5px;
}

/* Mobile responsive */
@media (max-width: 576px) {
    .breadcrumb {
        padding: 10px 15px;
        font-size: 13px;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        padding: 0 6px;
    }
}
</style>

