<?php
// Initialize variables
$service = null;
$error = null;

// Check if slug is provided in the URL
if(isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = trim($_GET['slug']);
    
    // Validate slug format (basic validation)
    if(!preg_match('/^[a-z0-9\-]+$/', $slug)) {
        $error = "Invalid service URL format.";
    } else {
        // Include database connection
        if(!file_exists('includes/connection.php')) {
            $error = "Database configuration not found.";
        } else {
            include('includes/connection.php');
            
            // Check if connection exists
            if(!isset($conn)) {
                $error = "Database connection is not properly configured.";
            } else {
                // Prepare SQL query with prepared statement to prevent SQL injection
                $query = "SELECT `id`, `category_id`, `sub_category_id`, `service_name`, `title`, `slug`, `short_description`, `long_description`, `created_at`, `updated_at` 
                          FROM `services` 
                          WHERE `slug` = ? 
                          AND `slug` IS NOT NULL 
                          AND `slug` != '' 
                          LIMIT 1";
                
                if($stmt = mysqli_prepare($conn, $query)) {
                    // Bind parameters
                    mysqli_stmt_bind_param($stmt, "s", $slug);
                    
                    // Execute query
                    if(mysqli_stmt_execute($stmt)) {
                        // Get result
                        $result = mysqli_stmt_get_result($stmt);
                        
                        if(mysqli_num_rows($result) > 0) {
                            // Fetch service data
                            $service = mysqli_fetch_assoc($result);
                            
                            // Convert NULL values to empty strings for display
                            $service = array_map(function($value) {
                                return $value === null ? '' : $value;
                            }, $service);
                            
                            // Optional: You can add additional processing here
                            // For example, convert markdown to HTML, process images, etc.
                        } else {
                            $error = "The service you're looking for doesn't exist or has been moved.";
                        }
                    } else {
                        $error = "Unable to fetch service details at the moment. Please try again later.";
                    }
                    
                    // Close statement
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Database query preparation failed.";
                }
                
                // Close connection
                mysqli_close($conn);
            }
        }
    }
} else {
    // No slug provided - this is okay, will show welcome message
    $error = "No service selected. Please choose a service from our list.";
}

// Optional: Log errors (for debugging)
if($error && file_exists('error_log.txt')) {
    $log_message = date('Y-m-d H:i:s') . " - Slug: " . ($_GET['slug'] ?? 'none') . " - Error: " . $error . "\n";
    file_put_contents('error_log.txt', $log_message, FILE_APPEND);
}
?>