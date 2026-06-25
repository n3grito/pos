<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CashRegisterSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseMovementController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\WelcomeController;

use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\DatabaseExplorerController;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\ActivityLogController;

// Fallback para servir archivos de storage cuando no existe el symlink public/storage
Route::get('storage/{path}', function (string $path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }
    return response()->file(Storage::disk('public')->path($path));
})->where('path', '.*');

Route::get('/', [WelcomeController::class, 'index']);
Route::view('/privacy', 'privacy')->name('privacy');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('branches', BranchController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products/export/download', [ProductController::class, 'export'])->name('products.export');
    Route::resource('services', ServiceController::class);
    Route::post('services/import', [ServiceController::class, 'import'])->name('services.import');
    Route::get('services/export/download', [ServiceController::class, 'export'])->name('services.export');
    Route::resource('clients', ClientController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('cash-registers', CashRegisterController::class);
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('currencies', CurrencyController::class);

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('{product}/toggle-availability', [InventoryController::class, 'toggleAvailability'])->name('toggle-availability');
        Route::get('{product}', [InventoryController::class, 'show'])->name('show');
        Route::get('movements/list', [InventoryController::class, 'movements'])->name('movements');
        Route::get('adjustment/new', [InventoryController::class, 'adjustment'])->name('adjustment');
        Route::post('adjustment', [InventoryController::class, 'storeAdjustment'])->name('adjustment.store');
        Route::get('low-stock/alerts', [InventoryController::class, 'lowStock'])->name('low-stock');
    });

    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('incoming/new', [WarehouseMovementController::class, 'transferCreate'])->name('transfer');
        Route::post('incoming', [WarehouseMovementController::class, 'transferStore'])->name('transfer.store');
        Route::get('stock', [WarehouseMovementController::class, 'stockByWarehouse'])->name('stock');
    });

    Route::resource('warehouses', WarehouseController::class);

    Route::get('warehouses/{warehouse}/products', [WarehouseMovementController::class, 'getProducts'])->name('warehouses.products');

    Route::put('currencies/{currency}/toggle-active', [CurrencyController::class, 'toggleActive'])->name('currencies.toggle-active');

    Route::get('/settings/mail', [SettingsController::class, 'mail'])->name('settings.mail');
    Route::post('/settings/mail', [SettingsController::class, 'updateMail'])->name('settings.mail.update');
    Route::post('/settings/mail/test', [SettingsController::class, 'testMail'])->name('settings.mail.test');

    Route::get('/settings/receipt', [SettingsController::class, 'receipt'])->name('settings.receipt');
    Route::post('/settings/receipt', [SettingsController::class, 'updateReceipt'])->name('settings.receipt.update');

    Route::get('/settings/general', [SettingsController::class, 'general'])->name('settings.general');
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');

    Route::put('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

    Route::post('cash-registers/{cashRegister}/open', [CashRegisterSessionController::class, 'open'])->name('cash-registers.open');
    Route::post('cash-register-sessions/{cashRegisterSession}/close', [CashRegisterSessionController::class, 'close'])->name('cash-register-sessions.close');
    Route::get('cash-register-sessions', [CashRegisterSessionController::class, 'index'])->name('cash-register-sessions.index');

    Route::get('/manuals', [ManualController::class, 'index'])->name('manuals.index');
    Route::get('/manuals/{role}', [ManualController::class, 'show'])->name('manuals.show');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales', [ReportController::class, 'salesByDate'])->name('sales');
        Route::get('top-products', [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
    });

    Route::prefix('database')->name('database.')->group(function () {
        Route::get('backups', [DatabaseBackupController::class, 'index'])->name('backups');
        Route::post('backups', [DatabaseBackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{filename}/download', [DatabaseBackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{filename}', [DatabaseBackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('backups/restore', [DatabaseBackupController::class, 'restore'])->name('backups.restore');

        Route::get('explorer', [DatabaseExplorerController::class, 'index'])->name('explorer.index');
        Route::get('explorer/{table}', [DatabaseExplorerController::class, 'show'])->name('explorer.show');
        Route::post('explorer/query', [DatabaseExplorerController::class, 'query'])->name('explorer.query');
    });

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/', [LogViewerController::class, 'index'])->name('index');
        Route::get('{filename}', [LogViewerController::class, 'show'])->name('show');
        Route::delete('{filename}', [LogViewerController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__.'/auth.php';
