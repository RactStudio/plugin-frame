<?php
/**
 * Plugin Framework Production Scoper
 * Final Working Version
 */

// ==================================================
// Configuration
// Excluded WordPress and WP based plugins classes
// ==================================================
define('WP_CORE_CLASSES', [
    // Exact matches
    'WP', 'wpdb', 'WP_Error', 'WP_Query', 'WP_Post',
    'WP_User', 'WP_Roles', 'WP_Admin_Bar', 'WP_Widget',
    'ABSPATH', 'WPINC', 'WP_CONTENT_DIR', 'WP_PLUGIN_DIR',
    'plugin_dir_path', 'plugin_dir_url', 'plugin_basename',
    'get_plugin_data', 'load_plugin_textdomain',
    'add_action', 'do_action', 'add_filter', 'apply_filters',
    // Prefix matches
    'WP_', 'wp_', 'WC_', 'WooCommerce_', 'Tribe_'
]);

// ==================================================
// Main Execution
// ==================================================
try {
    if ($argc < 3) throw new Exception("Missing required arguments");
    
    // the config initialization
    $pluginFrameArg = isset($argv[3]) ? $argv[3] : '';
    $config = [
        'namespace' => $argv[1],
        'prefix' => $argv[2],
        'plugin_frame' => $pluginFrameArg === 'false' || $pluginFrameArg === false ? false : $pluginFrameArg,
    ];
    
    validateInputs($config);
    processFiles(getTargetDir());

} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage() . PHP_EOL);
}

// ==================================================
// Core Functions
// ==================================================
function validateInputs($config) {
    // Validate namespace
    if (!preg_match('/^[a-zA-Z0-9]{2,50}$/', $config['namespace'])) {
        throw new Exception("Invalid namespace format");
    }
    
    // Validate prefix
    if (!preg_match('/^[a-z0-9]{2,10}$/', $config['prefix'])) {
        throw new Exception("Invalid prefix format");
    }
}

function getTargetDir() {
    $dir = dirname(__DIR__) . '/.dist/plugin-frame';
    if (!is_dir($dir)) throw new Exception("Missing target directory");
    return realpath($dir);
}

function processFiles($targetDir) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($files as $file) {
        if ($file->isFile()) {
            processFile($file->getRealPath());
        }
    }
}

function processFile($path) {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    switch ($extension) {
        case 'php':
            processPHPFile($path);
            break;
        case 'js':
        case 'css':
        case 'html':
        case 'twig':
            processAssetFile($path);
            break;
    }
}

// ==================================================
// File Processors
// ==================================================
function processPHPFile($path) {
    $content = file_get_contents($path);
    $original = $content;
    
    // Process namespaces
    $content = preg_replace_callback(
        '/^namespace\s+([^;]+);/m',
        'updateNamespace',
        $content
    );
    
    // Process use statements
    $content = preg_replace_callback(
        '/^use\s+([^;]+);/m',
        'updateUseStatement',
        $content
    );
    
    // Process ONLY fully qualified class instantiations
    $content = preg_replace_callback(
        '/\bnew\s+\\\\+([\w\\\\]+)\s*\(/',
        'updateClassInstantiation',
        $content
    );

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "✅ Updated PHP: $path\n";
    }
}

function processAssetFile($path) {
    global $config;
    
    $content = file_get_contents($path);
    $updated = str_replace('pf-', $config['prefix'] . '-', $content);
    
    if ($updated !== $content) {
        file_put_contents($path, $updated);
        echo "✅ Updated Asset: $path\n";
    }
}

// ==================================================
// Namespace Handlers
// ==================================================
function updateNamespace($matches) {
    global $config;
    $existing = trim($matches[1]);
    $parts = [$config['namespace']];

    // Split existing namespace into segments
    $existingParts = explode('\\', $existing);
    
    if ($config['plugin_frame'] === false) {
        // Remove all PluginFrame segments
        $existingParts = array_filter($existingParts, function($part) {
            return $part !== 'PluginFrame';
        });
    } elseif (!empty($config['plugin_frame'])) {
        // Replace PluginFrame segments with custom frame
        $existingParts = array_map(function($part) use ($config) {
            return $part === 'PluginFrame' ? $config['plugin_frame'] : $part;
        }, $existingParts);
        
        // Ensure custom frame is at the start if needed
        if ($existingParts[0] !== $config['plugin_frame']) {
            array_unshift($existingParts, $config['plugin_frame']);
        }
    } else {
        // Default case: Add PluginFrame if missing
        if (!in_array('PluginFrame', $existingParts, true)) {
            array_unshift($existingParts, 'PluginFrame');
        }
    }

    // Rebuild the namespace
    $existing = implode('\\', array_filter($existingParts));
    $parts[] = $existing;

    return 'namespace ' . implode('\\', array_filter($parts)) . ';';
}

function updateUseStatement($matches) {
    global $config;
    $statement = trim($matches[1]);
    
    if (shouldSkip($statement)) return "use $statement;";
    
    $parts = explode(' as ', $statement, 2);
    $class = trim($parts[0]);
    $newClass = buildNamespacedClass($class);
    
    return isset($parts[1]) 
        ? "use $newClass as {$parts[1]};"
        : "use $newClass;";
}

// ==================================================
// Class Instantiation Processor (Updated)
// ==================================================
function updateClassInstantiation($matches) {
    global $config;
    $class = trim($matches[1]);
    
    // Skip if already namespaced correctly
    if (strpos($class, $config['namespace']) === 0) {
        return $matches[0];
    }
    
    // Process fully qualified classes
    $class = ltrim($class, '\\');
    if (shouldSkip($class)) return $matches[0];
    
    return "new \\" . buildNamespacedClass($class) . "(";
}

function buildNamespacedClass($class) {
    global $config;
    $parts = [$config['namespace']];
    $classParts = explode('\\', $class);

    // Handle plugin_frame=false
    if ($config['plugin_frame'] === false) {
        $classParts = array_filter($classParts, function($part) {
            return $part !== 'PluginFrame';
        });
    }
    // Handle custom plugin_frame value
    elseif (!empty($config['plugin_frame'])) {
        $classParts = array_map(function($part) use ($config) {
            return $part === 'PluginFrame' ? $config['plugin_frame'] : $part;
        }, $classParts);
        
        // Add custom frame if not present at start
        if ($classParts[0] !== $config['plugin_frame']) {
            array_unshift($classParts, $config['plugin_frame']);
        }
    }
    // Default case
    else {
        if ($classParts[0] !== 'PluginFrame') {
            array_unshift($classParts, 'PluginFrame');
        }
    }

    $parts = array_merge($parts, $classParts);
    return implode('\\', array_filter($parts));
}

function shouldSkip($class) {
    static $phpCoreClasses = null;
    
    // Get PHP core classes once
    if ($phpCoreClasses === null) {
        $phpCoreClasses = array_flip(array_map('strtolower', get_declared_classes()));
    }
    
    $class = ltrim($class, '\\');
    $classLower = strtolower($class);
    
    // 1. Skip PHP core classes
    if (isset($phpCoreClasses[$classLower])) {
        return true;
    }
    
    // 2. Skip WordPress core classes
    foreach (WP_CORE_CLASSES as $pattern) {
        $patternLower = strtolower($pattern);
        if (strpos($patternLower, '_') === false) {
            // Exact match check
            if ($classLower === $patternLower) return true;
        } else {
            // Prefix match check
            if (str_starts_with($classLower, $patternLower)) return true;
        }
    }
    
    return false;
}