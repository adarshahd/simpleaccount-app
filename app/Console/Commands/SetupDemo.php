<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\AppSettingController;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class SetupDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sa:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup SimpleAccount Demo Application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        printf("Migrating Database . . .\n");
        Artisan::call('migrate:fresh', ['--force' => true]);

        //Roles and Permissions
        printf("Updating roles and permissions . . .\n");
        Artisan::call('db:seed', ['--class' => 'RoleAndPermissionSeeder', '--force' => true]);
        AppSettingController::setRolesAndPermissionsSeeded();



        //Admin User
        printf("Creating admin user . . .\n");
        $adminUser = User::query()->create([
            'name' => 'SimpleAccount Admin',
            'email' => 'admin@simpleaccount.app',
            'password' => Hash::make('password'),
            'email_verified' => true
        ]);
        $adminUser->assignRole('admin');

        //Region Data
        printf("Updating region data . . .\n");
        $regionData = new \stdClass();
        $regionData->country = 'India';
        $regionData->currency_name = 'Indian Rupee';
        $regionData->currency_symbol = '₹';

        AppSetting::query()->create([
            'key' => 'region',
            'value' => json_encode($regionData)
        ]);

        printf("Almost done . . .\n");
        AppSettingController::setApplicationInitialized();

        printf("Complete!\n");
        return 0;
    }
}
