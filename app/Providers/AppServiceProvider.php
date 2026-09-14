<?php

namespace App\Providers;

use App\Services\Pricing\PricingCalculator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use NumberFormatter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PricingCalculator::class);
    }

    public function boot(): void
    {
        // Money is stored as integer rupiah. Render as "Rp 350.000".
        Blade::directive('rupiah', function (string $expression): string {
            return "<?php echo 'Rp ' . number_format((int) ({$expression}), 0, ',', '.'); ?>";
        });

        Blade::directive('datetime_id', function (string $expression): string {
            return "<?php echo \\Carbon\\Carbon::parse({$expression})->locale('id')->translatedFormat('d F Y, H.i'); ?>";
        });

        Blade::directive('date_id', function (string $expression): string {
            return "<?php echo \\Carbon\\Carbon::parse({$expression})->locale('id')->translatedFormat('d F Y'); ?>";
        });
    }
}
