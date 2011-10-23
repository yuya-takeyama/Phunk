<?php
use Phunk\Builder;

class ContentLength
{
    protected $_app;

    public function __construct($app)
    {
        $this->_app = $app;
    }

    public function __invoke($env)
    {
        list($status, $headers, $body) = $this->_app->__invoke($env);
        $headers[] = 'Content-Length: ' . strlen($body);
        return [$status, $headers, $body];
    }
}

$app = new Builder(function ($b) {
    $b->enable('ContentLength');

    $b->run(function ($env) {
        return [200, ['Content-type: text/plain'], 'Hello, Phunk Builder!'];
    });
});
