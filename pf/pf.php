<?php
/**
 * Plugin Framework Production Scoper with Full Logging
 */

// ==================================================
// Command-line Argument Processing & Config Setup
// ==================================================
global $config;

try {
    if ($argc < 7) {
        throw new Exception("Missing required arguments");
    }

    $pluginFrameArg = $argv[7] ?? 'PluginFrame';
    $config = [
        'namespace' => $argv[1],
        'prefix' => $argv[2],
        'name' => $argv[3],
        'name_lower' => strtolower($argv[3]),
        'version' => $argv[4],
        'slug' => $argv[5],
        'rand_num' => $argv[6],
        'slug_upper' => strtoupper(str_replace('-', '_', $argv[5])),
        'slug_lower' => strtolower(str_replace('-', '_', $argv[5])),
        'plugin_frame' => ($pluginFrameArg === 'false') ? false : $pluginFrameArg,
    ];
} catch (Exception $e) {
    fwrite(STDERR, "FATAL ERROR: " . $e->getMessage() . "\n");
    exit(1);
}

// ==================================================
// Logging Configuration
// ==================================================
define('LOG_FILE', realpath(__DIR__ . '/../.dist') . '/build-'.$config['rand_num'].'.log');
@unlink(LOG_FILE); // Remove previous log file
$logHandle = fopen(LOG_FILE, 'w');
if (!$logHandle) die("❌ Cannot create log file");

function log_message($message, $type = 'INFO') {
    global $logHandle;
    $timestamp = date('d-M-Y H:i:s.v');
    $formatted = "[$timestamp][$type] $message\n";
    
    // Write to terminal and log file
    fwrite(STDERR, $formatted);
    fwrite($logHandle, $formatted);
    
    // Immediate flush
    fflush(STDERR);
    fflush($logHandle);
}

// ==================================================
// Initialization
// ==================================================
log_message("=== STARTING PLUGIN FRAME SCOPING PROCESS ===", 'PROCESS');
log_message("Configuration received:", 'CONFIG');
log_message(print_r($config, true), 'CONFIG');

// ==================================================
// Directory Configuration
// ==================================================
$ROOT_DIR = realpath(__DIR__ . "/../.dist/".$config['slug']."-".$config['rand_num']);
log_message("Root directory: $ROOT_DIR", 'CONFIG');

// Exclude directory or a single file from scoping
$EXCLUDED_DIRS = [
    realpath($ROOT_DIR . "/vendor"),
    // realpath($ROOT_DIR . "/src/MyClass.php"),
    // ... add more entries as needed ...
];

log_message("Excluded paths:", 'CONFIG');
foreach ($EXCLUDED_DIRS as $path) {
    log_message("- " . ($path ?: 'INVALID PATH'), 'CONFIG');
}

// ==================================================
// Manual string replacements
// ==================================================
$MANUAL_REPLACEMENTS = [
    [
        'file' => realpath($ROOT_DIR . "/app/Config/Providers.php"), // Exact file path
        'search' => 'protected $baseNamespace = \'PluginFrame\\Providers\';', // Exact match
        'type' => 'namespace', // namespace
    ],
    [
        'file' => realpath($ROOT_DIR . "/app/Core/Helpers/BootstrapHelper.php"), // Exact file path
        'search' => '$prefix = \'PluginFrame\';', // Exact match
        'type' => 'namespace', // namespace
    ],
    [
        'file' => realpath($ROOT_DIR . "/app/Core/Services/ScreenHelp.php"), // Exact file path
        'search' => '#pf-load.pf-page', // Exact match
        'type' => 'prefix', // prefix
    ],
    [
        'file' => realpath($ROOT_DIR . "/"), // Exact directory path
        'search' => 'PLUGIN_FRAME_', // Partial match for all entries
        'type' => 'replace-dir', // replace-dir for all directory files
        'replace' => 'PLUGIN_FRAME', // replace
        'with' => $config['slug_upper'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/"), // Exact directory path
        'search' => 'plugin-frame', // Partial match for all entries
        'type' => 'replace-dir', // replace-dir for all directory files
        'replace' => 'plugin-frame', // replace
        'with' => $config['slug'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/"), // Exact directory path
        'search' => 'plugin_frame', // Partial match for all entries
        'type' => 'replace-dir', // replace-dir for all directory files
        'replace' => 'plugin_frame', // replace
        'with' => $config['slug_lower'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/"), // Exact directory path
        'search' => 'plugin frame', // Partial match for all entries
        'type' => 'replace-dir', // replace-dir for all directory files
        'replace' => 'plugin frame', // replace
        'with' => $config['name_lower'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/"), // Exact directory path
        'search' => 'Plugin Frame', // Partial match for all entries
        'type' => 'replace-dir', // replace-dir for all directory files
        'replace' => 'Plugin Frame', // replace
        'with' => $config['name'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/plugin-frame.php"), // Exact file path
        'search' => 'Plugin Name: Plugin Frame', // Exact match
        'type' => 'replace', // replace
        'replace' => 'Plugin Frame', // replace
        'with' => $config['name'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/plugin-frame.php"), // Exact file path
        'search' => 'Version:     0.9.2', // Exact match
        'type' => 'replace', // replace
        'replace' => '0.9.2', // replace
        'with' => $config['version'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/plugin-frame.php"), // Exact file path
        'search' => 'Text Domain: plugin-frame', // Exact match
        'type' => 'replace', // replace
        'replace' => 'plugin-frame', // replace
        'with' => $config['slug'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/plugin-frame.php"), // Exact file path
        'search' => '\''.$config['slug_upper'].'_NAME\', \'Plugin Frame\'', // Exact match
        'type' => 'replace', // replace
        'replace' => 'Plugin Frame', // replace
        'with' => $config['name'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/plugin-frame.php"), // Exact file path
        'search' => '\''.$config['slug_upper'].'_VERSION\', \'0.9.2\'', // Exact match
        'type' => 'replace', // replace
        'replace' => '0.9.2', // replace
        'with' => $config['version'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/plugin-frame.php"), // Exact file path
        'search' => '\''.$config['slug_upper'].'_SLUG\', \'plugin-frame\'', // Exact match
        'type' => 'replace', // replace
        'replace' => 'plugin-frame', // replace
        'with' => $config['slug'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/app/Config/Bootstrap.php"), // Exact file path
        'search' => '\''.$config['slug_upper'].'_NAME\', \'Plugin Frame\'', // Exact match
        'type' => 'replace', // replace
        'replace' => 'Plugin Frame', // replace
        'with' => $config['name'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/app/Config/Bootstrap.php"), // Exact file path
        'search' => '\''.$config['slug_upper'].'_VERSION\', \'0.9.2\'', // Exact match
        'type' => 'replace', // replace
        'replace' => '0.9.2', // replace
        'with' => $config['version'], // with
    ],
    [
        'file' => realpath($ROOT_DIR . "/app/Config/Bootstrap.php"), // Exact file path
        'search' => '\''.$config['slug_upper'].'_SLUG\', \'plugin-frame\'', // Exact match
        'type' => 'replace', // replace
        'replace' => 'plugin-frame', // replace
        'with' => $config['slug'], // with
    ],
    // ... add more entries as needed ...
];

log_message("Manual replacements configured:", 'CONFIG');
foreach ($MANUAL_REPLACEMENTS as $entry) {
    log_message("- File: " . ($entry['file'] ?? 'INVALID'), 'CONFIG');
    log_message("  Search: " . substr($entry['search'] ?? 'MISSING', 0, 50), 'CONFIG');
    log_message("  Type: " . ($entry['type'] ?? 'UNKNOWN'), 'CONFIG');
}

// ==================================================
// Excluded WordPress classes
// ==================================================
define('WP_CORE_CLASSES', [
    'WP', 'wpdb', 'WP_Error', 'WP_Query', 'WP_Post',
    'WP_User', 'WP_Roles', 'WP_Admin_Bar', 'WP_Widget',
    'ABSPATH', 'WPINC', 'WP_CONTENT_DIR', 'WP_PLUGIN_DIR',
    'plugin_dir_path', 'plugin_dir_url', 'plugin_basename',
    'get_plugin_data', 'load_plugin_textdomain',
    'add_action', 'do_action', 'add_filter', 'apply_filters',
    'WP_', 'wp_', 'WC_', 'WooCommerce_', 'Tribe_'
]);

// ==================================================
// Class collection functions
// ==================================================
function extractFqcnsFromFile($filePath) {
    log_message("Extracting classes from: $filePath", 'DEBUG');
    $content = @file_get_contents($filePath);
    if ($content === false) {
        log_message("Failed to read file: $filePath", 'ERROR');
        return [];
    }

    $namespace = '';
    $classes = [];
    
    if (preg_match('/^namespace\s+([^;]+);/m', $content, $matches)) {
        $namespace = $matches[1];
        log_message("Found namespace: $namespace", 'DEBUG');
    }

    $tokens = token_get_all($content);
    $classToken = false;
    foreach ($tokens as $token) {
        if (is_array($token)) {
            switch ($token[0]) {
                case T_CLASS:
                case T_INTERFACE:
                case T_TRAIT:
                    $classToken = true;
                    break;
                case T_STRING:
                    if ($classToken) {
                        $fqcn = $namespace ? "$namespace\\$token[1]" : $token[1];
                        log_message("Found class: $fqcn", 'DEBUG');
                        $classes[] = $fqcn;
                        $classToken = false;
                    }
                    break;
            }
        } else {
            $classToken = false;
        }
    }
    return $classes;
}

function collectExcludedClasses() {
    global $EXCLUDED_DIRS;
    $excludedClasses = [];
    log_message("Collecting excluded classes...", 'PROCESS');

    foreach ($EXCLUDED_DIRS as $path) {
        if (is_dir($path)) {
            log_message("Processing directory: $path", 'DEBUG');
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $excludedClasses = array_merge(
                        $excludedClasses, 
                        extractFqcnsFromFile($file->getRealPath())
                    );
                }
            }
        } elseif (is_file($path) && pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            log_message("Processing file: $path", 'DEBUG');
            $excludedClasses = array_merge(
                $excludedClasses, 
                extractFqcnsFromFile($path)
            );
        }
    }
    log_message("Collected " . count($excludedClasses) . " excluded classes", 'INFO');
    return array_unique($excludedClasses);
}

$EXCLUDED_CLASSES = collectExcludedClasses();

// ==================================================
// Namespace Transformation
// ==================================================
function transformNamespaceString($namespaceString) {
    global $config;
    $existingParts = explode('\\', $namespaceString);
    
    if ($config['plugin_frame'] === false) {
        log_message("Removing PluginFrame from namespace", 'DEBUG');
        $existingParts = array_filter($existingParts, fn($p) => $p !== 'PluginFrame');
    } elseif ($config['plugin_frame']) {
        log_message("Replacing PluginFrame with: {$config['plugin_frame']}", 'DEBUG');
        $existingParts = array_map(fn($p) => $p === 'PluginFrame' ? $config['plugin_frame'] : $p, $existingParts);
        if ($existingParts[0] !== $config['plugin_frame']) {
            array_unshift($existingParts, $config['plugin_frame']);
        }
    } else {
        log_message("Using default PluginFrame namespace", 'DEBUG');
        if (!in_array('PluginFrame', $existingParts, true)) {
            array_unshift($existingParts, 'PluginFrame');
        }
    }
    
    $transformedParts = array_merge([$config['namespace']], $existingParts);
    $result = implode('\\', array_filter($transformedParts));
    log_message("Transformed namespace: $namespaceString => $result", 'DEBUG');
    return $result;
}

// ==================================================
// Manual Replacement Processor
// ==================================================
function processManualReplacements() {
    global $MANUAL_REPLACEMENTS, $config, $EXCLUDED_DIRS;
    
    $skippedReplacements = [];
    
    foreach ($MANUAL_REPLACEMENTS as $index => $replacement) {
        $logPrefix = "Replacement #" . ($index + 1);
        log_message("$logPrefix - Starting manual replacement processing", 'DEBUG');
        
        try {
            $filePath = $replacement['file'] ?? '';
            $search = $replacement['search'] ?? '';
            $type = $replacement['type'] ?? '';
            $replace = $replacement['replace'] ?? null;
            $with = $replacement['with'] ?? null;

            // Validation
            $validationErrors = [];
            if (!$filePath) $validationErrors[] = 'Missing file';
            if (!$search) $validationErrors[] = 'Missing search';
            if (!$type) $validationErrors[] = 'Missing type';
            
            // Additional validation for specific types
            if ($type === 'replace') {
                if ($replace === null) $validationErrors[] = 'Missing replace';
                if ($with === null) $validationErrors[] = 'Missing with';
            }
            if ($type === 'replace-dir') {
                if ($replace === null) $validationErrors[] = 'Missing replace';
                if ($with === null) $validationErrors[] = 'Missing with';
            }
            
            if (!empty($validationErrors)) {
                $reason = 'Invalid configuration: ' . implode(', ', $validationErrors);
                log_message("$logPrefix - $reason", 'ERROR');
                $skippedReplacements[] = compact('filePath', 'search', 'reason');
                continue;
            }

            log_message("$logPrefix - Target path: $filePath", 'DEBUG');
            log_message("$logPrefix - Search pattern: " . substr($search, 0, 50), 'DEBUG');
            log_message("$logPrefix - Replacement type: $type", 'DEBUG');

            // Handle directory-wide replacements
            if ($type === 'replace-dir') {
                if (!is_dir($filePath)) {
                    $reason = "Directory not found: $filePath";
                    log_message("$logPrefix - $reason", 'ERROR');
                    $skippedReplacements[] = compact('filePath', 'search', 'reason');
                    continue;
                }

                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($filePath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isDir()) continue;

                    $currentFilePath = $file->getRealPath();
                    $isExcluded = false;

                    // Check against excluded directories/files
                    foreach ($EXCLUDED_DIRS as $excludedPath) {
                        if (strpos($currentFilePath, $excludedPath) === 0) {
                            $isExcluded = true;
                            break;
                        }
                    }

                    if ($isExcluded) {
                        log_message("$logPrefix - Skipping excluded file: $currentFilePath", 'DEBUG');
                        continue;
                    }

                    // Process file
                    log_message("$logPrefix - Processing file: $currentFilePath", 'DEBUG');
                    $content = file_get_contents($currentFilePath);
                    if ($content === false) {
                        $reason = "Could not read file: $currentFilePath";
                        log_message("$logPrefix - $reason", 'ERROR');
                        $skippedReplacements[] = compact('filePath', 'search', 'reason');
                        continue;
                    }

                    // Replace in lines containing search pattern
                    $pattern = '/^.*?' . preg_quote($search, '/') . '.*$/m';
                    $newContent = preg_replace_callback($pattern, function($matches) use ($replace, $with) {
                        return str_replace($replace, $with, $matches[0]);
                    }, $content);

                    if ($newContent !== $content) {
                        if (file_put_contents($currentFilePath, $newContent) === false) {
                            $reason = "Failed to write file: $currentFilePath";
                            log_message("$logPrefix - $reason", 'ERROR');
                            $skippedReplacements[] = compact('filePath', 'search', 'reason');
                        } else {
                            log_message("$logPrefix - Updated file: $currentFilePath", 'SUCCESS');
                        }
                    } else {
                        log_message("$logPrefix - No changes in file: $currentFilePath", 'DEBUG');
                    }
                }
                continue; // Skip further processing for this entry
            }

            // Single file validations
            if (!file_exists($filePath)) {
                $reason = "File not found";
                log_message("$logPrefix - $reason", 'ERROR');
                $skippedReplacements[] = compact('filePath', 'search', 'reason');
                continue;
            }

            $content = file_get_contents($filePath);
            if ($content === false) {
                $reason = "Could not read file";
                log_message("$logPrefix - $reason", 'ERROR');
                $skippedReplacements[] = compact('filePath', 'search', 'reason');
                continue;
            }

            if (strpos($content, $search) === false) {
                $reason = "Search pattern not found";
                log_message("$logPrefix - $reason", 'WARNING');
                $skippedReplacements[] = compact('filePath', 'search', 'reason');
                continue;
            }

            // Perform replacement
            $newContent = '';
            switch ($type) {
                case 'namespace':
                    if (!preg_match('/["\']([^"\']+)["\']/', $search, $matches)) {
                        $reason = "No namespace found in pattern";
                        log_message("$logPrefix - $reason", 'ERROR');
                        $skippedReplacements[] = compact('filePath', 'search', 'reason');
                        continue 2;
                    }
                    $oldNamespace = $matches[1];
                    $newNamespace = transformNamespaceString($oldNamespace);
                    $newString = str_replace($oldNamespace, $newNamespace, $search);
                    $newContent = str_replace($search, $newString, $content);
                    log_message("$logPrefix - Namespace replacement: $oldNamespace => $newNamespace", 'DEBUG');
                    break;

                case 'prefix':
                    $newPrefix = $config['prefix'];
                    $replacementString = str_replace('pf', $newPrefix, $search);
                    $newContent = str_replace($search, $replacementString, $content);
                    log_message(
                        "$logPrefix - Prefix replacement: " . 
                        substr($search, 0, 20) . " => " . 
                        substr($replacementString, 0, 20), 
                        'DEBUG'
                    );
                    break;

                case 'replace':
                    $newString = str_replace($replace, $with, $search);
                    $newContent = str_replace($search, $newString, $content);
                    log_message(
                        "$logPrefix - String replacement: " .
                        substr($replace, 0, 20) . " => " .
                        substr($with, 0, 20),
                        'DEBUG'
                    );
                    break;

                default:
                    $reason = "Invalid replacement type: $type";
                    log_message("$logPrefix - $reason", 'ERROR');
                    $skippedReplacements[] = compact('filePath', 'search', 'reason');
                    continue 2;
            }

            if ($newContent !== $content) {
                file_put_contents($filePath, $newContent);
                log_message("$logPrefix - Successfully updated file", 'SUCCESS');
            } else {
                $reason = "No changes detected";
                log_message("$logPrefix - $reason", 'WARNING');
                $skippedReplacements[] = compact('filePath', 'search', 'reason');
            }

        } catch (Exception $e) {
            $reason = "Exception: " . $e->getMessage();
            log_message("$logPrefix - $reason", 'ERROR');
            $skippedReplacements[] = compact('filePath', 'search', 'reason');
        }
    }

    if (!empty($skippedReplacements)) {
        log_message("Skipped replacements report:", 'WARNING');
        foreach ($skippedReplacements as $entry) {
            log_message(sprintf(
                "File: %s | Search: %s | Reason: %s",
                $entry['filePath'] ?? 'N/A',
                substr($entry['search'] ?? '', 0, 50),
                $entry['reason'] ?? 'Unknown'
            ), 'WARNING');
        }
    }
    log_message("Completed manual replacements", 'PROCESS');
}

// ==================================================
// Main Execution
// ==================================================
try {
    // Validation
    function validateInputs($config) {
        log_message("Validating inputs...", 'PROCESS');
        if (!preg_match('/^[a-zA-Z0-9]{2,50}$/', $config['namespace'])) {
            throw new Exception("Invalid namespace format");
        }
        if (!preg_match('/^[a-z0-9]{2,10}$/', $config['prefix'])) {
            throw new Exception("Invalid prefix format");
        }
        log_message("Input validation passed", 'SUCCESS');
    }
    validateInputs($config);
    
    // File processing
    function getTargetDir($ROOT_DIR) {
        if (!is_dir($ROOT_DIR)) throw new Exception("Missing target directory");
        return realpath($ROOT_DIR);
    }
    
    function processFiles($targetDir) {
        global $EXCLUDED_DIRS;
        log_message("Processing files in: $targetDir", 'PROCESS');

        $excludedDirs = [];
        $excludedFiles = [];
        foreach ($EXCLUDED_DIRS as $path) {
            if (is_dir($path)) {
                $excludedDirs[] = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            } else {
                $excludedFiles[] = $path;
            }
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        $processedCount = 0;
        foreach ($files as $file) {
            if (!$file->isFile()) continue;
            $filePath = $file->getRealPath();
            
            if (in_array($filePath, $excludedFiles, true)) {
                log_message("Skipping excluded file: $filePath", 'DEBUG');
                continue;
            }
            
            foreach ($excludedDirs as $dir) {
                if (strpos($filePath, $dir) === 0) {
                    log_message("Skipping excluded directory: $filePath", 'DEBUG');
                    continue 2;
                }
            }
            
            processFile($filePath);
            $processedCount++;
        }
        log_message("Processed $processedCount files", 'SUCCESS');
    }

    function processFile($path) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        log_message("Processing file: $path", 'DEBUG');
        
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
            default:
                log_message("Unsupported file type: $extension", 'WARNING');
        }
    }

    function processPHPFile($path) {
        log_message("Processing PHP file: $path", 'DEBUG');
        $content = file_get_contents($path);
        $original = $content;
        
        $content = preg_replace_callback('/^namespace\s+([^;]+);/m', 'updateNamespace', $content);
        $content = preg_replace_callback('/^use\s+([^;]+);/m', 'updateUseStatement', $content);
        $content = preg_replace_callback('/\bnew\s+\\\\+([\w\\\\]+)\s*\(/', 'updateClassInstantiation', $content);

        if ($content !== $original) {
            file_put_contents($path, $content);
            log_message("Updated PHP file: $path", 'SUCCESS');
        }
    }

    function processAssetFile($path) {
        global $config;
        log_message("Processing asset file: $path", 'DEBUG');
        $content = file_get_contents($path);
        $updated = str_replace('pf-', $config['prefix'] . '-', $content);
        
        if ($updated !== $content) {
            file_put_contents($path, $updated);
            log_message("Updated asset file: $path", 'SUCCESS');
        }
    }

    // Namespace handling functions
    function updateNamespace($matches) {
        global $config;
        $existing = trim($matches[1]);
        $parts = [$config['namespace']];
        $existingParts = explode('\\', $existing);

        if ($config['plugin_frame'] === false) {
            $existingParts = array_filter($existingParts, fn($p) => $p !== 'PluginFrame');
        } elseif ($config['plugin_frame']) {
            $existingParts = array_map(fn($p) => $p === 'PluginFrame' ? $config['plugin_frame'] : $p, $existingParts);
            if ($existingParts[0] !== $config['plugin_frame']) array_unshift($existingParts, $config['plugin_frame']);
        } else {
            if (!in_array('PluginFrame', $existingParts, true)) array_unshift($existingParts, 'PluginFrame');
        }

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
        
        return isset($parts[1]) ? "use $newClass as {$parts[1]};" : "use $newClass;";
    }

    function updateClassInstantiation($matches) {
        global $config;
        $class = trim($matches[1]);
        if (strpos($class, $config['namespace']) === 0) return $matches[0];
        $class = ltrim($class, '\\');
        if (shouldSkip($class)) return $matches[0];
        return "new \\" . buildNamespacedClass($class) . "(";
    }

    function buildNamespacedClass($class) {
        global $config;
        $classParts = explode('\\', $class);
        if ($config['plugin_frame'] === false) {
            $classParts = array_filter($classParts, fn($p) => $p !== 'PluginFrame');
        } elseif ($config['plugin_frame']) {
            $classParts = array_map(fn($p) => $p === 'PluginFrame' ? $config['plugin_frame'] : $p, $classParts);
            if ($classParts[0] !== $config['plugin_frame']) array_unshift($classParts, $config['plugin_frame']);
        } else {
            if ($classParts[0] !== 'PluginFrame') array_unshift($classParts, 'PluginFrame');
        }
        return $config['namespace'] . '\\' . implode('\\', array_filter($classParts));
    }

    function shouldSkip($class) {
        static $phpCoreClasses = null;
        global $EXCLUDED_CLASSES;

        if ($phpCoreClasses === null) {
            $phpCoreClasses = array_flip(array_map('strtolower', get_declared_classes()));
        }
        
        $class = ltrim($class, '\\');
        $classLower = strtolower($class);
        
        if (isset($phpCoreClasses[$classLower])) return true;
        
        foreach (WP_CORE_CLASSES as $pattern) {
            $patternLower = strtolower($pattern);
            if (strpos($patternLower, '_') === false) {
                if ($classLower === $patternLower) return true;
            } else {
                if (str_starts_with($classLower, $patternLower)) return true;
            }
        }
        
        if (in_array($class, $EXCLUDED_CLASSES, true)) return true;
        
        return false;
    }

    // Execute processing
    log_message("Starting file processing...", 'PROCESS');
    processFiles(getTargetDir($ROOT_DIR));
    
    log_message("Starting manual replacements...", 'PROCESS');
    processManualReplacements();

    // ==================================================
    // Composer Autoloader Hash Replacement
    // ==================================================
    log_message("Starting Composer autoloader hash replacement...", 'PROCESS');

    $autoloadFilePath = $ROOT_DIR . '/vendor/autoload.php';
    if (!file_exists($autoloadFilePath)) {
        throw new Exception("autoload.php not found in vendor directory");
    }

    $autoloadContent = file_get_contents($autoloadFilePath);
    if ($autoloadContent === false) {
        throw new Exception("Failed to read autoload.php");
    }

    // Extract existing hash
    if (!preg_match('/ComposerAutoloaderInit([a-f0-9]{32})/', $autoloadContent, $matches)) {
        throw new Exception("Could not find existing Composer autoloader hash in autoload.php");
    }
    $oldHash = $matches[1];
    log_message("Existing Composer autoloader hash: $oldHash", 'INFO');

    // Generate new hash
    $newHash = bin2hex(random_bytes(16));
    log_message("Generated new Composer autoloader hash: $newHash", 'INFO');

    // Collect files to process
    $composerDir = $ROOT_DIR . '/vendor/composer';
    $filesToProcess = [$autoloadFilePath];

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($composerDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $filesToProcess[] = $file->getRealPath();
        }
    }

    log_message("Processing " . count($filesToProcess) . " files for hash replacement", 'INFO');

    // Replace old hash with new hash
    $totalReplacements = 0;
    foreach ($filesToProcess as $file) {
        log_message("Processing file: $file", 'DEBUG');
        $content = file_get_contents($file);
        if ($content === false) {
            log_message("Failed to read file: $file", 'ERROR');
            continue;
        }

        $newContent = str_replace($oldHash, $newHash, $content, $count);
        if ($count > 0) {
            $bytesWritten = file_put_contents($file, $newContent);
            if ($bytesWritten === false) {
                log_message("Failed to write to file: $file", 'ERROR');
                continue;
            }
            log_message("Replaced $count instances in $file", 'SUCCESS');
            $totalReplacements += $count;
        } else {
            log_message("No replacements needed in $file", 'DEBUG');
        }
    }

    log_message("Total hash replacements: $totalReplacements", 'INFO');
    log_message("Composer autoloader hash replacement completed", 'SUCCESS');

    log_message("=== PLUGIN FRAME SCOPING COMPLETED SUCCESSFULLY ===", 'SUCCESS');

} catch (Exception $e) {
    log_message("FATAL ERROR: " . $e->getMessage(), 'ERROR');
    exit(1);
} finally {
    fclose($logHandle);
}