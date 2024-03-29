<?php

namespace NadzorServera\Skijasi\Middleware;

use Closure;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\CaseConvert;
use NadzorServera\Skijasi\Helpers\HandleFile;
use NadzorServera\Skijasi\Helpers\Redis\ConfigurationRedis;
use NadzorServera\Skijasi\Models\Configuration;
use NadzorServera\Skijasi\Models\DataType;

class ApiRequest
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The URIs that should be accessible while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [];

    /**
     * URIs prefix.
     *
     * @var string
     */
    protected $prefix = null;

    /**
     * Maintenance key status.
     *
     * @var string
     */
    private $skijasi_maintenance = null;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->except = config('skijasi.whitelist');
        $this->prefix = config('skijasi.api_route_prefix');
        $this->skijasi_maintenance = config('skijasi.skijasi_maintenance');
    }

    public function handle($request, Closure $next)
    {
        $lang = ($request->hasHeader('Accept-Language')) ? $request->header('Accept-Language') : 'hr';

        app()->setLocale($lang);

        $request->merge(CaseConvert::snake($request->all()));
        $request->merge(HandleFile::normalize($request->all()));

        if ($this->isUnderMaintenance() || $this->app->isDownForMaintenance() || $this->isCrudGeneratedMaintenance($request)) {
            if ($this->isAdministrator()) {
                return $next($request);
            }

            if ($this->inExceptArray($request)) {
                return $next($request);
            }

            return ApiResponse::serviceUnavailable();
        }

        return $next($request);
    }

    protected function isUnderMaintenance()
    {
        if (isset($this->skijasi_maintenance)) {
            if ($this->skijasi_maintenance == true) {
                return true;
            } else {
                return false;
            }
        } else {
            try {
                $configuration_model = ConfigurationRedis::get();
                $maintenance = $configuration_model->where('key', 'maintenance')->firstOrFail();

                return $maintenance->value == '1' ? true : false;
            } catch (Exception $e) {
                $maintenance = Configuration::where('key', 'maintenance')->firstOrFail();

                return $maintenance->value == '1' ? true : false;
            }
        }
    }

    protected function isAdministrator()
    {
        $user = auth()->user();
        if (isset($user)) {
            $roles = $user->roles ?? null;
            if (isset($roles)) {
                $role = $roles->first() ?? null;
                if (isset($role)) {
                    $role_name = $role->name ?? null;
                    if ($role_name === 'administrator') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    protected function isCrudGeneratedMaintenance($request)
    {
        $slug = '';

        if (isset($request->query()['slug'])) {
            $slug = $request->query()['slug'];
        }

        if (preg_match('/\bentities\b/', $request->path())) {
            $slug = explode('/', explode('/entities/', $request->path())[1])[0];
        }

        $data_type = DataType::where('slug', $slug)->first();
        if ($data_type) {
            return $data_type->is_maintenance === 1 ? true : false;
        }

        return false;
    }

    protected function inExceptArray($request)
    {
        $excepts = [];

        foreach ($this->except['api'] as $key => $path) {
            $excepts[] = $this->prefix.$path;
        }

        foreach ($excepts as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
