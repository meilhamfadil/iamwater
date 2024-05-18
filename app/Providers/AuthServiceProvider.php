<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\ViewFeaturePermission;
use Exception;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        try {
            $roles = Role::all();
            foreach ($roles as $role) {
                Gate::define('is' . $role->slug, function ($user) use ($role) {
                    return $user->role_id == $role->id;
                });
            }

            $permissions = array_reduce($roles->toArray(), function ($acc, $cur) {
                $features = preg_split('/,/', $cur['permissions']);
                $columns = array_column($acc, 'name');
                foreach ($features as $feature) {
                    if ($feature != "" && !is_null($feature)) {
                        $index = array_search($feature, $columns);
                        if (is_int($index)) {
                            $acc[$index]['ids'][] = $cur['id'];
                        } else {
                            $acc[] = [
                                'name' => $feature,
                                'ids' => [$cur['id']]
                            ];
                        }
                    }
                }
                return $acc;
            }, []);

            foreach ($permissions as $route) {
                Gate::define($route['name'], function ($user) use ($route) {
                    return in_array($user->role_id, $route['ids']);
                });
            }
        } catch (Exception $e) {
        }
    }
}
