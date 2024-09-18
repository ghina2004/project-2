<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CancellationReservationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\consTjourny;
use App\Http\Controllers\EditReservation;
use App\Http\Controllers\filters;
use App\Http\Controllers\hotels;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MostReservationTripController;
use App\Http\Controllers\optionaljourny;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\recentJournies;
use App\Http\Controllers\Reservations;
use App\Http\Controllers\Restaurant;
use App\Http\Controllers\Search;
use App\Http\Controllers\ticket;
use App\Http\Controllers\ticketFromplaneCompany;
use App\Http\Controllers\Transportations;
use App\Http\Controllers\tripshadual;
use App\Http\Controllers\VerificationController;
use App\Models\const_trip;
use App\Models\restaurant as ModelsRestaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::post('/storeFCMToken', [NotificationController::class, 'storeFCMToken']);

Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/password/email', [AuthController::class, 'userforgotpassword']);
    Route::post('/password/code/check', [AuthController::class, 'usercheckcode']);
    Route::post('/password/reset', [AuthController::class, 'userResetPassword']);
    Route::get('/recoveryAccount/{id}', [AuthController::class, 'recoveryAccount']);
    Route::get('/getAllUser', [AuthController::class, 'getAllUser']);


Route::middleware('auth:sanctum')->group(function (){
    Route::get('/logout',[AuthController::class,'logout']);
    Route::get('/deleteAccount', [AuthController::class, 'deleteAccount']);
    Route::get('/showProfile', [AuthController::class, 'showProfile']);
    Route::get('/deleteImageProfile', [AuthController::class, 'deleteImageProfile']);
    Route::post('/updateProfile', [AuthController::class, 'updateProfile']);
    Route::post('/updatePassword', [AuthController::class, 'updatePassword']);
    Route::get('/email/resend',[VerificationController::class,'resend'])->name('verification.resend');});
    Route::get('/email/verify/{id}/{hash}',[VerificationController::class,'verify'])->name('verification.verify');


Route::post('/map', [MapController::class, 'map']);
Route::get('/getMostReservationTrip', [MostReservationTripController::class,'getMostReservationTrip']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cancellationReservationConstJourney/{id}', [CancellationReservationController::class, 'cancellationReservationConstJourney']);
    Route::get('/cancellationReservationOptionalJourny/{id}', [CancellationReservationController::class, 'cancellationReservationOptionalJourny']);
    Route::get('/cancellationReservationTicket/{id}', [CancellationReservationController::class, 'cancellationReservationTicket']);
});
Route::controller(CommentController::class)->middleware('auth:sanctum')->group(function (){
    Route::get('/index/{id}',  'index');
    Route::post('/Comment_store',  'store');
    Route::get('Comment_show/{id}', 'show');
    Route::post('Comment_update/{id}', 'update');
    Route::delete('Comment_delete/{id}',  'delete');
});
Route::controller(RatingController::class)->middleware('auth:sanctum')->group(function (){
    Route::get('/Rating_index/{id}',  'index');
    Route::post('/Rating_store',  'store');
    Route::get('/Rating_show/{id}', 'show');
    Route::post('/Rating_update/{id}', 'update');
    Route::post('/Rating_delete/{id}',  'delete');
    Route::get('/Highest_rating','Highest_rating');
})
;
//Filters
Route::middleware('ChangLanguage')->group(function () {
    Route::post('contients', [filters::class, 'GetContients']);//done//Admin
    Route::post('seasons', [filters::class, 'GetSeasons']);//done//Admin
    Route::post('sections', [filters::class, 'GetSections']);//done//Admin
    Route::post('typeTicket', [filters::class, 'GetTypeTicket']);//done//Admin
});
//Hotels
Route::middleware('ChangLanguage')->group(function () {
    Route::post('AddHotel', [hotels::class, 'AddHotels']);//done//Admin
    Route::post('EditHotel/{hotelId}', [hotels::class, 'EditHotel']);//done//Admin
    Route::post('DeleteHotel/{hotelId}', [hotels::class, 'DeleteHotel']);//done//Admin
    Route::post('GetHotels', [hotels::class, 'GetHotels']);//done//Admin
});
//Transportation
Route::middleware('ChangLanguage')->group(function () {
    Route::post('AddTransportation', [Transportations::class, 'AddTransportations']);//done//Admin
    Route::post('EditTransportation/{transId}', [Transportations::class, 'EditTransportation']);//done//Admin
    Route::post('DeleteTransporation/{transportationId}', [Transportations::class, 'DeleteTransporation']);//done//Admin
    Route::post('GetTransportations', [Transportations::class, 'GetTransportations']);//done//Admin
});
//optionaljourny
Route::middleware('ChangLanguage')->group(function () {
    Route::post('AddOptionalJourny', [optionaljourny::class, 'AddOptionalJourny']);//done//Admin
    Route::post('EditOptionalJourny/{JournyId}', [optionaljourny::class, 'EditOptionalJourny']);//done//Admin
    Route::post('DeleteOptionalJourny/{journyId}', [optionaljourny::class, 'DeleteOptionalJourny']);//done//Admin
    Route::post('GetoptionalJournies', [optionaljourny::class, 'GetoptionalJournies']);//done//Admin
});
//constjourny
Route::middleware('ChangLanguage')->group(function () {
    Route::post('AddConstJourny/{transportationId}/{hotelId}/{trupschadualId}', [consTjourny::class, 'AddConstJourny']);//done//Admin
    Route::post('EditConstJourny/{JournyId}/{transportationId}/{hotelId}/{tripschaudualId}', [consTjourny::class, 'EditConstJourny']);//done//Admin
    Route::post('DeleteConstJourny/{JournyId}', [consTjourny::class, 'DeleteConstJourny']);//done//Admin
    Route::post('GetconstTrips', [consTjourny::class, 'GetconstTrips']);//done//Admin
});
//ticket
Route::middleware('ChangLanguage')->group(function () {
    Route::post('AddTicket', [ticket::class, 'AddTicket']);//done//Admin
    Route::post('editTicket/{TicketId}', [ticket::class, 'editTicket']);//done//Admin
    Route::post('DeleteTicket/{ticketId}', [ticket::class, 'DeleteTicket']);//done//Admin
    Route::post('GetTickets', [ticket::class, 'GetTickets']);//done//Admin
});
//optionaljourny
//Hotels Related To specific destination
Route::middleware('ChangLanguage')->group(function () {
    Route::post('GetSpecificHotels/{optionalTripId}', [hotels::class, 'GetSpecificHotelsOptional']);//done//user
//Transporation Related To specific destination
    Route::post('GetSpecificTransportationsOptional/{optionaltrip}', [Transportations::class, 'GetSpecificTransportationsOptional']);//done//user
//constjourny
//Hotels Related To specific destination
    Route::post('GetSpecificHotelsToConst/{destination}', [hotels::class, 'GetSpecificHotelsToConst']);//done//Admin
    Route::post('choseHotelConst/{hotelId}', [hotels::class, 'choseHotelConst']);//done//Admin
//Transporation Related To specific destination
    Route::post('GetSpecificTransportationsConst/{destination}', [Transportations::class, 'GetSpecificTransportationsConst']);//done//Admin
    Route::post('choseTransportationConst/{transportationId}', [Transportations::class, 'choseTransportationConst']);//done//Admin
//Tripschadual Related To specific destination
    Route::get('getSpecifTripschadual/{destination}/{flyDate}/{flyTime}', [tripshadual::class, 'getSpecifTripschadual']);//done//Admin
    Route::get('choseSpecificTripSChadual/{tripschadualId}', [tripshadual::class, 'choseSpecificTripSChadual']);//done//Admin
});
// schadual Trip
Route::middleware('ChangLanguage')->group(function () {
    Route::post('AddjournySchaduals', [tripshadual::class, 'AddjournySchaduals']);//done//Admin
    Route::post('editJournySchaduals/{schadualId}', [tripshadual::class, 'editJournySchaduals']);//done//Admin
    Route::post('DeleteSchadualTrip/{schadualId}', [tripshadual::class, 'DeleteSchadualTrip']);
    Route::post('GetSchadualTrips', [tripshadual::class, 'GetSchadualTrips']);//done//Admin

//Food
    Route::post('AddFoodToTheMenu', [Restaurant::class, 'AddFoodToTheMenu']);//done//Admin
    Route::post('updateFoodMenu/{restaurantId}', [Restaurant::class, 'updateFoodMenu']);//done//Admin
    Route::post('GetALLFoodTypes', [Restaurant::class, 'GetALLFoodTypes']);//done//Admin
    Route::post('DeleteMenu/{restaurantId}', [Restaurant::class, 'DeleteMenu']);//done//Admin

//optionalJourny reservation
    Route::get('selectHotel/{hotelId}', [Reservations::class, 'selectHotel']);//done//user
    Route::get('selectTransportation/{transportationId}', [Reservations::class, 'selectTransportation']);//done//user
    Route::post('OptionalJournyReservation/{userId}/{optionaljournyId}/{hotelId}/{transportationId}/{tripSchadualId}', [Reservations::class, 'OptionalJournyReservation']);//done//user
// عرض الجداول
//تجريب
    Route::post('displaySchadualsForOptional/{optionaltripID}', [tripshadual::class, 'getSpecifTripschadualForoptional']);//done//user
// اختيار الجدول
    Route::post('choseSchadualForOptional/{tripschadualId}', [tripshadual::class, 'choseSchadualForOptional']);//done//user

// لتاكيد نوع الدفع عندما تكون المحفظة لا تحوي على مال كافي
    Route::post('confirmationForOptional/{reserveId}', [Reservations::class, 'confirmationForOptional']);//done//user
    Route::post('PaymentOptional/{reservId}/{userId}', [Reservations::class, 'PaymentOptional']);//done//user
    Route::post('updatepaymentStatusByAdminForManualPaymentFoOptional/{userId}/{reserveId}', [Reservations::class, 'updatepaymentStatusByAdminForManualPaymentFoOptional']);//done//Admin

//const journy reservation
    Route::post('constTripReservation/{userId}/{constTripId}', [Reservations::class, 'constTripReservation']);//done//user
    Route::post('confirmationForConstTrip/{reserveId}', [Reservations::class, 'confirmationForConstTrip']);//done//user
    Route::post('PaymentConst/{reservId}/{userId}', [Reservations::class, 'PaymentConst']);//done//user
    Route::post('updatepaymentStatusByAdminForManualPaymentForConst/{userId}/{reservedId}', [Reservations::class, 'updatepaymentStatusByAdminForManualPaymentForConst']);//done//Admin
//حساب السعر الاجمالي للرحلة الثابتة
    Route::get('CalculateTotalPriceForConst/{consttripId}/{hotelId}/{transportationId}/{tripSchadualId1}', [consTjourny::class, 'CalculateTotalPriceForConst']);//done//Admin

//ticket
    Route::post('ticketReservation/{userId}/{ticketID}/{transportationID}', [Reservations::class, 'ticketReservation']);//done//user
    Route::post('confirmationForTicket/{reserveId}', [Reservations::class, 'confirmationForTicket']);//done//user
    Route::post('Paymentticket/{reservId}/{userId}', [Reservations::class, 'Paymentticket']);//done//user
    Route::post('updatepaymentStatusByAdminForManualPaymentForticket/{userId}/{reserveId}', [Reservations::class, 'updatepaymentStatusByAdminForManualPaymentForticket']);//done//Admin
//Ticket
    Route::post('GetSpecificTransportationsTicket/{ticketID}', [Transportations::class, 'GetSpecificTransportationsTicket']);//done//user
    Route::post('choseTransportationticket/{transportationId}', [Transportations::class, 'choseTransportationticket']);//done//user

//Food reservation
    Route::get('GetavailableReservation/{userId}', [Restaurant::class, 'GetavailableReservation']);//user//done
    Route::get('chosereservation/{reservId}/{type}', [Restaurant::class, 'chosereservation']);//user//done
//عرض المطعم الرمبوط بالفندق المحجوز
    Route::post('GetRestaurantrelatedToReservedHotel/{userId}/{reservId}/{type}', [Restaurant::class, 'GetRestaurantrelatedToReservedHotel']);//done//user
    Route::post('choseDish/{restaurantId}/{userId}', [Restaurant::class, 'choseDish']);//done//user
//اضافة  مال الى المحفظة
    Route::post('AddMonyToTheWallete/{userId}', [Reservations::class, 'AddMonyToTheWallete']);//done//Admin
// الرحلات المضافة مؤخرا
    Route::post('lastjournies', [recentJournies::class, 'GetLastJournies']);//done//user
//تذاكر بسعر الكلفة
    Route::post('AddticketsFromplaneCompany', [ticketFromplaneCompany::class, 'AddticketsFromplaneCompany']);

//search
    Route::post('search', [Search::class, 'search']);//done//user
//edit reservation
// اختبار امكانية التعديل
//تعديل معلومات الحجز
//optional journy
    Route::post('EditOptionalJournyReservation/{userId}/{reserveId}/{optionaljournyId}/{hotelId}/{transportationId}/{tripSchadualId}', [EditReservation::class, 'EditOptionalJournyReservation']);//done//user
    Route::post('EditTicketReservation/{userId}/{ticketId}/{transportationId}', [EditReservation::class, 'EditTicketReservation']);//done//user
    Route::post('editConstTripReservation/{userId}/{constreserveId}', [EditReservation::class, 'EditConstTripReservation']);
//الدفع للتعديل
    Route::post('paymentEditingForOptional/{oldTotalPrice}/{newTotalPrice}/{userId}', [EditReservation::class, 'paymentEditingForOptional']);//done//user
    Route::post('paymentEditingForConst/{oldTotalPrice}/{newTotalPrice}/{userId}', [EditReservation::class, 'paymentEditingForConst']);//user//done
    Route::post('paymentEditingForTicket/{oldTotalPrice}/{newTotalPrice}/{userId}', [EditReservation::class, 'paymentEditingForTicket']);//done//user

//عرض الرحلات حسب التصنيف
//تجريب
    Route::post('DisplayTripsDependonFORConstTrip/{id1}/{id2}/{id3}/{id4}', [filters::class, 'DisplayTripsDependonFORConstTrip']);//done//user
    Route::post('DisplayTripsDependonForOptionalTrip/{id1}/{id2}/{id3}/{id4}', [filters::class, 'DisplayTripsDependonForOptionalTrip']);//done//user
    Route::post('DisplayTripsDependonFoTicket/{id1}/{id2}', [filters::class, 'DisplayTripsDependonFoTicket']);//done//user

//عرض حجوزات المستخدم
Route::get('reservationsForUser/{userID}', [Reservations::class, 'reservationsForUser']);//user//done
Route::post('getconstchosen/{id}',[consTjourny::class,'getconstchosen']);
Route::post('getoptionaltripschosen/{id}',[optionaljourny::class,'getoptionaltripschosen']);
Route::post('getticketchosen/{id}',[ticket::class,'getticketchosen']);




});

