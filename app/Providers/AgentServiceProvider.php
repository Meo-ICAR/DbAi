<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Neuron\Agents\DataAnalystAgent;

class AgentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DataAnalystAgent::class, function ($app) {
            return new DataAnalystAgent();
        });
    }
}
