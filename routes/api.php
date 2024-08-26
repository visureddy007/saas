<?php

use App\Yantrana\Components\Contact\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Yantrana\Components\WhatsAppService\Controllers\WhatsAppServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

    // base url
    Route::any('/', function() {
        return 'api endpoint';
    })->name('api.base_url');
Route::group([
    'middleware' => 'api.vendor.authenticate',
    'prefix' => '{vendorUid}/',
], function () {
    Route::post('/contact/send-message', [
        WhatsAppServiceController::class,
        'apiSendChatMessage',
    ])->name('api.vendor.chat_message.send.process');
    // send media message
    Route::post('/contact/send-media-message', [
        WhatsAppServiceController::class,
        'apiSendMediaChatMessage',
    ])->name('api.vendor.chat_message_media.send.process');
    // send media message
    Route::post('/contact/send-template-message', [
        WhatsAppServiceController::class,
        'apiSendTemplateChatMessage',
    ])->name('api.vendor.chat_template_message.send.process');
    // create new contact
    Route::post('/contact/create', [
        ContactController::class,
        'apiProcessContactCreate',
    ])->name('api.vendor.contact.create.process');
});