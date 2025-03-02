<?php

if ($argc < 3) {
    die("Usage: php pf.php <NEW_NAMESPACE> <NEW_PREFIX>\n");
}

$NEW_NAMESPACE = trim($argv[1]);
$NEW_PREFIX = trim($argv[2]);
$ROOT_DIR = realpath(__DIR__ . "/../.dist/plugin-frame");

if (!$NEW_NAMESPACE || !$NEW_PREFIX) {
    die("ERROR: Missing namespace or prefix arguments.\n");
}

// Exclude vendor directory (commented out for now)
$EXCLUDED_DIRS = [
    // realpath($ROOT_DIR . "/vendor"),
];

// Cache of PHP core classes
$CORE_PHP_CLASSES = array_flip(get_declared_classes());

// Check if a class is a PHP core class
function isCoreClass($className) {
    global $CORE_PHP_CLASSES;
    $className = ltrim($className, '\\'); // Remove leading backslash
    return isset($CORE_PHP_CLASSES[$className]);
}

// Scan and process files
function collectFiles($dir) {
    global $EXCLUDED_DIRS;
    $files = [];

    if (in_array(realpath($dir), $EXCLUDED_DIRS)) {
        return $files;
    }

    foreach (scandir($dir) as $file) {
        if ($file === "." || $file === "..") {
            continue;
        }
        $fullPath = "$dir/$file";

        if (is_dir($fullPath)) {
            $files = array_merge($files, collectFiles($fullPath));
        } elseif (preg_match('/\.(php|js|css|html|twig)$/', $file)) {
            $files[] = $fullPath;
        }
    }

    return $files;
}

// Process PHP files: update namespace, use statements, and class instantiations
function updateNamespaceAndUsages($filePath) {
    global $NEW_NAMESPACE;

    $content = file_get_contents($filePath);

    // Match existing namespace
    if (preg_match('/^namespace\s+([a-zA-Z0-9_\\\\]+);/m', $content, $matches)) {
        $existingNamespace = trim($matches[1]);

        // Avoid double-prepending
        if (strpos($existingNamespace, $NEW_NAMESPACE) !== 0) {
            $newNamespace = "namespace {$NEW_NAMESPACE}\\{$existingNamespace};";
            $content = preg_replace('/^namespace\s+[a-zA-Z0-9_\\\\]+;/m', $newNamespace, $content, 1);
        }
    }

    // Match use statements (including those with aliases)
    $content = preg_replace_callback(
        '/^use\s+([a-zA-Z0-9_\\\\]+)(?:\s+as\s+[a-zA-Z0-9_]+)?;/m',
        function ($match) use ($NEW_NAMESPACE) {
            $existingUse = trim($match[1]);
            $existingUseWithoutSlash = ltrim($existingUse, '\\');

            // Skip core PHP classes
            if (isCoreClass($existingUseWithoutSlash)) {
                return $match[0];
            }

            // Avoid double-prepending
            if (strpos($existingUseWithoutSlash, $NEW_NAMESPACE . '\\') === 0) {
                return $match[0];
            }

            // Reconstruct the use statement with the new namespace
            $newUseStatement = "use {$NEW_NAMESPACE}\\{$existingUseWithoutSlash}";

            // Preserve the alias if it exists
            if (strpos($match[0], ' as ') !== false) {
                $aliasPart = substr($match[0], strpos($match[0], ' as '));
                $newUseStatement .= $aliasPart;
            }

            return $newUseStatement . ';';
        },
        $content
    );

    // Match **only** fully qualified class instantiations
    $content = preg_replace_callback(
        '/new\s+\\\\?([a-zA-Z0-9_\\\\]+)\s*\(/',
        function ($match) use ($NEW_NAMESPACE) {
            $existingClass = trim($match[1]);
            $existingClassWithoutSlash = ltrim($existingClass, '\\');

            // Skip core PHP classes
            if (isCoreClass($existingClassWithoutSlash)) {
                return $match[0];
            }

            // Avoid modifying relative instantiations
            if (strpos($existingClassWithoutSlash, '\\') !== false) {
                return "new \\{$NEW_NAMESPACE}\\{$existingClassWithoutSlash}(";
            }
            return $match[0];
        },
        $content
    );

    file_put_contents($filePath, $content);
    echo "✅ Updated namespace and class references in $filePath\n";
}

// Process other files: update prefix
function updatePrefix($filePath) {
    global $NEW_PREFIX;

    $content = file_get_contents($filePath);
    $updatedContent = preg_replace('/\bpf-/', "{$NEW_PREFIX}-", $content);

    if ($updatedContent !== $content) {
        file_put_contents($filePath, $updatedContent);
        echo "✅ Updated prefix in $filePath\n";
    }
}

// Run processing
$allFiles = collectFiles($ROOT_DIR);

foreach ($allFiles as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === "php") {
        updateNamespaceAndUsages($file);
    } else {
        updatePrefix($file);
    }
}

echo "✅ Namespace, class references, and prefix updates completed successfully.\n";