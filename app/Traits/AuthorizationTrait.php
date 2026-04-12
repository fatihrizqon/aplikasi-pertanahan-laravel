<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

trait AuthorizationTrait
{
    use AuthorizesRequests {
        authorizeResource as baseAuthorizeResource;
        resourceAbilityMap as baseResourceAbilityMap;
        resourceMethodsWithoutModels as baseMethodsWithoutModels;
    }

    /**
     * Map controller methods to policy abilities.
     */
    protected function resourceAbilityMap(): array
    {
        return $this->baseResourceAbilityMap() + [
            'form'   => 'create|update',
            'save'   => 'create|update',
            'delete' => 'delete',
        ];
    }

    /**
     * Resource methods that never require a model instance.
     */
    protected function resourceMethodsWithoutModels(): array
    {
        return array_merge(
            $this->baseMethodsWithoutModels(),
            ['form', 'save']
        );
    }

    /**
     * Override Laravel authorizeResource with merged-ability support.
     */
    public function authorizeResource(
        $model,
        $parameter = null,
        array $options = [],
        $request = null
    ) {
        $this->middleware(function (Request $request, $next) use ($model, $parameter) {

            $method = $request->route()->getActionMethod();
            $abilities = $this->getAbilitiesForMethod($method);

            if ($abilities === null) {
                return $next($request);
            }

            $modelInstance = $this->resolveModelInstance(
                $request,
                $parameter,
                $model
            );

            if ($this->authorizeAny($abilities, $modelInstance, $model)) {
                return $next($request);
            }

            abort(403);
        });
    }

    /**
     * Get abilities mapped to controller method.
     */
    protected function getAbilitiesForMethod(string $method): ?array
    {
        $map = $this->resourceAbilityMap();

        return isset($map[$method])
            ? explode('|', $map[$method])
            : null;
    }

    /**
     * Authorize against any matching ability.
     */
    protected function authorizeAny(
        array $abilities,
        ?Model $modelInstance,
        string $modelClass
    ): bool {
        foreach ($abilities as $ability) {

            if ($this->authorizeAbility($ability, $modelInstance, $modelClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Authorize a single ability.
     */
    protected function authorizeAbility(
        string $ability,
        ?Model $modelInstance,
        string $modelClass
    ): bool {

        if ($this->abilityRequiresModel($ability)) {
            return $modelInstance instanceof Model
                && Gate::allows($ability, $modelInstance);
        }

        return Gate::allows($ability, $modelClass);
    }

    /**
     * Resolve model instance from route parameter or ID.
     */
    protected function resolveModelInstance(
        Request $request,
        ?string $parameter,
        string $modelClass
    ): ?Model {

        if (! $parameter) {
            return null;
        }

        $value = $request->route($parameter);

        if ($value instanceof Model) {
            return $value;
        }

        return is_scalar($value)
            ? $modelClass::find($value)
            : null;
    }

    /**
     * Determine whether an ability requires a model instance.
     */
    protected function abilityRequiresModel(string $ability): bool
    {
        static $modelAbilities = [
            'view',
            'update',
            'delete',
            'restore',
            'forceDelete',
        ];

        return in_array($ability, $modelAbilities, true);
    }
}
