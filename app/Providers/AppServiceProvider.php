<?php

namespace App\Providers;

use Monolog\Logger;
use Yansongda\Pay\Pay;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //inject 'alipay' instance into container
        $this->app->singleton('alipay', function () {
            $config               = config('pay.alipay');
            $config['notify_url'] = 'http://requestbin.fullcontact.com/[自己的url]'; //test website
//  实际代码 $config['notify_url'] = route('payment.alipay.notify');
            $config['return_url'] = route('payment.alipay.return');

            //check whether this project operation env is 'production'
            if (app()->environment() !== 'production') {
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }

            //call Yansongda\Pay to create a alipay object
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] = 'http://requestbin.fullcontact.com/[自己的url]'; //test website
// 实际代码  $config['notify_url'] = route('payment.wechat.notify');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }

            //call Yansongda\Pay to create a wechat project
            return Pay::wechat($config);
        });
    }
}
