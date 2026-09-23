<?php
class ApiModel
{

    // declare(strict_types=1);


    private function getHttpAction(string $httpMethod): string
    {
        $httpMethod = strtoupper($httpMethod);
        $allow = array("GET" => "read", "POST" => "create", "DELETE" => "delete", "PUT" => "replacement", "PATCH" => "update");
        return array_key_exists($httpMethod, $allow) ? $allow[$httpMethod] : "undefined";
    }

    /**
     * Extracts the static "base" of a route path by stripping any dynamic
     * segments (curly-brace placeholders like {id} or {id:string}).
     *
     * Examples:
     *   '/doc'               -> '/doc'
     *   '/doc/'              -> '/doc'
     *   '/doc/{id:string}'   -> '/doc'
     *   '/doc/{id}/edit'     -> '/doc'          (first dynamic segment truncates)
     *   '/users/{id}/posts'  -> '/users'
     *   '/'                  -> '/'
     *
     * @param string $path
     * @return string
     */
    private function extractStaticBasePath(string $path): string
    {
        // Normalise: ensure a single leading slash, strip trailing slashes.
        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/');
        if ($path === '') {
            return '/';
        }

        // Split and keep segments only until the first dynamic one.
        $segments = explode('/', trim($path, '/'));
        $static = [];

        foreach ($segments as $segment) {
            // A dynamic segment is anything wrapped in {...} (with optional type).
            if (preg_match('/^\{.*\}$/', $segment) === 1) {
                break;
            }
            $static[] = $segment;
        }

        if ($static === []) {
            return '/';
        }

        return '/' . implode('/', $static);
    }

    /**
     * Groups API routes by their static base path, merging HTTP verbs and
     * collapsing dynamic-segment variants into a single bucket.
     *
     * Buckets are keyed by the static base (e.g. '/sections', '/doc', '/users').
     * Within each bucket, every distinct concrete routePath is preserved under
     * the 'variants' key so no information is lost.
     * @param array<string, mixed> $payload  Raw API response .
     * @param array<string>        $exclude  those you don't what to show.
     * @param bool                 $preserveOrder  Keep first-seen insertion order of paths.
     *
     * @return array<string, array<string, mixed>> Grouped routes keyed by routePath.
     *
     * @throws InvalidArgumentException When the payload is malformed.
     */
    public function groupRoutesByPath(array $payload, array $exclude = array(), bool $preserveOrder = true, bool $flatListKeyedByPath = false): array
    {

        // if (!array_key_exists('allowed', $payload)) {
        //     throw new InvalidArgumentException('Payload is missing the "allowed" key.');
        // }

        // $allowed = $payload['allowed'];
        // if (!is_array($allowed)) {
        //     throw new InvalidArgumentException('The "allowed" key must be an array.');
        // }

        $allowed = $payload;
        $methodOrder = ['GET' => 1, 'POST' => 2, 'PUT' => 3, 'PATCH' => 4, 'DELETE' => 5, 'OPTIONS' => 6, 'HEAD' => 7];
        $grouped = [];

        foreach ($allowed as $httpMethod => $routes) {
            $httpMethod = strtoupper((string) $httpMethod);

            if (!is_array($routes)) {
                continue; // Skip malformed verb buckets.
            }

            foreach ($routes as $route) {
                if (!is_array($route) || !isset($route['routePath'])) {
                    continue; // Skip malformed entries.
                }

                $rawPath = (string) $route['routePath'];
                $basePath = $this->extractStaticBasePath($rawPath);
                $cleanPath = rtrim('/' . ltrim($rawPath, '/'), '/') ?: '/';

                $checkings = $cleanPath === '/' ? $cleanPath : strtolower(ltrim($basePath, '/'));
                $disallowed = array_map(fn($v) => $v === '/' ? $v : strtolower(ltrim($v, '/')), $exclude);
                if (in_array($checkings, $disallowed)) {
                    // echo $basePath."<br/>";
                    continue; // Skip malformed entries.
                }

                // Initialise the bucket for this static base.
                if (!isset($grouped[$basePath])) {
                    $grouped[$basePath] = [
                        'basePath' => $checkings,
                        'position' => count($grouped) + 1,
                        'methods' => [],
                        'requireToken' => false,
                        'description' => '',
                        'parameters' => [],
                        'variants' => [], // routePath => [methods, route defs]
                    ];
                }

                // Merge bucket-level metadata.
                if (!empty($route['requireToken'])) {
                    $grouped[$basePath]['requireToken'] = true;
                }

                if (!empty($route['description'])) {
                    $grouped[$basePath]['description'] = $route['description'];
                }

                if (!empty($route['parameters']) && is_array($route['parameters'])) {
                    $grouped[$basePath]['parameters'] = array_values(
                        array_unique(
                            array_merge($grouped[$basePath]['parameters'], $route['parameters']),
                            SORT_REGULAR
                        )
                    );
                }

                // Record the HTTP verb on the bucket.
                if (!in_array($httpMethod, $grouped[$basePath]['methods'], true)) {
                    $grouped[$basePath]['methods'][] = $httpMethod;
                }

                // Record the concrete path variant.
                if (!isset($grouped[$basePath]['variants'][$cleanPath])) {
                    $grouped[$basePath]['variants'][$cleanPath] = [
                        'routePath' => $cleanPath,
                        'isDynamic' => $cleanPath !== $basePath,
                        'position' => count($grouped[$basePath]['variants']) + 1,
                        'methods' => [],
                        'requireToken' => false,
                        'description' => '',
                        'parameters' => [],
                        'routes' => [],
                    ];
                }

                $variant = &$grouped[$basePath]['variants'][$cleanPath];

                if (!empty($route['requireToken'])) {
                    $variant['requireToken'] = true;
                }
                if (!empty($route['description'])) {
                    $variant['description'] = $route['description'];
                }
                if (!empty($route['parameters']) && is_array($route['parameters'])) {
                    $variant['parameters'] = array_values(
                        array_unique(
                            array_merge($variant['parameters'], $route['parameters']),
                            SORT_REGULAR
                        )
                    );
                }

                if (!in_array($httpMethod, $variant['methods'], true)) {
                    $variant['methods'][] = $httpMethod;
                }


                $parentid = $grouped[$basePath]['position'] ?? null;
                $childidn = $variant['position'] ?? null;
                $activity = $this->getHttpAction($httpMethod);

                $route['urlAlias'] = implode("", array($checkings, $parentid, $childidn, $activity));

                $variant['routes'][$httpMethod] = $route;
                // $variant['routes'][$httpMethod]['urlAlias'] = (1)."_dd";
                unset($variant);
            }
        }

        // Deterministic ordering of methods.
        $sortMethods = static function (array &$methods) use ($methodOrder): void {
            usort(
                $methods,
                static fn(string $a, string $b): int => ($methodOrder[$a] ?? 99) <=> ($methodOrder[$b] ?? 99)
            );
        };

        foreach ($grouped as &$bucket) {
            $sortMethods($bucket['methods']);
            foreach ($bucket['variants'] as &$variant) {
                $sortMethods($variant['methods']);
            }
            unset($variant);

            // Sort variants: static first, then dynamic; alphabetical within each.
            uksort(
                $bucket['variants'],
                static function (string $a, string $b) use ($bucket): int {
                    $aDynamic = $a !== $bucket['basePath'];
                    $bDynamic = $b !== $bucket['basePath'];
                    if ($aDynamic !== $bDynamic) {
                        return $aDynamic <=> $bDynamic; // static (false) first
                    }
                    return strcmp($a, $b);
                }
            );
        }
        unset($bucket);

        if (!$preserveOrder) {
            ksort($grouped);
        }

        return $flatListKeyedByPath === true ? array_map(
            static fn(array $g): array => [
                'routePath' => $g['routePath'],
                'methods' => $g['methods'],
                'requireToken' => $g['requireToken'],
                'description' => $g['description'],
                'parameters' => $g['parameters'],
            ],
            $grouped
        ) : $grouped;
    }



    public function sortMethods(array $data): array
    {

        // Map HTTP methods to CSS classes, icons, and display text
        $methodMeta = [
            'GET' => ['class' => 'get', 'display' => 'get', 'icon' => 'fa-list'],
            'POST' => ['class' => 'post', 'display' => 'post', 'icon' => 'fa-plus'],
            'PUT' => ['class' => 'put', 'display' => 'put', 'icon' => 'fa-pen'],
            'PATCH' => ['class' => 'patch', 'display' => 'patch', 'icon' => 'fa-eraser'],
            'DELETE' => ['class' => 'del', 'display' => 'delete', 'icon' => 'fa-trash'],
        ];

        // 3. Normalize JSON endpoints into clean arrays for template rendering
        $formattedEndpoints = [];
        if (!empty($data['endpoints'])) {
            foreach ($data['endpoints'] as $path => $endpoint) {
                $routeList = [];

                if (!empty($endpoint['variants'])) {
                    foreach ($endpoint['variants'] as $variant) {
                        if (!empty($variant['routes'])) {
                            foreach ($variant['routes'] as $method => $routeData) {
                                $meta = $methodMeta[$method] ?? [
                                    'class' => strtolower($method),
                                    'display' => strtolower($method),
                                    'icon' => 'fa-code'
                                ];

                                $routeList[] = [
                                    'routePath' => $routeData['routePath'],
                                    'method' => $method,
                                    'method_class' => $meta['class'],
                                    'method_display' => $meta['display'],
                                    'icon' => $meta['icon'],
                                    'requireToken' => $routeData['requireToken'],
                                    'description' => $routeData['description'],
                                    'parameters' => htmlspecialchars(json_encode($routeData['parameters']), ENT_QUOTES, 'UTF-8'),
                                ];
                            }
                        }
                    }
                }

                $formattedEndpoints[] = [
                    'basePath' => $endpoint['basePath'],
                    'position' => $endpoint['position'],
                    'route_list' => $routeList
                ];
            }
        }

        // Sort endpoints by position
        usort($formattedEndpoints, fn($a, $b) => $a['position'] <=> $b['position']);

        // // 4. Instantiate FlaskTemplate engine and render
        // $engine = new FlaskTemplate(__DIR__ . '/templates', __DIR__ . '/cache', true);

        // // echo $engine->render('sidebar.html', [
        // //     'endpoints' => $formattedEndpoints
        // // ]);

        return $formattedEndpoints;
    }
}