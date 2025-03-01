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

// Exclude vendor directory
$EXCLUDED_DIRS = [
    realpath($ROOT_DIR . "/vendor"),
];

// Scan and process files
function collectFiles($dir)
{
    global $EXCLUDED_DIRS;
    $files = [];

    // if (in_array(realpath($dir), $EXCLUDED_DIRS)) {
    //     return $files;
    // }

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
function updateNamespaceAndUsages($filePath)
{
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

    // Match use statements
    $content = preg_replace_callback('/^use\s+([a-zA-Z0-9_\\\\]+);/m', function ($match) {
        global $NEW_NAMESPACE;
        $existingUse = trim($match[1]);

        // Avoid double-prepending
        if (strpos($existingUse, $NEW_NAMESPACE) !== 0) {
            return "use {$NEW_NAMESPACE}\\{$existingUse};";
        }
        return $match[0];
    }, $content);

    // Match **only** fully qualified class instantiations
    $content = preg_replace_callback('/new\s+\\\\?([a-zA-Z0-9_\\\\]+)\s*\(/', function ($match) {
        global $NEW_NAMESPACE;
        $existingClass = trim($match[1]);

        // Avoid modifying relative instantiations like `new AuthMiddleware();`
        if (strpos($existingClass, "\\") !== false) {
            return "new \\{$NEW_NAMESPACE}\\{$existingClass}(";
        }
        return $match[0];
    }, $content);

    file_put_contents($filePath, $content);
    echo "✅ Updated namespace and class references in $filePath\n";
}

// Process other files: update prefix
function updatePrefix($filePath)
{
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
