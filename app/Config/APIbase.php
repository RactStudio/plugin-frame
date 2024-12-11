<?php

namespace PluginFrame\Config;

class APIbase
{
    // Define your API routes here
    public function __invoke(): string
    {
        // Provide API base URL here
        //return 'https://api.example.com';
        return 'http://127.0.0.1:80';
    }
}