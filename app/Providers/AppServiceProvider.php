<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Events\Interfaces\INotificationEvent;
use App\Listeners\DispatchNotificationListener;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Event::listen(
            INotificationEvent::class,
            DispatchNotificationListener::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.preline');

        Blade::directive('date', function ($value) {
            return "<?php echo \Carbon\Carbon::parse($value)->format('Y-m-d'); ?>";
        });

        Blade::directive('currency', function ($expression) {
            if(!is_null($expression)){
                return "<?php echo 'Rp ' . number_format((float) $expression, 2, ',', '.'); ?>";
            }else {
                return "<?php echo ''; ?>";
            }
        });

        Blade::directive('bulan', fn($value) => "<?php
            echo [
                1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
            ][$value] ?? '';
        ?>");
    }
}
