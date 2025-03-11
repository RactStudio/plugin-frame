<?php
/**
 * Plugin Framework Production Scoper (PHP-Parser Version)
 */
require __DIR__.'/../vendor/autoload.php';

use PhpParser\Node;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\UseUse;
use PhpParser\Node\Name;

// ==================================================
// Configuration
// ==================================================
define('WP_CORE_CLASSES', [
    // Exact matches
    'WP', 'wpdb', 'WP_Error', 'WP_Query', 'WP_Post',
    'WP_User', 'WP_Roles', 'WP_Admin_Bar', 'WP_Widget',
    
    // Prefix matches
    'WP_', 'wp_', 'WC_', 'WooCommerce_', 'Tribe_'
]);

// ==================================================
// Main Execution
// ==================================================
try {
    if ($argc < 3) throw new Exception("Missing required arguments");
    
    $pluginFrameArg = $argv[3] ?? '';
    $config = [
        'namespace' => $argv[1],
        'prefix' => $argv[2],
        'plugin_frame' => ($pluginFrameArg === 'false') ? false : $pluginFrameArg,
    ];
    
    validateInputs($config);
    processFiles(getTargetDir(), $config);

} catch (Exception $e) {
    die("❌ Error: " . $e->getMessage() . PHP_EOL);
}

// ==================================================
// Core Functions
// ==================================================
function validateInputs($config) {
    if (!preg_match('/^[a-zA-Z0-9]{2,50}$/', $config['namespace'])) {
        throw new Exception("Invalid namespace format");
    }
    
    if (!preg_match('/^[a-z0-9]{2,10}$/', $config['prefix'])) {
        throw new Exception("Invalid prefix format");
    }
}

function getTargetDir() {
    $dir = dirname(__DIR__) . '/.dist/plugin-frame';
    if (!is_dir($dir)) throw new Exception("Missing target directory");
    return realpath($dir);
}

function processFiles($targetDir, $config) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($targetDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($files as $file) {
        if ($file->isFile()) {
            processFile($file->getRealPath(), $config);
        }
    }
}

function processFile($path, $config) {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
    if ($extension === 'php') {
        processPHPFile($path, $config);
    } elseif (in_array($extension, ['js', 'css', 'html', 'twig'])) {
        processAssetFile($path, $config);
    }
}

// ==================================================
// PHP File Processor
// ==================================================
function processPHPFile($path, $config) {
    $parser = (new ParserFactory())->createForNewestSupportedVersion();
    $traverser = new NodeTraverser();
    $printer = new Standard();

    $traverser->addVisitor(new class($config) extends NodeVisitorAbstract {
        private $config;
        private $currentNamespace;
        
        public function __construct($config) {
            $this->config = $config;
        }
        
        public function enterNode(Node $node) {
            if ($node instanceof Namespace_) {
                $this->currentNamespace = $node->name ? $node->name->toString() : '';
            }
            return null;
        }
        
        public function leaveNode(Node $node) {
            if ($node instanceof Namespace_) {
                return $this->rewriteNamespace($node);
            }
            
            if ($node instanceof Node\Stmt\Use_) {
                foreach ($node->uses as $use) {
                    $this->rewriteUse($use);
                }
                return $node;
            }
            
            if ($node instanceof Name) {
                return $this->rewriteName($node);
            }
            
            if ($node instanceof Node\Scalar\String_) {
                return $this->rewriteStringClass($node);
            }

            if ($node instanceof Node\Expr\FuncCall) {
                return $this->rewriteFunctionCall($node);
            }

            if ($node instanceof Node\Expr\ConstFetch) {
                return $this->rewriteConstantFetch($node);
            }
            
            return $node;
        }
        
        private function rewriteNamespace(Namespace_ $namespace) {
            $original = $namespace->name ? $namespace->name->toString() : '';
            $newName = $this->buildNamespacedClass($original, true);
            
            // Prevent duplicate namespaces
            if (strpos($newName, $this->config['namespace']) === 0) {
                $newName = substr($newName, strlen($this->config['namespace']) + 1);
            }
            
            $namespace->name = new Name($newName);
            return $namespace;
        }
        
        private function rewriteUse(UseUse $use) {
            $original = $use->name->toString();
            if ($this->shouldSkip($original)) return;
            
            $newName = $this->buildNamespacedClass($original, true);
            $use->name = new Name($newName);
        }
        
        private function rewriteName(Name $name) {
            $original = $name->toString();
            if ($this->shouldSkip($original)) return $name;
            
            // Don't prefix fully qualified names
            if ($name->isFullyQualified()) {
                return $name;
            }
            
            $newName = $this->buildNamespacedClass($original);
            return new Name($newName);
        }
        
        private function rewriteStringClass(Node\Scalar\String_ $node) {
            $original = $node->value;
            if (class_exists($original) && !$this->shouldSkip($original)) {
                $newClass = $this->buildNamespacedClass($original);
                return new Node\Scalar\String_($newClass);
            }
            return $node;
        }
        
        private function rewriteFunctionCall(Node\Expr\FuncCall $node) {
            if ($node->name instanceof Name) {
                $functionName = $node->name->toString();
                if ($this->shouldSkip($functionName)) {
                    return $node;
                }
                $node->name = $this->rewriteName($node->name);
            }
            return $node;
        }
        
        private function rewriteConstantFetch(Node\Expr\ConstFetch $node) {
            $constName = $node->name->toString();
            if ($this->shouldSkip($constName)) {
                return $node;
            }
            
            // Don't prefix true/false/null constants
            if (in_array(strtolower($constName), ['true', 'false', 'null'])) {
                return $node;
            }
            
            $node->name = $this->rewriteName($node->name);
            return $node;
        }
        
        private function buildNamespacedClass($class, $isNamespace = false) {
            $parts = [];
            
            // Add main namespace
            if (!$isNamespace) {
                $parts[] = $this->config['namespace'];
            }
            
            $classParts = explode('\\', $class);
            
            // Handle Plugin Frame namespace
            if ($this->config['plugin_frame'] !== false) {
                $framePart = $this->config['plugin_frame'] ?: 'PluginFrame';
                if ($classParts[0] !== $framePart && !$isNamespace) {
                    array_unshift($classParts, $framePart);
                }
            }
            
            // Filter out empty parts and merge
            $parts = array_merge($parts, array_filter($classParts));
            return implode('\\', $parts);
        }
        
        private function shouldSkip($name) {
            static $phpCore = null;
            static $wpCore = null;
            
            if ($phpCore === null) {
                $phpCore = array_merge(
                    get_defined_functions()['internal'],
                    ['defined', 'class_exists', 'interface_exists', 'trait_exists'],
                    ['true', 'false', 'null']
                );
                
                $wpCore = [
                    'ABSPATH', 'WPINC', 'WP_CONTENT_DIR', 'WP_PLUGIN_DIR',
                    'plugin_dir_path', 'plugin_dir_url', 'plugin_basename',
                    'get_plugin_data', 'load_plugin_textdomain',
                    'add_action', 'do_action', 'add_filter', 'apply_filters'
                ];
            }
            
            $lowerName = strtolower($name);
            
            // Skip PHP core components
            if (in_array($lowerName, array_map('strtolower', $phpCore))) {
                return true;
            }
            
            // Skip WordPress core components
            if (in_array($lowerName, array_map('strtolower', $wpCore))) {
                return true;
            }
            
            // Skip WordPress classes
            foreach (WP_CORE_CLASSES as $pattern) {
                $patternLower = strtolower($pattern);
                if (str_starts_with($lowerName, $patternLower)) {
                    return true;
                }
            }
            
            return false;
        }
    });

    try {
        $code = file_get_contents($path);
        $stmts = $parser->parse($code);
        $modifiedStmts = $traverser->traverse($stmts);
        $newCode = $printer->prettyPrintFile($modifiedStmts);
        file_put_contents($path, $newCode);
        echo "✅ Processed PHP: $path\n";
    } catch (\Throwable $e) {
        echo "❌ Error processing $path: " . $e->getMessage() . "\n";
    }
}

// ==================================================
// Asset File Processor
// ==================================================
function processAssetFile($path, $config) {
    $content = file_get_contents($path);
    $updated = str_replace('pf-', $config['prefix'] . '-', $content);
    
    if ($updated !== $content) {
        file_put_contents($path, $updated);
        echo "✅ Updated Asset: $path\n";
    }
}