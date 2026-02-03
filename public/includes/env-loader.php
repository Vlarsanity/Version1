<?php

/**
 * Environment Loader
 * Loads environment variables from .env files
 * Priority: .env.local (development) > .env.prod (production)
 */

class EnvLoader
{
    private static $loaded = false;

    /**
     * Load environment variables
     * @param string $environment 'local' or 'prod' (optional, auto-detects)
     */
    public static function load($environment = null)
    {
        if (self::$loaded) {
            return; // Already loaded
        }

        $baseDir = dirname(__DIR__, 2);

        // Auto-detect environment if not specified
        if ($environment === null) {
            if (file_exists($baseDir . '/.env.local')) {
                $environment = 'local';
            } elseif (file_exists($baseDir . '/.env.prod')) {
                $environment = 'prod';
            } elseif (file_exists($baseDir . '/.env')) {
                $environment = 'generic';
            } else {
                throw new Exception('No .env file found. Please create .env.local or .env.prod');
            }
        }

        // Determine which file to load
        $envFile = match ($environment) {
            'local' => $baseDir . '/.env.local',
            'prod' => $baseDir . '/.env.prod',
            'generic' => $baseDir . '/.env',
            default => throw new Exception("Invalid environment: $environment")
        };

        // Check if file exists
        if (!file_exists($envFile)) {
            throw new Exception("Environment file not found: $envFile");
        }

        // Load the file
        self::parseEnvFile($envFile);

        // Mark as loaded
        self::$loaded = true;

        // Log which environment was loaded (optional)
        error_log("Environment loaded: $environment from $envFile");
    }

    /**
     * Parse .env file and load into $_ENV and $_SERVER
     * @param string $filePath
     */
    private static function parseEnvFile($filePath)
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Skip comments
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Skip lines without '='
            if (strpos($line, '=') === false) {
                continue;
            }

            // Parse key=value
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Remove quotes if present
            $value = self::removeQuotes($value);

            // Set in both $_ENV and $_SERVER for compatibility
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;

            // Also set using putenv() for maximum compatibility
            putenv("$key=$value");
        }
    }

    /**
     * Remove surrounding quotes from value
     * @param string $value
     * @return string
     */
    private static function removeQuotes($value)
    {
        // Remove single quotes
        if (str_starts_with($value, "'") && str_ends_with($value, "'")) {
            return substr($value, 1, -1);
        }

        // Remove double quotes
        if (str_starts_with($value, '"') && str_ends_with($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * Get environment variable
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }

    /**
     * Check if environment variable exists
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return isset($_ENV[$key]) || isset($_SERVER[$key]) || getenv($key) !== false;
    }

    /**
     * Get current environment name
     * @return string
     */
    public static function getEnvironment()
    {
        return self::get('APP_ENV', 'production');
    }

    /**
     * Check if running in local/development mode
     * @return bool
     */
    public static function isLocal()
    {
        return in_array(self::getEnvironment(), ['local', 'development', 'dev']);
    }

    /**
     * Check if running in production mode
     * @return bool
     */
    public static function isProduction()
    {
        return self::getEnvironment() === 'production' || self::getEnvironment() === 'prod';
    }
}

// Auto-load when file is included
EnvLoader::load();
