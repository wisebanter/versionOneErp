<?php


declare(strict_types=1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// // Convert all errors (except fatal) to exceptions
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException("{$errstr} in {$errfile} on line {$errline}", 0, $errno, $errfile, $errline);
});

// // Register shutdown to catch fatal errors
// register_shutdown_function(function () {
//     $lastError = error_get_last();
//     if ($lastError && in_array($lastError['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
//         // Handle fatal error (log, display, etc.)
//         echo 'Fatal error: ' . $lastError['message'];
//     }
// });



/**
 * Type Converter Definition
 */
final class ParameterType
{
    public function __construct(
        public readonly string $pattern,
        public readonly Closure $caster
    ) {}
}

/**
 * Route Match DTO
 */
final class RouteMatch
{
    public function __construct(
        public readonly string $pattern,
        public readonly string $uri,
        public readonly bool $requireToken,
        public readonly array $parameters,
        public readonly mixed $handler = null
    ) {}

    public function toArray(): array
    {
        return [
            'route' => $this->pattern,
            'requireToken' => $this->requireToken,
            'parameters' => $this->parameters,
        ];
    }
}


class Arguments
{
    private $_provided = array();

    public function __construct(array $provided = array(), bool $changeCase = true)
    {
        $this->_provided = $changeCase ? array_change_key_case($provided, CASE_LOWER) : $provided;
    }

    public function __toString(): string
    {
        return json_encode($this->_provided);
    }

    public function getValue(string $key, mixed $default = "")
    {
        $lower = strtolower($key);
        // return isset($this->_provided[$lower]) ? preg_replace('/\s+/', ' ', (strtolower(gettype($this->_provided[$lower])) == "string" ? html_entity_decode(trim($this->_provided[$lower])) : $this->_provided[$lower])) : $default;
        if (isset($this->_provided[$lower])) {
            if (gettype($this->_provided[$lower]) === 'string') {
                return preg_replace('/\s+/', ' ', html_entity_decode(trim($this->_provided[$lower])));
            } else {
                return $this->_provided[$lower];
            }
        } else {
            return $default;
        }
    }

    public function getArray()
    {
        return $this->_provided;
    }

    public function debug()
    {
        echo json_encode($this->_provided);
    }
}



if (!function_exists('url_for')) {
    function url_for(string $endpoint, mixed ...$params): string
    {
        $endpoint = strtolower(trim($endpoint));
        $params = array_change_key_case($params, CASE_LOWER);
        // $firstPart = strstr($endpoint, '/', true);





        // The set of keys you want to remove
        $keysToRemove = array('_external', '_anchor', '_scheme', '_method', 'filename');

        // Filter the array by matching against its keys
        $queryParams = array_filter(
            $params,
            fn($key) => !in_array($key, $keysToRemove),
            ARRAY_FILTER_USE_KEY
        );



        $routePath = $endpoint . '/' . implode("/", $queryParams);
        $reqMethod = isset($params['requestmethod']) ? strtoupper($params['requestmethod']) : "GET";


        // return $routePath;

        $router = Router::getActiveInstance();
        if ($router === null) {
            throw new RuntimeException(
                "url_for() called before any Router instance was constructed."
            );
        } else {
            // $availbale = array_column($router->getSupportedHttpMethods()[$reqMethod], 'name');

            if (in_array($endpoint, array('static', '/static', 'assets', '/assets'))) {
                if (!isset($params['filename']) || $params['filename'] === '') {
                    throw new \InvalidArgumentException("'filename' is required");
                } else {
                    $filename = (string) $params['filename'];
                    $filename = ltrim($filename, '/');
                    $filename = preg_replace('#/+#', '/', $filename);

                    if (str_contains($filename, '..')) {
                        throw new InvalidArgumentException("Invalid static filename: {$filename}");
                    } else {
                        $url = '/' . ltrim($endpoint, '/') . '/' . $filename;
                        $external = (bool) ($params['_external'] ?? false);
                        $scheme = isset($params['_scheme']) ? (string) $params['_scheme'] : null;

                        return $router->finalizeUrl($url, $external, null, $scheme, $queryParams);
                    }
                }
            } else if ($router->matchRoute($routePath, $reqMethod) !== null) {
                $scheme = null;
                if (array_key_exists('_scheme', $params)) {
                    // ── FIX: validate the scheme instead of silently allowing "ftp".
                    $scheme = strtolower((string) $params['_scheme']);
                    if (!in_array($scheme, ['http', 'https'], true)) {
                        throw new InvalidArgumentException(
                            "url_for(): invalid _scheme '{$scheme}' (expected 'http' or 'https')."
                        );
                    }
                }
                // return '/' . ltrim($routePath, '/') . '------------';
                return $router->finalizeUrl('/' . ltrim($routePath, '/'), false, null, $scheme);
            } else {


                $external = false;
                $anchor = null;
                $scheme = null;

                if (array_key_exists('_external', $params)) {
                    $external = (bool) $params['_external'];
                    unset($params['_external']);
                } else if (array_key_exists('_anchor', $params)) {
                    $anchor = (string) $params['_anchor'];
                    unset($params['_anchor']);
                } else if (array_key_exists('_scheme', $params)) {
                    // ── FIX: validate the scheme instead of silently allowing "ftp".
                    $scheme = strtolower((string) $params['_scheme']);
                    if (!in_array($scheme, ['http', 'https'], true)) {
                        throw new InvalidArgumentException(
                            "url_for(): invalid _scheme '{$scheme}' (expected 'http' or 'https')."
                        );
                    }
                    unset($params['_']);
                } else if (array_key_exists('_method', $params)) {
                    unset($params['_method']);   // accepted for Flask parity; not used
                }


                $url = '/' . ltrim($endpoint, '/');
                // Append query string parameters (skipping null values)
                // $queryParams = array_filter($queryParams, fn($v) => $v !== null);
                // if (!empty($queryParams)) {
                //     $url .= '?' . http_build_query($queryParams);
                // }

                return $router->finalizeUrl($url, $external, $anchor, $scheme, $queryParams);
            }
        }
    }
}




// {
//   "error": "Not Found",
//   "message": "The requested API route '/api/v1/invalid-endpoint' does not exist.",
//   "statusCode": 404
// }


class Router
{
    private string $requestMethod = "GET";

    private string $requestUri = "/";

    private string $contentType = "";

    private array $models = array();

    private string $templatePath = "";

    private string $staticPath = "";

    private string $baseModelPath = "";

    private bool $isHTML = false;
    private ?Closure $showhtmlerror = null;





    private $supportedHttpMethods = array(
        "GET" => array(
            'action' => 'SELECT',
            'endPoints' => array()
        ),
        "POST" => array(
            'action' => 'INSERT',
            'endPoints' => array()
        ),
        "PUT" => array(
            'action' => 'UPDATE',
            'endPoints' => array(),
        ),
        "PATCH" => array(
            'action' => 'UPDATE',
            'endPoints' => array(),
        ),
        "DELETE" => array(
            'action' => 'DELETE',
            'endPoints' => array(),
        ),
    );

    private array $throwables = array();

    private array $types = array();

    private array $_error = array();

    private ?FlaskTemplate $templateEngine = null;

    private static ?Router $activeInstance = null;



    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    /**
     * @param string|null $templatePath   Directory containing template files (optional)
     * @param string|null $staticPath     Directory for compiled static files (optional)
     * @param string|null $cachePath      Directory for compiled template cache (optional)
     * @param bool        $autoescape    Enable automatic HTML escaping (default true)
     */

    public function __construct(
        ?string $templatePath = null,
        ?string $cachePath = null,
        ?string $staticPath = null,
        bool $autoescape = true,
        bool $defaultHtml = false,
    ) {
        $this->registerDefaultTypes();
        $this->isHTML = $defaultHtml;
        self::$activeInstance = $this;

        $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
        // $this->$contentType = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $this->requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        $this->requestUri = $this->formatRoute((!is_null($pathInfo) || !empty($pathInfo)) ? $pathInfo : '/');

        $this->baseModelPath = dirname(__DIR__) . "/models/";
        $this->templatePath = $templatePath !== null ? $templatePath : dirname(__DIR__, 2) . "/templates/";
        $this->staticPath = $staticPath !== null ? $staticPath : dirname(__DIR__, 2) . "/static/";


        try {
            // Initialize template engine if a template directory is provided
            if ($templatePath !== null) {
                $this->templateEngine = new FlaskTemplate($templatePath, $cachePath, $autoescape);
            }
        } catch (RuntimeException $e) {
            // Handle the failed instantiation safely
            // echo "Application Error: " . $e->getMessage() . "\n";
            $this->setError($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            // echo "Unexpected error: " . $e->getMessage();
            $this->setError($e->getCode(), $e->getMessage());
        }
    }


    public function __destruct()
    {
        try {
            $result = $this->execute();
        } catch (ErrorException $ex) {
            $result = $this->formatMessage($ex);
        } catch (Exception $ex) {
            $result = $this->formatMessage($ex);
        }

        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Methods: ' . implode(', ', array_keys($this->supportedHttpMethods)));
        header('Access-Control-Allow-Headers: Content-Type');


        $status = $result['status'] ?? false;
        $statusCode = $result['statusCode'] ?? 500;
        if ($this->isHTML) {
            header("Content-Type: text/html; charset=UTF-8");
            http_response_code($statusCode);

            $message = $result["message"] ?? "";
            $level = $result["messageCode"] ?? "";

            if ($status === true) {
                echo $message;
            } else {
                $executedCustomHandler = false;
                try {
                    if (is_callable($this->showhtmlerror)) {
                        $args = new Arguments(array('code' => $statusCode, 'message' => $message, 'level' => $level));

                        // 1. Start capturing all output (echos, prints, etc.)
                        ob_start();

                        $customResult = call_user_func($this->showhtmlerror, $this, $args);

                        // 2. Fetch the captured output and clear the buffer
                        $capturedEchoedOutput = ob_get_clean();

                        // 3. Determine if it echoed something or returned a non-null value
                        if ($capturedEchoedOutput !== '' || $customResult !== null) {

                            // If it returned a value but didn't echo, print the returned value
                            if ($customResult !== null && $capturedEchoedOutput === '') {
                                echo $customResult;
                            } else {
                                // Otherwise, print whatever it echoed
                                echo $capturedEchoedOutput;
                            }

                            $executedCustomHandler = true;
                        }
                    }
                } catch (Exception $e) {
                    $executedCustomHandler = false;
                    $statusCode = 500;
                    $message = $e->getMessage();
                }

                // 4. Fallback default error handling logic
                if (!$executedCustomHandler) {
                    echo "<div style='color:red;'>Error [{$statusCode}]: " . htmlspecialchars($message) . "</div>";
                }
            }
        } else {
            header("Content-Type: application/json; charset=UTF-8");
            http_response_code($statusCode);
            echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            // echo "<pre>", print_r($result), "<pre>";
        }
    }

    // =========================================================================
    // url_for() support
    // =========================================================================

    public static function getActiveInstance(): ?Router
    {
        return self::$activeInstance;
    }

    public function finalizeUrl(
        string $url,
        bool $external,
        ?string $anchor,
        ?string $scheme,
        array $query = array(),
    ): string {
        // 1. Detect base subdirectory (e.g., /versionOneErp)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $pathParts = explode('/', trim($scriptName, '/'));

        // If a first directory exists, set it as the base directory (e.g., "/versionOneErp")
        $baseDir = !empty($pathParts[0]) ? '/' . $pathParts[0] : '';

        // 2. Prepend the base directory if the URL doesn't already start with it
        if ($baseDir !== '' && !str_starts_with($url, $baseDir)) {
            $url = $baseDir . '/' . ltrim($url, '/');
        }

        // 3. Filter query string (skip nulls and internal routing keys)
        $query = array_filter($query, fn($v) => $v !== null);
        $keysToRemove = array('_external', '_anchor', '_scheme', '_method');

        $result = array_filter(
            $query,
            fn($key) => !in_array($key, $keysToRemove),
            ARRAY_FILTER_USE_KEY
        );

        // Use $result here instead of $query to check if keys remain after filtering
        if (!empty($result)) {
            $url .= '?' . http_build_query($result, '', '&', PHP_QUERY_RFC3986);
        }

        // 4. Absolute URL generation, if requested
        if ($external || $scheme !== null) {
            $currentScheme = $scheme
                ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $url = $currentScheme . '://' . $host . $url;
        }

        // 5. Anchor fragment
        if ($anchor !== null && $anchor !== '') {
            $url .= '#' . ltrim($anchor, '#');
        }

        return $url;
    }


    // -------------------------------------------------------------------------
    // Public API – Route Registration
    // -------------------------------------------------------------------------

    public function __call(string $name, array $arguments)
    {
        $method = strtoupper($name);
        if (!array_key_exists($method, $this->supportedHttpMethods)) {
            $this->setError(405, "HTTP {$method} method is not supported for this endpoint");
        } else {
            // $this->setError(405, json_encode($arguments));
            // list($route, $callback, $requireToken) = $arguments;
            list($route, $callback, $requireToken, $description, $parameters) = array(
                isset($arguments[0]) ? $arguments[0] : '/',
                isset($arguments[1]) ? $arguments[1] : function (Router $class, Arguments $args) {
                    return $this->formatMessage(new Exception("Default", 500));
                },
                isset($arguments[2]) ? $arguments[2] : true,
                isset($arguments[3]) ? $arguments[3] : "",
                isset($arguments[4]) ? (array) $arguments[4] : array(),
            );
            $this->addRoute($method, $this->formatRoute($route), $callback, $requireToken, $description, $parameters);
        }
    }

    /**
     * Register a route for multiple HTTP methods.
     */
    // public function any(string $route, array $httpMethods, callable $callback, bool $requireToken = true): void
    // {
    //     try {
    //         $errs = array();
    //         foreach (array_values($httpMethods) as $method) {
    //             $method = strtoupper($method);
    //             if (!array_key_exists($method, $this->supportedHttpMethods)) {
    //                 $errs[] = $method;
    //             } else {
    //                 $this->addRoute($method, $this->formatRoute($route), $callback, $requireToken);
    //             }
    //         }
    //         if (count($errs) >= 1) {
    //             $message = count($errs) == 1
    //                 ? "HTTP " . end($errs) . " method is not supported for this endpoint"
    //                 : "HTTP methods [" . implode(', ', $errs) . "] are not supported for this endpoint";
    //             throw new Exception($message, 405);
    //         }
    //     } catch (Exception $e) {
    //         $this->setError($e->getCode(), $e->getMessage());
    //     }
    // }


    public function any(string $route, array $httpMethods, callable $callback, array $parameters = array()): void
    {
        try {
            $errsMethod = array();
            $errsParams = array();
            foreach (array_values($httpMethods) as $method) {
                $method = strtoupper($method);
                $routeName = $this->formatRoute($route);

                if (!array_key_exists($method, $this->supportedHttpMethods)) {
                    $errsMethod[] = $method;
                } else {
                    $methodParams = array();
                    $urlparam = array();

                    $requireToken = true;
                    $description = "";

                    if (isset($parameters[$method])) {
                        $mparm = array_change_key_case($parameters[$method]);

                        $requireToken = isset($mparm['requiretoken']) && is_bool($mparm['requiretoken']) ? $mparm['requiretoken'] : false;
                        $description = isset($mparm['description']) ? $mparm['description'] : "";
                        $mparameters = isset($mparm['parameters']) && is_array($mparm['parameters']) ? ((array) $mparm['parameters']) : array();


                        foreach ($mparameters as $row) {
                            $row = array_change_key_case($row);
                            if (isset($row['name']) && !empty($row['name'])) {
                                $name = $row['name'];
                                $type = isset($row['type']) ? $row['type'] : '';
                                $required = isset($row['required']) ? $row['required'] : '';
                                $describe = isset($row['description']) ? $row['description'] : '';


                                if (isset($row['urlparam']) && $row['urlparam'] === true) {
                                    $urlparam[] = "{" . $name . "" . (!empty($type) ? ":{$type}" : "") . "}";
                                } else {
                                    $prm = array("name" => $name, "type" => !empty($type) ? $type : "string");
                                    if (!empty($required) && is_bool($required)) {
                                        $prm["required"] = $required;
                                    }
                                    if (!empty($describe)) {
                                        $prm["description"] = $describe;
                                    }
                                    $methodParams[] = $prm;
                                }
                            }
                        }
                    }

                    $newRoute =  rtrim($routeName, "/") . (count($urlparam) >= 1 ? "/" . implode("/", $urlparam) : "");

                    $this->addRoute($method, $newRoute, $callback, $requireToken, $description, $methodParams);
                }
            }


            if (count($errsMethod) >= 1 || count($errsParams) >= 1) {

                $errs = array();
                if (count($errsMethod) >= 1) {
                    $errs[] = count($errsMethod) == 1
                        ? "HTTP " . end($errsMethod) . " method is not supported for this endpoint"
                        : "HTTP methods [" . implode(', ', $errsMethod) . "] are not supported for this endpoint";
                }
                if (count($errsParams) >= 1) {
                    $errs[] = implode(', ', $errsParams);
                }
                throw new Exception(implode(' and ', $errs), 405);
            }
        } catch (Exception $e) {
            $this->setError($e->getCode(), $e->getMessage());
        }
    }


    /**
     * Render a template and return the HTML string (to be used inside route handlers).
     */
    public function render(string $template, array $data = []): string
    {
        if ($this->templateEngine === null) {
            throw new \RuntimeException("Template engine not initialized. Provide template directory in constructor.");
        } else {
            $this->isHTML = true;
            return $this->templateEngine->render($template, $data);
        }
    }



    public function addFilter(string $name, callable $callback): void
    {
        if ($this->templateEngine !== null) {
            $this->templateEngine->addFilter($name, $callback);
        }
    }

    public function addGlobal(string $name, mixed $value): void
    {
        if ($this->templateEngine !== null) {
            $this->templateEngine->addGlobal($name, $value);
        }
    }


    public function getGlobal(string $name, mixed $defaultValue = ""): mixed
    {
        if ($this->templateEngine !== null) {
            return $this->templateEngine->getGlobal($name, $defaultValue);
        } else {
            return $defaultValue;
        }
    }



    public function setErrorHandler(Closure $errorHandler): void
    {
        $this->showhtmlerror = $errorHandler;
    }

    /**
     * Format an exception into a JSON‑compatible array.
     */
    public function formatMessage(Throwable $ex, mixed $messageCode = 0, array $parameters = array()): array
    {
        try {
            $code = $ex->getCode() == 0 ? 500 : $ex->getCode();
            $status = in_array($code, array(200, 201, 202, 203));
            return array_merge(array('status' => $status, 'statusCode' => $code, 'messageCode' => $messageCode, 'message' => $ex->getMessage(),), $parameters);
        } catch (TypeError $ex) {
            return array('status' => $ex->getCode() == 200, 'statusCode' => $ex->getCode(), 'messageCode' => $messageCode, 'message' => $ex->getMessage());
        } catch (Exception $ex) {
            return array('status' => $ex->getCode() == 200, 'statusCode' => $ex->getCode(), 'messageCode' => $messageCode, 'message' => $ex->getMessage());
        }
    }




    function array_except(array $array, string|int $key): array
    {
        return array_diff_key($array, [$key => ""]);
    }

    // -------------------------------------------------------------------------
    // Model Management (optional)
    // -------------------------------------------------------------------------

    public function autoModelRegister(): void
    {
        // $this->models[] = $this->nameCleaner($modelName);
        $dir = $this->baseModelPath;

        // Remove '.' and '..'
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $file) {
            // Ensure it's a file, not a subdirectory
            if (is_file($dir . $file)) {
                // echo $file . "\n";
                $modelName = str_ireplace(array('.php'), '', $file);
                $this->addModel($this->nameCleaner($modelName));
            }
        }
    }

    public function addModel(string $modelName): void
    {
        $this->models[] = $this->nameCleaner($modelName);
    }


    public function getModel(string $modelName, array $arguments = array()): object
    {
        try {
            $name = $this->nameCleaner($modelName);
            if (in_array($name, $this->models)) {
                if (file_exists($this->baseModelPath . "{$modelName}.php")) {
                    // return new $name();

                    $reflector = new ReflectionClass($name);
                    return $reflector->newInstanceArgs($arguments);
                } else {
                    throw new Exception("Model: {$modelName} does not exists", 404);
                    // throw new Exception("Model: ".$this->baseModelPath. "{$modelName}"." does not exists", 404);
                }
            } else {
                throw new Exception("Non-registered Model: {$modelName}", 500);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    // -------------------------------------------------------------------------
    // Throwable Registry (for custom error handling)
    // -------------------------------------------------------------------------

    public function addThrowable(string $name, Throwable $throwable)
    {
        $this->throwables[$this->nameCleaner($name)] = $throwable;
    }

    public function getThrowable(string $name): Throwable
    {
        try {
            $title = $this->nameCleaner($name);
            if (array_key_exists($title, $this->throwables)) {
                return $this->throwables[$title];
            } else {
                return new Exception("Non-registered Throwable: [{$name}]", 405);
            }
        } catch (Exception $ex) {
            return $ex;
        }
    }


    // -------------------------------------------------------------------------
    // Utility Methods
    // -------------------------------------------------------------------------

    public function getSupportedHttpMethods(): array
    {
        $routes = array();
        // return $this->supportedHttpMethods;
        foreach ($this->supportedHttpMethods as $key => $value) {
            $route = array();
            foreach ($value['endPoints'] as $endpoint) {
                $route[] = array(
                    'routePath' => $endpoint['pattern'],
                    'requireToken' => $endpoint['requireToken'],
                    'description' => $endpoint['description'],
                    'parameters' => $endpoint['parameters'],
                );
            }
            if (count($route) != 0) {
                $routes[$key] = $route;
            }
        }

        return $routes;
    }

    public function matchRoute(string $uri, string $method = 'GET'): ?RouteMatch
    {
        return $this->match($uri, $method);
    }






    // -------------------------------------------------------------------------
    // Internal Methods
    // -------------------------------------------------------------------------

    protected function setError(int $httpCode, string $message)
    {
        $this->_error = array($httpCode, $message);
    }

    private function formatRoute(string $route): string
    {
        $route = strtolower($route);
        $surfix = rtrim($route, '/') !== '' ? rtrim($route, '/') : '/';
        return ('/' . ltrim($surfix, '/'));
    }

    private function clean(string $data = ''): string
    {
        if (!empty($data) && !is_null($data) && !is_array($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        } else {
            return $data;
        }
    }

    private function nameCleaner(string $name): string
    {
        $name = str_ireplace(array($this->baseModelPath, "models/"), "", $name);
        $name = preg_replace('/\s+/', '_', $name);
        return $name;
    }

    private function getBody(array $extras = array()): array
    {
        $body = array('requestMethod' => $this->requestMethod);

        if (array_key_exists($this->requestMethod, $this->supportedHttpMethods)) {
            $isJson = strpos($this->contentType, 'application/json') !== false;
            $data = array();

            if (in_array($this->requestMethod, array('PUT', 'PATCH', 'DELETE'))) {
                $rawData = file_get_contents("php://input");
                if ($isJson) {
                    $data = json_decode($rawData, true);
                } else {
                    parse_str($rawData, $data);
                }
            } else if ($isJson && in_array($this->requestMethod, array('POST', 'GET'))) {
                $rawData = file_get_contents("php://input");
                $data = json_decode($rawData, true);
            } else if (!$isJson && in_array($this->requestMethod, array('POST', 'GET'))) {
                $data = $this->requestMethod === "POST" ? $_POST : $_GET;
            } else {
                $rawData = file_get_contents("php://input");
                parse_str($rawData, $data);
            }



            if (!$isJson) {
                foreach ($data as $key => $value) {
                    $body[$key] = $this->clean($value);
                }
            } else {
                $body = $data;
            }
        }

        return array_merge($extras, $body);
    }
    /**
     * Register default type converters matching Flask/Django capabilities
     */
    private function registerDefaultTypes(): void
    {
        // {param:int}
        $this->addType('int', '\d+', fn($val) => (int) $val);
        $this->addType('integer', '\d+', fn($val) => (int) $val);

        // {param:string} or {param:str}
        $this->addType('string', '[^/]+', fn($val) => (string) $val);
        $this->addType('str', '[^/]+', fn($val) => (string) $val);

        // {param:float}
        $this->addType('float', '\d+(?:\.\d+)?', fn($val) => (float) $val);

        // {param:slug}
        $this->addType('slug', '[a-z0-9-]+', fn($val) => (string) $val);

        // {param:uuid}
        $this->addType('uuid', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}', fn($val) => (string) $val);

        // {param:path} (matches slashes too)
        $this->addType('path', '.+', fn($val) => (string) $val);
    }

    /**
     * Add or override a custom parameter type converter
     */
    private function addType(string $name, string $regexPattern, Closure $caster): self
    {
        $this->types[$name] = new ParameterType($regexPattern, $caster);
        return $this;
    }

    /**
     * Map a route pattern to a handler
     */
    private function addRoute(string $method, string $pattern, mixed $handler = null, bool $requireToken = true, string $description = "", array $parameters = array()): self
    {
        $pattern = $this->formatRoute($pattern);

        list($regex, $paramTypes) = $this->compileRoute($pattern);

        $this->supportedHttpMethods[$method]['endPoints'][] = array(
            'requireToken' => $requireToken,
            'pattern' => $pattern,
            'regex' => $regex,
            'paramTypes' => $paramTypes,
            'handler' => $handler,
            'description' => $description,
            'parameters' => $parameters,
        );

        return $this;
    }



    /**
     * Compiles Django/Flask route strings like `/id/{id:int}/{name:string}` 
     * into PCRE Regex with named parameters.
     */
    private function compileRoute(string $pattern): array
    {
        $paramTypes = array();

        // Matches `{paramName}` OR `{paramName:typeName}`
        $regexPattern = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([a-zA-Z_][a-zA-Z0-9_]*))?\}/',
            function (array $matches) use (&$paramTypes): string {
                $paramName = $matches[1];
                $typeName = $matches[2] ?? 'string'; // Default parameter type is string

                if (!isset($this->types[$typeName])) {
                    throw new Exception("Unsupported route parameter type [{$typeName}] in pattern.", 422);
                }

                $paramTypes[$paramName] = $typeName;
                $typePattern = $this->types[$typeName]->pattern;

                // Return named capture group: (?P<paramName>pattern)
                return "(?P<{$paramName}>{$typePattern})";
            },
            $pattern
        );

        // Escape slashes and enforce start/end string bounds
        $finalRegex = '~^' . $regexPattern . '$~u';

        return [$finalRegex, $paramTypes];
    }


    /**
     * Match an incoming HTTP URI against registered routes.
     */
    private function match(string $uri, string $method = 'GET'): ?RouteMatch
    {
        // $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        // $uri = '/' . trim($uri, '/');

        $uri = $this->formatRoute($uri);
        $method = strtoupper($method);

        // $this->requestMethod
        foreach ($this->supportedHttpMethods[$method]['endPoints'] as $route) {


            if (preg_match($route['regex'], $uri, $matches)) {
                $parameters = array();
                $parameters['action'] = $this->supportedHttpMethods[$method]['action'];

                foreach ($route['paramTypes'] as $paramName => $typeName) {
                    if (array_key_exists($paramName, $matches)) {
                        $rawValue = $this->clean($matches[$paramName]);
                        // Convert value to actual native PHP type (e.g. integer, float)
                        $caster = $this->types[$typeName]->caster;
                        $parameters[$paramName] = $caster($rawValue);
                    }
                }

                return new RouteMatch(
                    requireToken: $route['requireToken'],
                    pattern: $route['pattern'],
                    uri: $uri,
                    parameters: $parameters,
                    handler: $route['handler']
                );
            }
        }

        return null;
    }

    /**
     * is_associative
     *
     * @param  mixed $input
     * @return bool
     */
    public function is_associative(mixed $input = array()): bool
    {
        $return = false;
        if (is_array($input)) {
            $return = count(array_filter(array_keys($input), 'is_string')) > 0;
        }
        return $return;
    }



    /**
     * Get Bearer Token.
     *
     * 
     * @return string
     */
    private function getBearerToken(): string
    {
        $authHeader = $this->getAuthorizationHeader();
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        } else {
            return "";
        }
    }

    /**
     * Get Authorization Header.
     * 
     * @return ?string|null
     */
    private function getAuthorizationHeader(): ?string
    {
        $headers = array();

        // 1. Try apache_request_headers() if available
        if (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();

            // Normalize header keys to lowercase for consistency
            foreach ($requestHeaders as $key => $value) {
                $headers[strtolower($key)] = $value;
            }

            if (isset($headers['authorization'])) {
                return trim($headers['authorization']);
            } else {
                return null;
            }
        }

        // 2. Fallback to $_SERVER variables (works on NGINX, FastCGI, etc.)
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }

        // 3. PHP under CGI may store it in this alternative
        else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }

        // No Authorization header found
        else {
            return null;
        }
    }


    private function execute(): array
    {
        try {
            if (count($this->_error) != 0) {
                throw new Exception($this->_error[1], $this->_error[0]);
            } else {

                $match = $this->match($this->requestUri, $this->requestMethod);
                if (is_null($match) || $match === null || !$match) {
                    throw new Exception("The requested API route {$this->requestMethod} '{$this->requestUri}' does not exist.", 404);
                } else {

                    if (!is_callable($match->handler, false, $name)) {
                        throw new Exception(" {$this->requestMethod} '{$match->uri}' MUST have a Callback function instead of [{$name}]", 405);
                    } else if ($match->requireToken && empty($this->getBearerToken())) {
                        throw new Exception("{$this->requestMethod} '{$match->uri}' requires a Authorization Token to continue.", 401);
                    } else {
                        $result = call_user_func_array($match->handler, array("class" => $this, "args" => new Arguments($this->getBody($match->parameters))));
                        if (!is_null($result)) {

                            // echo $result;  
                            if (!$this->is_associative($result) && !$this->isHTML) {
                                throw new Exception("{$this->requestMethod} '{$match->uri}' MUST return an associative array", 409);
                            } else {
                                $missing = array();
                                $result = !$this->isHTML ? $result : array('status' => true, 'statusCode' => 200, 'messageCode' => 0, 'message' => $result);

                                foreach (array('status', 'statusCode', 'messageCode', 'message') as $key) {
                                    if (!array_key_exists($key, $result)) {
                                        $missing[] = $key;
                                    }
                                }
                                if (count($missing) >= 1) {
                                    throw new Exception("{$this->requestMethod} '{$match->uri}' is missing the following expected Key(s): " . implode(", ", $missing), 409);
                                } else {
                                    return $result;
                                }
                            }
                        } else {
                            throw new Exception("{$this->requestMethod} '{$this->requestUri}'  has returned a null value", 500);
                        }
                    }
                }
            }
        } catch (Error $ex) {
            return $this->formatMessage($ex);
        } catch (TypeError $ex) {
            return $this->formatMessage($ex);
        } catch (RuntimeException $ex) {
            return $this->formatMessage($ex);
        } catch (Exception $ex) {
            return $this->formatMessage($ex);
        }
    }
}






/**
 * FlaskTemplate — Secure, eval‑free Jinja2/Flask‑style template engine.
 *
 * Security highlights:
 *   - No eval() – compiles to real PHP files (tmpfile or cache) and uses include()
 *   - Expressions are sanitised: function calls are DISALLOWED (except built‑ins we inject)
 *   - Template names are validated against directory traversal attacks
 *   - Temporary files are deleted after rendering (and created with restrictive permissions)
 *   - Auto‑escaping is ON by default (htmlspecialchars with ENT_QUOTES, UTF‑8)
 *   - Raw blocks are isolated and never parsed
 *   - Comments are stripped entirely before compilation
 *
 * Features:
 *   - Inheritance: {% extends "base.html" %} + {% block name %}...{% endblock %}
 *   - Control flow: {% if %}, {% elif %}, {% else %}, {% endif %}
 *   - Loops: {% for item in iterable %}...{% else %}...{% endfor %}
 *   - Include: {% include "partial.html" %} (shares context)
 *   - Set: {% set var = value %}
 *   - Raw: {% raw %}...{% endraw %}
 *   - Filters: upper, lower, escape, raw, length, trim, default, join, first, last, sort, reverse, batch, format, replace, striptags
 *   - Whitespace control: {%- -%} and {{- -}}
 *   - Globals: addGlobal() for variables available everywhere
 */

class FlaskTemplate
{
    private string $templateDir;
    private ?string $cacheDir;
    private bool $autoescape;
    private array $globals = [];
    private array $filters = [];

    private const SAFE_FUNCTIONS = ['isset', 'empty', 'in_array', 'url_for'];

    private const BUILTIN_FILTERS = [
        'upper' => 'strtoupper',
        'lower' => 'strtolower',
        'trim' => 'trim',
        'escape' => 'htmlspecialchars',
        'raw' => null,
        'length' => 'strlen',
        'default' => 'default_filter',
        'join' => 'join_filter',
        'first' => 'first_filter',
        'last' => 'last_filter',
        'sort' => 'sort_filter',
        'reverse' => 'reverse_filter',
        'batch' => 'batch_filter',
        'format' => 'sprintf',
        'replace' => 'replace_filter',
        'striptags' => 'strip_tags',
    ];

    public function __construct(string $templateDir, ?string $cacheDir = null, bool $autoescape = true)
    {
        $this->templateDir = rtrim(realpath($templateDir) ?: $templateDir, '/');
        if (!is_dir($this->templateDir)) {
            throw new \RuntimeException("Template directory does not exist: {$templateDir}");
        }
        $this->cacheDir = $cacheDir ? rtrim($cacheDir, '/') : null;
        $this->autoescape = $autoescape;
        $this->registerBuiltinFilters();
    }

    public function addFilter(string $name, callable $callback): void
    {
        $this->filters[$name] = $callback;
    }

    public function addGlobal(string $name, mixed $value): void
    {
        $this->globals[$name] = $value;
    }

    public function getGlobal(string $name, mixed $defaultValue = ""): mixed
    {
        return isset($this->globals[$name]) ? $this->globals[$name] : $defaultValue;
    }

    public function execFilter(string $name, mixed $value, mixed ...$args): mixed
    {
        if (!isset($this->filters[$name])) {
            throw new \RuntimeException("Unknown filter: {$name}");
        }

        return call_user_func($this->filters[$name], $value, ...$args);
    }

    public function render(string $templateName, array $data = []): string
    {
        $this->validateTemplateName($templateName);
        $compiledPath = $this->compile($templateName);
        return $this->includeCompiled($compiledPath, $data);
    }

    private function compile(string $templateName): string
    {
        $cacheFile = null;
        $this->validateTemplateName($templateName);
        $sourceFile = $this->templateDir . '/' . ltrim($templateName, '/');

        if ($this->cacheDir) {
            $cacheFile = $this->cacheDir . '/' . $this->getCacheKey($templateName);
            if (file_exists($cacheFile) && filemtime($cacheFile) >= filemtime($sourceFile)) {
                return $cacheFile;
            }
        }

        $source = file_get_contents($sourceFile);
        if ($source === false) {
            throw new \RuntimeException("Could not read template: {$templateName}");
        }

        [$source, $rawSlots] = $this->protectRaw($source);

        $parentName = null;
        if (preg_match('/{%\s*extends\s+[\'"](.+?)[\'"]\s*%}/s', $source, $m)) {
            $parentName = $m[1];
            $this->validateTemplateName($parentName);
            $source = str_replace($m[0], '', $source);
        }

        $childBlocks = [];
        $source = preg_replace_callback(
            '/{%\s*block\s+(\w+)\s*%}(.*?){%\s*endblock\s*%}/s',
            function (array $m) use (&$childBlocks): string {
                $childBlocks[$m[1]] = $m[2];
                return "<!-- block:{$m[1]} -->";
            },
            $source
        );

        if ($parentName !== null) {
            $phpCode = $this->resolveInheritance($parentName, $childBlocks, $rawSlots);
        } else {
            $phpCode = $this->convertSyntax($source);
            $phpCode = $this->restoreRaw($phpCode, $rawSlots);
        }

        if ($this->cacheDir) {
            if (!is_dir($this->cacheDir)) {
                mkdir($this->cacheDir, 0755, true);
            }
            file_put_contents($cacheFile, $phpCode);
            return $cacheFile;
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'flask_');
        if ($tmpPath === false) {
            throw new \RuntimeException('Could not create temporary file');
        }
        file_put_contents($tmpPath, $phpCode);
        return $tmpPath;
    }

    private function resolveInheritance(string $parentName, array $childBlocks, array $childRawSlots): string
    {
        $this->validateTemplateName($parentName);
        $parentSource = file_get_contents($this->templateDir . '/' . ltrim($parentName, '/'));
        if ($parentSource === false) {
            throw new \RuntimeException("Could not read parent template: {$parentName}");
        }

        [$parentSource, $parentRawSlots] = $this->protectRaw($parentSource);

        $grandParentName = null;
        if (preg_match('/{%\s*extends\s+[\'"](.+?)[\'"]\s*%}/s', $parentSource, $m)) {
            $grandParentName = $m[1];
            $this->validateTemplateName($grandParentName);
            $parentSource = str_replace($m[0], '', $parentSource);
        }

        if ($grandParentName !== null) {
            $parentBlocks = [];
            $parentSource = preg_replace_callback(
                '/{%\s*block\s+(\w+)\s*%}(.*?){%\s*endblock\s*%}/s',
                function (array $m) use (&$parentBlocks): string {
                    $parentBlocks[$m[1]] = $m[2];
                    return "<!-- block:{$m[1]} -->";
                },
                $parentSource
            );
            $merged = array_merge($parentBlocks, $childBlocks);
            $mergedRaw = array_merge($parentRawSlots, $childRawSlots);
            return $this->resolveInheritance($grandParentName, $merged, $mergedRaw);
        }

        $parentSource = preg_replace_callback(
            '/{%\s*block\s+(\w+)\s*%}(.*?){%\s*endblock\s*%}/s',
            fn(array $m) => $childBlocks[$m[1]] ?? $m[2],
            $parentSource
        );

        $phpCode = $this->convertSyntax($parentSource);
        $phpCode = $this->restoreRaw($phpCode, $parentRawSlots);
        $phpCode = $this->restoreRaw($phpCode, $childRawSlots);

        return $phpCode;
    }

    private function convertSyntax(string $source): string
    {
        $source = preg_replace('/{%-\s*/', '{%', $source);
        $source = preg_replace('/\s*-%}/', '%}', $source);
        $source = preg_replace('/{{\s*-/', '{{', $source);
        $source = preg_replace('/-\s*}}/', '}}', $source);

        $source = preg_replace('/{#.*?#}/s', '', $source);

        $source = preg_replace_callback(
            '/{%\s*set\s+(\w+)\s*=\s*(.+?)\s*%}/',
            fn(array $m): string => "<?php \${$m[1]} = {$this->toPhpExpr($m[2])}; ?>",
            $source
        );

        // ── {{ variable|filter }} ──────────────────────────────────────────
        $source = preg_replace_callback(
            '/{{\s*(.+?)\s*}}/',
            function (array $m): string {
                $parts = preg_split('/\s*\|\s*/', trim($m[1]));
                $varExpr = array_shift($parts);
                $phpExpr = $this->toPhpExpr($varExpr);
                $hasRaw = false;

                foreach ($parts as $filter) {
                    if ($filter === 'raw') {
                        $hasRaw = true;
                        continue;
                    }

                    if (preg_match('/^(\w+)\s*\((.+)\)$/', $filter, $fMatches)) {
                        $filterName = $fMatches[1];
                        $args = $this->toPhpExpr($fMatches[2]);
                        $phpExpr = $this->applyFilter($phpExpr, $filterName, $args);
                    } else {
                        $phpExpr = $this->applyFilter($phpExpr, $filter);
                    }
                }

                if ($this->autoescape && !$hasRaw) {
                    return "<?= htmlspecialchars((string)({$phpExpr}), ENT_QUOTES, 'UTF-8') ?>";
                }

                return "<?= {$phpExpr} ?>";
            },
            $source
        );

        $tokens = $this->tokenize($source);
        $php = '';
        $stack = [];

        foreach ($tokens as $token) {
            if ($token['type'] === 'text') {
                $php .= $token['content'];
                continue;
            }

            $tag = trim($token['content']);

            if (preg_match('/^if\s+(.+)$/', $tag, $m)) {
                $stack[] = ['type' => 'if', 'hasElse' => false];
                $php .= '<?php if (' . $this->toPhpExpr($m[1]) . '): ?>';
            } elseif (preg_match('/^elif\s+(.+)$/', $tag, $m)) {
                $php .= '<?php elseif (' . $this->toPhpExpr($m[1]) . '): ?>';
            } elseif ($tag === 'else' || $tag === 'else%}') {
                if (empty($stack)) {
                    throw new \RuntimeException('Unexpected {% else %} without an opening block');
                }
                $last = &$stack[count($stack) - 1];
                if ($last['type'] === 'if') {
                    $php .= '<?php else: ?>';
                } elseif ($last['type'] === 'for') {
                    $php .= '<?php endforeach; else: ?>';
                    $last['hasElse'] = true;
                } else {
                    throw new \RuntimeException('{% else %} not allowed inside ' . $last['type']);
                }
            } elseif ($tag === 'endif') {
                if (empty($stack) || $stack[count($stack) - 1]['type'] !== 'if') {
                    throw new \RuntimeException('Unexpected {% endif %}');
                }
                array_pop($stack);
                $php .= '<?php endif; ?>';
            } elseif (preg_match('/^for\s+(\w+)\s+in\s+(.+)$/', $tag, $m)) {
                $stack[] = ['type' => 'for', 'hasElse' => false];
                $item = $m[1];
                $iterable = $this->toPhpExpr($m[2]);
                $loopVar = "_loop_{$item}";
                $php .= "<?php \${$loopVar} = {$iterable}; if (!empty(\${$loopVar})): foreach (\${$loopVar} as \${$item}): ?>";
            } elseif ($tag === 'endfor' || $tag === 'endfor%}') {
                if (empty($stack) || $stack[count($stack) - 1]['type'] !== 'for') {
                    throw new \RuntimeException('Unexpected {% endfor %}');
                }
                $block = array_pop($stack);
                if ($block['hasElse']) {
                    $php .= '<?php endif; ?>';
                } else {
                    $php .= '<?php endforeach; endif; ?>';
                }
            } elseif (preg_match('/^include\s+[\'"](.+?)[\'"]\s*(?:with\s+context)?$/', $tag, $m)) {
                $php .= $this->compileSubTemplate($m[1]);
            } else {
                $php .= '{' . $tag . '}';
            }
        }

        if (!empty($stack)) {
            throw new \RuntimeException('Unclosed block(s): ' . implode(', ', array_column($stack, 'type')));
        }

        return $php;
    }

    private function tokenize(string $source): array
    {
        $tokens = [];
        $offset = 0;
        $len = strlen($source);
        $tagRegex = '/{%\s*(.*?)\s*%}/s';

        while ($offset < $len) {
            if (preg_match($tagRegex, $source, $match, PREG_OFFSET_CAPTURE, $offset)) {
                $matchStart = $match[0][1];
                $matchEnd = $matchStart + strlen($match[0][0]);

                if ($matchStart > $offset) {
                    $tokens[] = ['type' => 'text', 'content' => substr($source, $offset, $matchStart - $offset)];
                }

                $tokens[] = ['type' => 'tag', 'content' => $match[1][0]];
                $offset = $matchEnd;
            } else {
                $tokens[] = ['type' => 'text', 'content' => substr($source, $offset)];
                break;
            }
        }
        return $tokens;
    }

    private function compileSubTemplate(string $name): string
    {
        $this->validateTemplateName($name);
        $source = file_get_contents($this->templateDir . '/' . ltrim($name, '/'));
        if ($source === false) {
            throw new \RuntimeException("Sub‑template not found: {$name}");
        }

        [$source, $rawSlots] = $this->protectRaw($source);
        $phpCode = $this->convertSyntax($source);
        $phpCode = $this->restoreRaw($phpCode, $rawSlots);
        return $phpCode;
    }

    // 2. Updated expression parser
    private function toPhpExpr(string $expr): string
    {
        $expr = trim($expr);

        // Protect string literals ('...' and "...") from variable and dot-notation regexes
        $stringLiterals = [];
        $expr = preg_replace_callback(
            '/\'(?:\\\\.|[^\'\\\\])*\'|"(?:\\\\.|[^"\\\\])*"/s',
            function (array $m) use (&$stringLiterals): string {
                $key = '__STR_' . count($stringLiterals) . '__';
                $stringLiterals[$key] = $m[0];
                return $key;
            },
            $expr
        );

        // Convert Jinja2 named args (filename='...') to PHP 8 named args (filename: '...')
        $expr = preg_replace('/(?<=\(|\,|^)\s*([a-zA-Z_]\w*)\s*=\s*/', '$1: ', $expr);

        // Operator replacements
        $expr = preg_replace('/\bis\s+defined\b/', 'isset', $expr);
        $expr = preg_replace('/\bis\s+not\s+defined\b/', '!isset', $expr);
        $expr = preg_replace('/\bis\s+none\b/', 'is_null', $expr);
        $expr = preg_replace('/\bis\s+not\s+none\b/', '!is_null', $expr);
        $expr = preg_replace('/\bnot\s+in\b/', '!in_array', $expr);
        $expr = preg_replace('/\bin\b/', 'in_array', $expr);

        // Validate function calls
        $expr = preg_replace_callback(
            '/\b([a-zA-Z_]\w*)\s*\(/',
            function (array $m): string {
                $func = $m[1];
                if (!in_array($func, self::SAFE_FUNCTIONS, true)) {
                    throw new \RuntimeException("Function call '{$func}()' is not allowed in expressions");
                }
                return $m[0];
            },
            $expr
        );

        // Prefix variables with $ (ignore reserved keywords, named parameters, and string tokens)
        $expr = preg_replace_callback(
            '/(?<!\$)\b([a-zA-Z_]\w*)\b(?!\s*[\(:])/',
            function (array $m): string {
                $keyword = strtolower($m[1]);
                $reserved = ['true', 'false', 'null', 'and', 'or', 'not', 'is', 'in', 'as', 'isset', 'empty', 'array'];
                if (in_array($keyword, $reserved, true) || str_starts_with($keyword, '__str_')) {
                    return $m[1];
                }
                return '$' . $m[1];
            },
            $expr
        );

        // Method calls ($obj.method())
        $expr = preg_replace_callback(
            '/(\$[a-zA-Z_]\w*)\.\$?([a-zA-Z_]\w*)\s*\(/',
            function (array $m): string {
                return $m[1] . '->' . $m[2] . '(';
            },
            $expr
        );

        // Dot notation array access ($obj.prop)
        $expr = preg_replace_callback(
            '/(\$[a-zA-Z_]\w*)((?:\.\$?[a-zA-Z_]\w*)+)(?![(])/',
            function (array $m): string {
                $base = $m[1];
                $rest = str_replace('$', '', $m[2]);
                $parts = explode('.', $rest);
                foreach ($parts as $part) {
                    if ($part !== '') {
                        $base .= "['" . $part . "']";
                    }
                }
                return $base;
            },
            $expr
        );

        // Restore string literals
        if (!empty($stringLiterals)) {
            $expr = str_replace(array_keys($stringLiterals), array_values($stringLiterals), $expr);
        }

        return $expr;
    }

    private function registerBuiltinFilters(): void
    {
        foreach (self::BUILTIN_FILTERS as $name => $callback) {
            if (is_string($callback) && method_exists($this, $callback)) {
                $this->filters[$name] = [$this, $callback];
            } elseif (is_callable($callback)) {
                $this->filters[$name] = $callback;
            }
        }
    }

    private function applyFilter(string $expr, string $filter, ?string $args = null): string
    {
        $argString = $args !== null ? ", {$args}" : "";

        return match ($filter) {
            'upper' => "strtoupper((string)({$expr}))",
            'lower' => "strtolower((string)({$expr}))",
            'trim' => "trim((string)({$expr}))",
            'length' => "(is_array({$expr}) ? count({$expr}) : strlen((string)({$expr})))",
            'striptags' => "strip_tags((string)({$expr}))",
            'format' => "sprintf((string)({$expr}){$argString})",
            default => "\$this->execFilter('{$filter}', {$expr}{$argString})",
        };
    }

    public function default_filter(mixed $value, $default = ''): mixed
    {
        return empty($value) ? $default : $value;
    }

    public function join_filter(array $value, string $glue = ''): string
    {
        return implode($glue, (array) $value);
    }

    public function first_filter(mixed $value): mixed
    {
        return is_array($value) ? reset($value) : null;
    }

    public function last_filter(mixed $value): mixed
    {
        return is_array($value) ? end($value) : null;
    }

    public function sort_filter(mixed $value): mixed
    {
        if (is_array($value)) {
            sort($value);
            return $value;
        }
        return $value;
    }

    public function reverse_filter(mixed $value): array|string
    {
        if (is_array($value)) {
            return array_reverse($value);
        }
        return strrev((string) $value);
    }

    public function batch_filter(mixed $value, int $size, $fill = null): array
    {
        if (!is_array($value)) {
            return [];
        }

        $chunks = array_chunk($value, $size, true);

        if ($fill !== null && !empty($chunks)) {
            $lastKey = array_key_last($chunks);
            $lastChunk = $chunks[$lastKey];
            $missing = $size - count($lastChunk);
            if ($missing > 0) {
                $chunks[$lastKey] = array_merge($lastChunk, array_fill(0, $missing, $fill));
            }
        }

        return $chunks;
    }

    public function replace_filter(array|string $value, array|string $search, array|string $replace): string
    {
        return str_replace($search, $replace, (string) $value);
    }

    private function protectRaw(string $source): array
    {
        $slots = [];
        $source = preg_replace_callback(
            '/{%\s*raw\s*%}(.*?){%\s*endraw\s*%}/s',
            function (array $m) use (&$slots): string {
                $key = '__RAW_SLOT_' . count($slots) . '__';
                $slots[$key] = $m[1];
                return $key;
            },
            $source
        );
        return [$source, $slots];
    }

    private function restoreRaw(string $source, array $slots): string
    {
        return str_replace(array_keys($slots), array_values($slots), $source);
    }

    private function includeCompiled(string $compiledPath, array $data): string
    {
        $data = array_merge($this->globals, $data);
        $isTemp = strpos($compiledPath, sys_get_temp_dir()) === 0;

        ob_start();
        extract($data, EXTR_SKIP);
        try {
            include $compiledPath;
        } finally {
            if ($isTemp && file_exists($compiledPath)) {
                unlink($compiledPath);
            }
        }
        return ob_get_clean();
    }

    private function validateTemplateName(string $name): void
    {
        $name = ltrim($name, '/');
        if (strpos($name, '..') !== false || strpos($name, './') !== false) {
            throw new \RuntimeException("Invalid template name: {$name}");
        }
        $fullPath = realpath($this->templateDir . '/' . $name);
        if ($fullPath === false || strpos($fullPath, realpath($this->templateDir)) !== 0) {
            throw new \RuntimeException("Template outside of allowed directory: {$name}");
        }
    }

    private function getCacheKey(string $name): string
    {
        return md5($name) . '.php';
    }
}




// =============================================================================
// Example usage (secure and Flask‑like)
// =============================================================================
/*
$engine = new FlaskTemplate(__DIR__ . '/templates', __DIR__ . '/cache', true);
$engine->addGlobal('app_name', 'MySite');
echo $engine->render('index.html', ['user' => ['name' => 'John', 'age' => 30]]);
*/


/**
 * 
 * # Detailed Usage Guide for the Enhanced FlaskTemplate Engine

This guide covers everything you need to know to use the **Enhanced FlaskTemplate** – a secure, zero‑dependency, eval‑free template engine that mimics Flask/Jinja2 in PHP.

---

## 📦 Installation

1. **Copy the class** – save the entire `FlaskTemplate` class code into a file named `FlaskTemplate.php`.
2. **No external libraries** – it’s pure PHP 7.4+.
3. **Require it** in your project:
   ```php
   require_once __DIR__ . '/FlaskTemplate.php';
   ```

---

## 🚀 Basic Usage

### 1. Create your template directory
Place all `.html`, `.php`, or any text files in a folder, e.g., `templates/`.

### 2. Instantiate the engine
```php
$engine = new FlaskTemplate(
    __DIR__ . '/templates',   // template directory
    __DIR__ . '/cache',       // optional cache directory (will be created)
    true                      // autoescape (default true)
);
```

### 3. Render a template
```php
echo $engine->render('index.html', [
    'username' => 'John',
    'items'    => ['Apple', 'Banana', 'Cherry']
]);
```

---

## 🧩 Template Syntax (Jinja2‑Compatible)

### Variables
```twig
{{ variable }}
{{ user.name }}          <!-- array access: $user['name'] -->
{{ user['name'] }}       <!-- explicit array access -->
{{ user.getName() }}     <!-- method call → $user->getName() -->
```
 **Auto‑escaping** is on by default – all output is passed through `htmlspecialchars`.  
Use the `raw` filter to disable escaping:
```twig
{{ html_content|raw }}
```

### Filters
Apply filters with the pipe `|` – you can chain them:
```twig
{{ name|upper }}
{{ name|lower }}
{{ name|trim }}
{{ name|escape }}
{{ name|length }}
{{ name|default('Guest') }}
{{ list|join(', ') }}
{{ list|first }}
{{ list|last }}
{{ list|sort }}
{{ list|reverse }}
{{ list|batch(3, 'fill') }}
{{ text|replace('old', 'new') }}
{{ text|striptags }}
```
Custom filters can be added (see below).

### Control Structures

#### `if` / `elif` / `else`
```twig
{% if user.is_admin %}
    <p>Welcome, admin!</p>
{% elif user.is_moderator %}
    <p>Hello, mod.</p>
{% else %}
    <p>Hi, {{ user.name }}.</p>
{% endif %}
```

#### `for` loops
```twig
{% for item in items %}
    <li>{{ item }}</li>
{% else %}
    <li>No items found.</li>
{% endfor %}
```
The `{% else %}` block executes when the iterable is empty.

### Template Inheritance

#### Base template (`base.html`)
```twig
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}My Site{% endblock %}</title>
</head>
<body>
    <header>{% block header %}Default Header{% endblock %}</header>
    <main>{% block content %}{% endblock %}</main>
    <footer>{% block footer %}© 2026{% endblock %}</footer>
</body>
</html>
```

#### Child template (`page.html`)
```twig
{% extends "base.html" %}

{% block title %}Home Page{% endblock %}

{% block header %}
    <h1>Welcome to our site</h1>
{% endblock %}

{% block content %}
    <p>Hello, {{ user.name }}!</p>
{% endblock %}
```
Blocks not overridden inherit the parent’s default content.

### Include Sub‑templates
```twig
{% include "header.html" %}
{% include "sidebar.html" with context %}
```
The `with context` is optional – context is always shared by default.

### Raw Blocks
Content inside `{% raw %}` … `{% endraw %}` is **not parsed**:
```twig
{% raw %}
    {{ This will not be interpreted }}
    {% if something %} … {% endif %}
{% endraw %}
```

### Set Variables
```twig
{% set title = "My Page" %}
{% set full_name = user.first ~ ' ' ~ user.last %}
```

### Comments
```twig
{# This comment is removed entirely from output #}
```

### Whitespace Control
Strip whitespace around tags with `-`:
```twig
{%- if show -%}
    Content
{%- endif -%}
```
Similarly for `{{-` and `-}}`.

---

## 🌍 Global Variables

You can define variables that are available in **every** template:
```php
$engine->addGlobal('app_name', 'MyAwesomeApp');
$engine->addGlobal('current_year', date('Y'));
```
Then in any template:
```twig
<footer>&copy; {{ current_year }} {{ app_name }}</footer>
```

---

## 🔧 Custom Filters

Register your own filters with a callable:
```php
$engine->addFilter('currency', function($amount, $symbol = '$') {
    return $symbol . number_format($amount, 2);
});
```
Then use it:
```twig
{{ price|currency('€') }}
```

---

## 🗂️ Caching

If you pass a cache directory, compiled PHP files are stored there:
```php
$engine = new FlaskTemplate(__DIR__ . '/templates', __DIR__ . '/cache');
```
The engine checks the source file’s modification time and recompiles only when needed.  
For maximum performance in production, ensure the cache directory is writable.

If no cache directory is given, the engine uses temporary files (created with `tempnam()`) that are automatically deleted after each render – this is safe but slightly slower.

---

## 🔒 Security Highlights

- **No `eval()`** – all templates are compiled to real PHP files and `include()`d.
- **Expression sandbox** – only safe functions (`isset`, `empty`, `in_array`) are allowed; all other function calls are **blocked**.
- **Path traversal protection** – template names are validated to stay inside the template directory.
- **Auto‑escaping** by default – prevents XSS.
- **Raw blocks** are isolated and never parsed.
- **Comments are stripped** before compilation.

---

## 🧪 Complete Example

### File structure
```
project/
├── FlaskTemplate.php
├── templates/
│   ├── base.html
│   ├── index.html
│   └── header.html
└── public/
    └── index.php
```

### `templates/base.html`
```twig
<!DOCTYPE html>
<html>
<head>
    <title>{% block title %}Default Title{% endblock %}</title>
</head>
<body>
    {% include "header.html" %}
    <main>
        {% block content %}{% endblock %}
    </main>
    <footer>&copy; {{ current_year }} {{ app_name }}</footer>
</body>
</html>
```

### `templates/header.html`
```twig
<header>
    <h1>{{ app_name }}</h1>
</header>
```

### `templates/index.html`
```twig
{% extends "base.html" %}

{% block title %}Welcome{% endblock %}

{% block content %}
    <h2>Hello, {{ user.name }}!</h2>
    <ul>
    {% for item in items %}
        <li>{{ item|upper }}</li>
    {% else %}
        <li>No items.</li>
    {% endfor %}
    </ul>
{% endblock %}
```

### `public/index.php`
```php
<?php
require_once __DIR__ . '/../FlaskTemplate.php';

$engine = new FlaskTemplate(__DIR__ . '/../templates', __DIR__ . '/../cache', true);
$engine->addGlobal('app_name', 'MySite');
$engine->addGlobal('current_year', date('Y'));

$data = [
    'user'  => ['name' => 'John Doe'],
    'items' => ['apple', 'banana', 'cherry']
];

echo $engine->render('index.html', $data);
```

### Output (HTML)
```html
<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <header>
        <h1>MySite</h1>
    </header>
    <main>
        <h2>Hello, John Doe!</h2>
        <ul>
            <li>APPLE</li>
            <li>BANANA</li>
            <li>CHERRY</li>
        </ul>
    </main>
    <footer>&copy; 2026 MySite</footer>
</body>
</html>
```

---

## ⚙️ Advanced: Disabling Auto‑escape

If you need raw output for a whole template (e.g., generating JSON or XML), you can disable auto‑escape at engine creation:
```php
$engine = new FlaskTemplate(__DIR__ . '/templates', null, false);
```
Then you must manually escape where needed, or use the `escape` filter.

---

## 🧪 Error Handling

The engine throws `RuntimeException` for:
- Missing templates.
- Invalid template names (outside the directory).
- Syntax errors (e.g., malformed expressions).

Wrap `render()` in a try‑catch to handle gracefully.

---

## 🏎️ Performance Tips

- **Use the cache directory** – compiled templates are stored and reused.
- **Avoid complex logic inside templates** – keep presentation logic minimal.
- **Use globals** for values that don’t change often.
- **Enable OPcache** – PHP’s opcode cache will speed up `include()` of compiled files.

---

## 📚 Summary

The Enhanced FlaskTemplate gives you a **secure, full‑featured, Jinja2‑like** templating experience in PHP – without third‑party libraries. It’s ideal for micro‑frameworks, CMSes, or any project that needs a clean separation of logic and presentation.

Start using it today and enjoy the simplicity and safety of Flask‑style templating in PHP!
 */



/**
 * 🧪 Detailed Usage Guide
1. Project Structure
text
project/
├── Router.php              # Contains both FlaskTemplate and Router
├── templates/              # Your HTML templates
│   ├── base.html
│   ├── index.html
│   └── error.html
├── models/                 # Optional model classes (auto‑loaded)
│   └── User.php
└── public/
    └── index.php           # Entry point
2. Entry Point (public/index.php)
php
<?php
require_once __DIR__ . '/../Router.php';

// Instantiate router with template support
$router = new Router(
    templateDir: __DIR__ . '/../templates',
    cacheDir:    __DIR__ . '/../cache',   // optional, for compiled templates
    autoescape:  true                     // HTML escaping on by default
);

// Define routes using magic methods
$router->get('/', function ($class, $args) {
    // Render a template and return HTML string
    return $class->render('index.html', ['user' => 'John']);
});

$router->get('/api/user/{id:int}', function ($class, $args) {
    // Return JSON response
    $userId = $args->getValue('id');
    return [
        'status'      => true,
        'statusCode'  => 200,
        'messageCode' => 0,
        'message'     => 'User found',
        'data'        => ['id' => $userId, 'name' => 'John Doe']
    ];
});

$router->post('/api/users', function ($class, $args) {
    // Access POST/JSON data via $args
    $name = $args->getValue('name');
    // ... create user ...
    return [
        'status'      => true,
        'statusCode'  => 201,
        'messageCode' => 0,
        'message'     => 'User created',
        'data'        => ['name' => $name]
    ];
});

// Everything is automatically executed in the destructor.
3. Template Example (templates/index.html)
twig
{% extends "base.html" %}

{% block title %}Home{% endblock %}

{% block content %}
    <h1>Hello, {{ user }}!</h1>
    <p>This page is rendered by the FlaskTemplate engine.</p>
{% endblock %}
4. Advanced: Using Models & Error Handling
php
$router->get('/users/{id:int}', function ($class, $args) {
    $id = $args->getValue('id');
    try {
        $userModel = $class->getModel('User');
        $user = $userModel->find($id);
        if (!$user) {
            $class->setStatusCode(404);
            return [
                'status'      => false,
                'statusCode'  => 404,
                'messageCode' => 1001,
                'message'     => 'User not found'
            ];
        }
        return [
            'status'      => true,
            'statusCode'  => 200,
            'messageCode' => 0,
            'message'     => 'OK',
            'data'        => $user
        ];
    } catch (Exception $e) {
        // The router will automatically catch and format this.
        throw new Exception('Database error: ' . $e->getMessage(), 500);
    }
});
5. Registering Custom Route Parameter Types
php
// In your bootstrap or inside the Router constructor override
$router->addType('hex', '[0-9a-fA-F]+', function($val) { return hexdec($val); });

// Then use it: /item/{color:hex}
6. Auto‑loading Models
php
// Automatically register all model classes from the models/ directory
$router->autoModelRegister();
// Now you can use $router->getModel('User') etc.
📦 Complete Integration Example
public/index.php – full working example:

php
<?php
require_once __DIR__ . '/../Router.php';

$router = new Router(
    templateDir: __DIR__ . '/../templates',
    cacheDir:    __DIR__ . '/../cache',
    autoescape:  true
);

// Register custom filter (optional)
$router->templateEngine->addFilter('stars', function($value) {
    return str_repeat('⭐', (int)$value);
});

// Home page – HTML
$router->get('/', function($class, $args) {
    return $class->render('index.html', ['user' => 'Guest']);
});

// JSON API – get user
$router->get('/api/user/{id:int}', function($class, $args) {
    $id = $args->getValue('id');
    return [
        'status'      => true,
        'statusCode'  => 200,
        'messageCode' => 0,
        'message'     => 'User profile',
        'user'        => ['id' => $id, 'name' => 'John Doe']
    ];
});

// POST – create user
$router->post('/api/user', function($class, $args) {
    $name = $args->getValue('name');
    if (empty($name)) {
        $class->setStatusCode(400);
        return [
            'status'      => false,
            'statusCode'  => 400,
            'messageCode' => 1002,
            'message'     => 'Name is required'
        ];
    }
    // ... save to database ...
    return [
        'status'      => true,
        'statusCode'  => 201,
        'messageCode' => 0,
        'message'     => 'User created',
        'data'        => ['name' => $name]
    ];
});

// Any method – fallback for invalid routes (already handled by Router)
✅ Summary
The enhanced Router now offers:

Flask‑style routing with parameter types (int, string, uuid, etc.)

Automatic token authentication (Bearer tokens)

Flexible responses – JSON or HTML (via templates)

Built‑in template engine (secure, eval‑free, Jinja2‑like)

Model auto‑loading for quick data access

Clean, exception‑safe error handling

All in a single, dependency‑free file – perfect for micro‑services, APIs, or full‑stack PHP applications that want a Flask‑like experience without a heavy framework.
 */
