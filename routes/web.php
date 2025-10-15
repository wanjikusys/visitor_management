<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\VisitorController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetCheckoutController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ParkingZoneController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\PublicVisitorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Hmis\HmisOpdController;
use App\Http\Controllers\Hmis\HmisWardController;
use App\Http\Controllers\Hmis\HmisDischargesController;
use App\Http\Controllers\Hmis\HmisVisitorsController;
use App\Http\Controllers\Hmis\HmisCarersController;
use App\Http\Controllers\Hmis\HmisVehiclesController;
use App\Http\Controllers\Hmis\HmisDevicesController;
use App\Http\Controllers\Hmis\HmisGatepassController;
use App\Http\Controllers\Hmis\HmisSecurityController;
use App\Http\Controllers\Hmis\HmisReportsController;
use App\Http\Controllers\Hmis\HmisAccountsController;

Route::redirect('/', '/login')->name('home');

Route::get('/visitor/register', [PublicVisitorController::class, 'create'])->name('public.visitor.register');
Route::post('/visitor/register', [PublicVisitorController::class, 'store'])->name('public.visitor.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () { return redirect()->route('admin.dashboard'); })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Visitor Management
    Route::resource('visitors', VisitorController::class);
    Route::post('visitors/{visitor}/checkout', [VisitorController::class, 'checkout'])->name('visitors.checkout');
    Route::post('visitors/{visitor}/checkin', [VisitorController::class, 'checkin'])->name('visitors.checkin');
    Route::post('visitors/{visitor}/blacklist', [VisitorController::class, 'blacklist'])->name('visitors.blacklist');
    Route::delete('visitors/{visitor}/remove-blacklist', [VisitorController::class, 'removeBlacklist'])->name('visitors.remove-blacklist');
    
    // Asset Management
    Route::resource('assets', AssetController::class);
    Route::get('assets/{asset}/checkout', [AssetCheckoutController::class, 'create'])->name('assets.checkout.create');
    Route::post('assets/{asset}/checkout', [AssetCheckoutController::class, 'store'])->name('assets.checkout.store');
    Route::get('assets/checkout/{checkout}/return', [AssetCheckoutController::class, 'returnForm'])->name('assets.checkout.return-form');
    Route::post('assets/checkout/{checkout}/return', [AssetCheckoutController::class, 'return'])->name('assets.checkout.return');
    Route::post('assets/checkout/{checkout}/approve', [AssetCheckoutController::class, 'approve'])->name('assets.checkout.approve');
    
    // Parking Zones
    Route::resource('parking-zones', ParkingZoneController::class);
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Role Management - NEW
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assign-users');
    Route::post('roles/{role}/attach-user', [RoleController::class, 'attachUser'])->name('roles.attach-user');
    Route::delete('roles/{role}/users/{user}', [RoleController::class, 'detachUser'])->name('roles.detach-user');
    Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    
    // Admin Reports (NOT HMIS)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/visitors', [ReportController::class, 'visitors'])->name('reports.visitors');
    Route::get('reports/vehicles', [ReportController::class, 'vehicles'])->name('reports.vehicles');
    Route::get('reports/assets', [ReportController::class, 'assets'])->name('reports.assets');
    
    // ============================================
    // HMIS Routes - All HMIS routes come BEFORE admin vehicle routes
    // ============================================
    Route::prefix('hmis')->name('hmis.')->group(function () {
        
        // OPD Register
        Route::get('/opd-register', [HmisOpdController::class, 'index'])->name('opd.index');
        Route::get('/opd-register/fetch', [HmisOpdController::class, 'fetch'])->name('opd.fetch');
        Route::get('/opd-register/poll', [HmisOpdController::class, 'poll'])->name('opd.poll');
        Route::post('/opd-register/clear-cache', [HmisOpdController::class, 'clearCache'])->name('opd.clear-cache');
        
        // Ward Register
        Route::get('/ward-register', [HmisWardController::class, 'index'])->name('ward.index');
        Route::get('/ward-register/fetch', [HmisWardController::class, 'fetch'])->name('ward.fetch');
        Route::get('/ward-register/poll', [HmisWardController::class, 'poll'])->name('ward.poll');
        Route::post('/ward-register/clear-cache', [HmisWardController::class, 'clearCache'])->name('ward.clear-cache');
        
        // Discharges Done
        Route::get('/discharges-done', [HmisDischargesController::class, 'done'])->name('discharges.done');
        Route::get('/discharges-done/fetch', [HmisDischargesController::class, 'fetch'])->name('discharges.fetch');
        Route::get('/discharges-done/poll', [HmisDischargesController::class, 'poll'])->name('discharges.poll');
        
        // Discharge Requests
        Route::get('/discharges-requested', [HmisDischargesController::class, 'requested'])->name('discharges.requested');
        Route::get('/discharges-requested/fetch', [HmisDischargesController::class, 'fetchRequested'])->name('discharges.requested.fetch');
        Route::get('/discharges-requested/poll', [HmisDischargesController::class, 'pollRequested'])->name('discharges.requested.poll');
        Route::post('/discharges/clear-cache', [HmisDischargesController::class, 'clearCache'])->name('discharges.clear-cache');

        // Inpatient Visitors
        Route::get('/inpatient-visitors', [HmisVisitorsController::class, 'index'])->name('visitors.index');
        Route::get('/inpatient-visitors/fetch', [HmisVisitorsController::class, 'fetch'])->name('visitors.fetch');
        Route::get('/inpatient-visitors/poll', [HmisVisitorsController::class, 'poll'])->name('visitors.poll');
        Route::post('/inpatient-visitors/clear-cache', [HmisVisitorsController::class, 'clearCache'])->name('visitors.clear-cache');
        Route::post('/inpatient-visitors/check-in', [HmisVisitorsController::class, 'storeVisitor'])->name('visitors.check-in');
        Route::post('/inpatient-visitors/{id}/check-out', [HmisVisitorsController::class, 'checkoutVisitor'])->name('visitors.check-out');
        Route::get('/inpatient-visitors/{patientNumber}/active', [HmisVisitorsController::class, 'getActiveVisitors'])->name('visitors.active');

        // Carers Management
        Route::get('/carers', [HmisCarersController::class, 'index'])->name('carers.index');
        Route::get('/carers/patients', [HmisCarersController::class, 'getAdmittedPatients'])->name('carers.patients');
        Route::post('/carers', [HmisCarersController::class, 'store'])->name('carers.store');
        Route::get('/carers/active', [HmisCarersController::class, 'getActiveCarers'])->name('carers.active');
        Route::post('/carers/{id}/checkout', [HmisCarersController::class, 'checkout'])->name('carers.checkout');

        // HMIS Vehicles Management - Specific routes FIRST before generic ones
        Route::get('/vehicles/checked-in', [HmisVehiclesController::class, 'getCheckedIn'])->name('vehicles.checked-in');
        Route::get('/vehicles', [HmisVehiclesController::class, 'index'])->name('vehicles.index');
        Route::post('/vehicles', [HmisVehiclesController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{id}', [HmisVehiclesController::class, 'show'])->name('vehicles.show');
        Route::put('/vehicles/{id}', [HmisVehiclesController::class, 'update'])->name('vehicles.update');
        Route::post('/vehicles/{id}/checkout', [HmisVehiclesController::class, 'checkout'])->name('vehicles.checkout');

        // HMIS Reports - CONSOLIDATED (removed duplicates)
        Route::get('/reports', [HmisReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/visitors', [HmisReportsController::class, 'visitorsReport'])->name('reports.visitors');
        Route::get('/reports/vehicles', [HmisReportsController::class, 'vehiclesReport'])->name('reports.vehicles');
        
        // Other HMIS Modules
        Route::get('/devices-movement', [HmisDevicesController::class, 'index'])->name('devices.index');
        Route::get('/vehicle-gatepass', [HmisGatepassController::class, 'index'])->name('gatepass.index');
        Route::get('/security-backoffice', [HmisSecurityController::class, 'index'])->name('security.index');
        Route::get('/account-operations', [HmisAccountsController::class, 'index'])->name('accounts.index');
    });
    
    // ============================================
    // Admin Vehicle Resource Routes (MUST come AFTER HMIS routes)
    // ============================================
    Route::resource('vehicles', VehicleController::class);
    Route::get('vehicles/{vehicle}/tracking', [VehicleController::class, 'tracking'])->name('vehicles.tracking');
    Route::post('vehicles/{vehicle}/blacklist', [VehicleController::class, 'blacklist'])->name('vehicles.blacklist');
});

require __DIR__.'/auth.php';
