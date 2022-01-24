<?php

namespace HaoSheng\Hcaptcha;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Throwable;

class HCaptchaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->bootConfig();
        $this->bootValidator();
    }

    public function bootConfig()
    {
        $config_path = __DIR__ . '/../config/hcatpcha.php';
        $this->mergeConfigFrom($config_path, 'hcaptcha');
    }

    public function bootValidator()
    {
        Validator::extend('HCaptcha', function ($attribute, $value) {
            try {
                $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
                    'secret'   => config('hcaptcha.secret'),
                    'response' => $value,
                    'remoteip' => Request::ip(),
                ]);
                return (bool) $response->json('success');
            } catch (Throwable $throwable) {
                return false;
            }
        }, trans('validation.hcaptcha.failed'));
    }
}
