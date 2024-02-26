<?php

use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return ['Laravel' => app()->version(), 'Container' => getenv('HOSTNAME')];
});

Route::get('/users', function() {
    return User::all();
});

Route::get('/transactions', function() {
    return Transaction::all();
});

Route::post('/transactions', [TransactionController::class, 'transactMoneyRefactored'])->name('transacitons.store');

require __DIR__.'/auth.php';
