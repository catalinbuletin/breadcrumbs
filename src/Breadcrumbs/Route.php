<?php

namespace Watson\Breadcrumbs;

use Illuminate\Contracts\Routing\Registrar;

class Route
{
    /**
     * The base router instance.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $registrar;

    /**
    * Construct the route instance.
    *
    * @param  \Illuminate\Contracts\Routing\Registrar  $registrar
    * @return void
    */
    public function __construct(Registrar $registrar)
    {
        $this->registrar = $registrar;
    }

    /**
     * Get whether a current route is present.
     *
     * @return bool
     */
    public function present(): bool
    {
        return ! is_null($this->registrar->current());
    }

    /**
     * Get the current route name or controller/action pair.
     *
     * @return string
     */
    public function name()
    {
        if ($name = $this->registrar->current()->getName()) {
            return $name;
        }

        $name = $this->registrar->current()->getActionName();

        if ($name === 'Closure') {
            return null;
        }

        $namespace = array_get($this->registrar->current()->getAction(), 'namespace');

        return str_replace($namespace . '\\', '', $name);
    }

    /**
     * Get the current route parameters.
     *
     * @return array
     */
    public function parameters(): array
    {
        return $this->registrar->current()->parameters();
    }
}
