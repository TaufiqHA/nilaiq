<?php

namespace App\Providers;

use Google\Client as GoogleClient;
use Google\Service\Drive as GoogleDriveService;
use GuzzleHttp\Client;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\UnableToReadFile;
use Masbug\Flysystem\GoogleDriveAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('google', function ($app, $config) {
            $client = new GoogleClient;

            if ($app->environment('local')) {
                $client->setHttpClient(new Client([
                    'verify' => false,
                ]));
            }

            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $service = new GoogleDriveService($client);
            $adapter = new SafeGoogleDriveAdapter($service, $config['folder'] ?? '/', $config);

            return new FilesystemAdapter(
                new Filesystem($adapter),
                $adapter,
                $config
            );
        });
    }
}

class SafeGoogleDriveAdapter extends GoogleDriveAdapter
{
    public function listContents(string $path, bool $deep): iterable
    {
        try {
            foreach (parent::listContents($path, $deep) as $item) {
                yield $item;
            }
        } catch (UnableToReadFile $e) {
            if (str_contains($e->getMessage(), 'File not found')) {
                return;
            }
            throw $e;
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'File not found')) {
                return;
            }
            throw $e;
        }
    }
}
