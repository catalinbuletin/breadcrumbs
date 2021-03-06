<?php

namespace Watson\Breadcrumbs;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Generator
{
    /**
     * The current route.
     *
     * @var \Watson\Breadcrumbs\Route
     */
    protected $route;

    /**
     * The breadcrumb registrar.
     *
     * @var \Watson\Breadcrumbs\Route
     */
    protected $registrar;

    /**
     * The breadcrumb trail.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $breadcrumbs;

    /**
     * Create a new instance of the generator.
     *
     * @param  \Watson\Breadcrumsb\Route  $route
     * @param  \Watson\Breadcrumbs\Registrar  $registrar
     * @return void
     */
    public function __construct(Route $route, Registrar $registrar)
    {
        $this->route = $route;
        $this->registrar = $registrar;
        $this->breadcrumbs = new Collection;
    }

    /**
     * Register a definition with the registrar.
     *
     * @param  string  $name
     * @param  \Closure  $definition
     * @return void
     * @throws \Watson\Breadcrumbs\Exceptions\DefinitionAlreadyExists
     */
    public function register(string $name, Closure $definition)
    {
        $this->registrar->set($name, $definition);
    }

    /**
     * Generate the collection of breadcrumbs from the given route.
     *
     * @return \Illuminate\Support\Collection
     */
    public function generate(): Collection
    {
        if ($this->route->present() && $this->registrar->has($this->route->name())) {
            $this->call(
                $this->route->name(),
                $this->route->parameters()
            );
        }

        return $this->breadcrumbs;
    }

    /**
     * Call a parent route with the given parameters.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @return void
     */
    public function parent(string $name, ...$parameters)
    {
        $this->call($name, $parameters);
    }

    /**
     * Add a breadcrumb to the collection.
     *
     * @param  string  $title
     * @param  string  $url
     * @return void
     */
    public function add(string $title, string $url)
    {
        $this->breadcrumbs->push(new Breadcrumb($title, $url));
    }

    /**
     * Call the breadcrumb definition with the given parameters.
     *
     * @param  string  $name
     * @param  array  $parameters
     * @return void
     * @throws \Watson\Breadcrumbs\DefinitionNotFoundException
     */
    protected function call(string $name, array $parameters)
    {
        $definition = $this->registrar->get($name);

        $parameters = Arr::prepend(array_values($parameters), $this);

        call_user_func_array($definition, $parameters);
    }
}
