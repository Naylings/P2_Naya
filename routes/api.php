<?php

use App\Http\Controllers\Api\RukunController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JabatanController;
use App\Http\Controllers\Api\FamilyController;
use App\Http\Controllers\Api\WargaController;
use App\Http\Controllers\Api\LurahConfigController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});


Route::middleware('auth:api')->group(function () {

    
    
    Route::prefix('users')->group(function () {
    
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    
    });
    
    Route::get('/jabatan', [JabatanController::class, 'index']);
    
    Route::prefix('rukun')->group(function () {
        Route::get('/', [RukunController::class, 'index']);
        Route::post('/', [RukunController::class, 'store']);
        Route::post('/{id}', [RukunController::class, 'destroy']);
    });
    
    Route::prefix('family')->group(function () {
        Route::get('/', [FamilyController::class, 'index']);
        Route::post('/', [FamilyController::class, 'store']);
        Route::get('/{id}', [FamilyController::class, 'show']);
        Route::put('/{id}', [FamilyController::class, 'update']);
        Route::delete('/{id}', [FamilyController::class, 'destroy']);

        
        Route::post('/{id}/set-head', [FamilyController::class, 'setHead']);
        Route::post('/{id}/add-member', [FamilyController::class, 'addMember']);
        Route::delete('/{id}/remove-member', [FamilyController::class, 'removeMember']);
    });
    
    Route::prefix('warga')->group(function () {
        Route::get('/without-family', [WargaController::class, 'withoutFamily']);
        Route::get('/template', [WargaController::class, 'downloadTemplate']);
        Route::post('/import', [WargaController::class, 'import']);

        
        Route::get('/', [WargaController::class, 'index']);
        Route::post('/', [WargaController::class, 'store']);
        Route::get('/{id}', [WargaController::class, 'show']);
        Route::put('/{id}', [WargaController::class, 'update']);
        Route::delete('/{id}', [WargaController::class, 'destroy']);

        
        Route::post('/{id}/assign-family', [WargaController::class, 'assignToFamily']);
        Route::delete('/{id}/remove-family', [WargaController::class, 'removeFromFamily']);
        
        
    });
    
    
    Route::prefix('config')->group(function () {
        Route::post('/', [LurahConfigController::class, 'save']);
        Route::delete('/logo', [LurahConfigController::class, 'deleteLogo']);
    });
});

Route::prefix('config')->group(function () {
    Route::get('/', [LurahConfigController::class, 'show']);
});