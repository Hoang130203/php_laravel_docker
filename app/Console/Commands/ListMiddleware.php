<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;

class ListMiddleware extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'middleware:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all registered middleware';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Router $router)
    {
        $middleware = $router->getMiddleware();
        $routeMiddleware = $router->getMiddlewareGroups();

        $this->info('Global Middleware:');
        foreach ($middleware as $name => $class) {
            $this->line("$name => $class");
        }

        $this->info("\nRoute Middleware Groups:");
        foreach ($routeMiddleware as $group => $middlewares) {
            $this->info("\n$group:");
            foreach ($middlewares as $middleware) {
                $this->line("  - $middleware");
            }
        }

        return 0;
    }
}
