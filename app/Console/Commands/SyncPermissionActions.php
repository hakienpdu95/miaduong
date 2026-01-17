<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Constants\ModuleConst;

class SyncPermissionActions extends Command
{
    protected $signature = 'permissions:sync-actions';
    protected $description = 'Sync permission actions from ModuleConst to permissions table';

    public function handle()
    {
        $existingColumns = Schema::getColumnListing('permissions');
        $requiredColumns = array_map(fn($action) => "can_{$action}", ModuleConst::getActions());

        foreach ($requiredColumns as $column) {
            if (!in_array($column, $existingColumns)) {
                Schema::table('permissions', function (Blueprint $table) use ($column) {
                    $table->boolean($column)->default(false)->after('can_assign_permission');
                });
                $this->info("Added column {$column} to permissions table.");
            }
        }

        $this->info('Permission actions synced successfully!');
    }
}