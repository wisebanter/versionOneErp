<?php

class Documentation
{

    private $_provided = array();

    public function __construct(array $provided = array(), bool $changeCase = true)
    {
        $this->_provided = array_change_key_case($this->_get());
    }

    public function getValue(string $action, string $endpoint): array
    {
        $action = strtolower($action);
        $endpoint = strtolower($endpoint);
        $results = array();

        if (isset($this->_provided[$action][$endpoint])) {
            $results = $this->_provided[$action][$endpoint];
        }
        return $results;
    }


    private function _get(): array
    {
        return array(
            "GET" => array(),

            "PUT" => array(),

            "POST" => array(
                "signin" => array(
                    array(
                        'name' => 'username',
                        'type' => 'string',
                        'required' => true,
                    ),
                    array(
                        'name' => 'password',
                        'type' => 'string',
                        'required' => true,
                        // 'description' => 'Page number',
                    ),
                ),
            ),

            "PATCH" => array(),

            "DELETE" => array(),

            "ANY" => array(
                "manager" => array(
                    'GET' => array(
                        'requireToken' => true,
                        'description' => 'Retrieves a resource or collection without modifying data.',
                        'parameters' => array(
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                        ),
                    ),
                    'PUT' => array(
                        'requireToken' => true,
                        'description' => 'Replaces an entire existing resource with updated data.',
                        'parameters' => array(),
                    ),
                    'POST' => array(
                        'requireToken' => true,
                        'description' => 'Creates a new resource using the provided request payload.',
                        'parameters' => array(
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                        ),
                    ),
                    'PATCH' => array(
                        'requireToken' => true,
                        'description' => 'Applies partial, targeted updates to an existing resource.',
                        'parameters' => array(
                            array(
                                'name' => 'idnum',
                                'type' => 'integer',
                                'required' => true,
                            ),
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                        ),
                    ),
                    'DELETE' => array(
                        'requireToken' => true,
                        'description' => "Permanently removes a specified resource from the server. ",
                        'parameters' => array(
                            array(
                                'urlparam' => true,
                                'name' => 'idnum',
                                'type' => 'integer',
                                'required' => true,
                            ),
                        ),
                    )
                ),
                "academicyear" => array(
                    'GET' => array(
                        'requireToken' => true,
                        'description' => 'Retrieves a resource or collection without modifying data.',
                        'parameters' => array(
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                        ),
                    ),
                    'POST' => array(
                        'requireToken' => true,
                        'description' => 'Creates a new resource using the provided request payload.',
                        'parameters' => array(
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                        ),
                    ),
                    'PATCH' => array(
                        'requireToken' => true,
                        'description' => 'Applies partial, targeted updates to an existing resource.',
                        'parameters' => array(
                            array(
                                'urlparam' => true,
                                'name' => 'id',
                                'type' => 'integer',
                                'required' => true,
                            ),
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                        ),
                    ),
                    'DELETE' => array(
                        'requireToken' => true,
                        'description' => "Permanently removes a specified resource from the server. ",
                        'parameters' => array(
                            array(
                                'urlparam' => true,
                                'name' => 'id',
                                'type' => 'integer',
                                'required' => true,
                            ),
                        ),
                    )
                ),
                "sessions" => array(
                    
                    'POST' => array(
                        'requireToken' => true,
                        'description' => 'Creates a new resource using the provided request payload.',
                        'parameters' => array(
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                        ),
                    ),
                    'PATCH' => array(
                        'requireToken' => true,
                        'description' => 'Applies partial, targeted updates to an existing resource.',
                        'parameters' => array(
                            array(
                                'urlparam' => true,
                                'name' => 'idnum',
                                'type' => 'integer',
                                'required' => true,
                            ),
                            array(
                                'name' => 'name',
                                'required' => true,
                            ),
                            array(
                                'name' => 'active',
                                'required' => true,
                            ),
                        ),
                    ),
                    'DELETE' => array(
                        'requireToken' => true,
                        'description' => "Permanently removes a specified resource from the server. ",
                        'parameters' => array(
                            array(
                                'urlparam' => true,
                                'name' => 'idnum',
                                'type' => 'integer',
                                'required' => true,
                            ),
                        ),
                    )
                ),
            ),
        );
    }
}
