<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\OfflinePaymentMethodController;

Route::group(['namespace' => 'Admin', 'as' => 'admin.'], function () {
    Route::get('lang/{locale}', 'LanguageController@lang')->name('lang');

    /** App Activation */
    Route::group(['middleware' => ['app_activate:get_from_route']], function () {
        Route::get('app-activate/{app_id}', 'SystemController@app_activate')->name('app-activate');
        Route::post('app-activate/{app_id}', 'SystemController@activation_submit');
    });

    /** Authentication */
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit')->middleware('actch');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });

    /** Admin */
    Route::group(['middleware' => ['admin']], function () {
        Route::get('/', 'DashboardController@dashboard')->name('dashboard');
        Route::get('settings', 'SystemController@settings')->name('settings');
        Route::post('settings', 'SystemController@settings_update');
        Route::post('settings-password', 'SystemController@settings_password_update')->name('settings-password');

        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:user_management']], function () {
            Route::get('create', 'CustomRoleController@create')->name('create');
            Route::post('create', 'CustomRoleController@store')->name('store');
            Route::get('update/{id}', 'CustomRoleController@edit')->name('update');
            Route::post('update/{id}', 'CustomRoleController@update');
            Route::delete('delete', 'CustomRoleController@delete')->name('delete');
            Route::get('excel-export', 'CustomRoleController@excel_export')->name('excel-export');
            Route::get('change-status/{id}', 'CustomRoleController@status_change')->name('change-status');
        });

        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:user_management']], function () {
            Route::get('add-new', 'EmployeeController@add_new')->name('add-new');
            Route::post('add-new', 'EmployeeController@store');
            Route::get('list', 'EmployeeController@list')->name('list');
            Route::get('update/{id}', 'EmployeeController@edit')->name('update');
            Route::post('update/{id}', 'EmployeeController@update');
            Route::get('status/{id}/{status}', 'EmployeeController@status')->name('status');
            Route::delete('delete', 'EmployeeController@delete')->name('delete');
            Route::get('excel-export', 'EmployeeController@excel_export')->name('excel-export');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.', 'middleware' => ['module:user_management']], function () {
            Route::get('add', 'DeliveryManController@index')->name('add');
            Route::post('store', 'DeliveryManController@store')->name('store');
            Route::get('list', 'DeliveryManController@list')->name('list');
            Route::get('preview/{id}', 'DeliveryManController@preview')->name('preview');
            Route::get('edit/{id}', 'DeliveryManController@edit')->name('edit');
            Route::post('update/{id}', 'DeliveryManController@update')->name('update');
            Route::delete('delete/{id}', 'DeliveryManController@delete')->name('delete');
            Route::post('search', 'DeliveryManController@search')->name('search');
            Route::get('ajax-is-active', 'DeliveryManController@ajax_is_active')->name('ajax-is-active');
            Route::get('excel-export', 'DeliveryManController@excel_export')->name('excel-export');
            Route::get('pending/list', 'DeliveryManController@pending_list')->name('pending');
            Route::get('denied/list', 'DeliveryManController@denied_list')->name('denied');
            Route::get('update-application/{id}/{status}', 'DeliveryManController@update_application')->name('application');

            Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                Route::get('list', 'DeliveryManController@reviews_list')->name('list');
            });
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.', 'middleware' => ['module:promotion_management']], function () {
            Route::get('add-new', 'NotificationController@index')->name('add-new');
            Route::post('store', 'NotificationController@store')->name('store');
            Route::get('edit/{id}', 'NotificationController@edit')->name('edit');
            Route::post('update/{id}', 'NotificationController@update')->name('update');
            Route::get('status/{id}/{status}', 'NotificationController@status')->name('status');
            Route::delete('delete/{id}', 'NotificationController@delete')->name('delete');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:system_management']], function () {

            Route::group(['prefix' => 'email-setup'], function () {
                Route::get('{type}/{tab?}', 'EmailTemplateController@email_index')->name('email-setup');
                Route::POST('update/{type}/{tab?}', 'EmailTemplateController@update_email_index')->name('email-setup.update');
                Route::get('{type}/{tab}/{status}', 'EmailTemplateController@update_email_status')->name('email-status');

            });

            //store-settings
            Route::group(['prefix' => 'store', 'as' => 'store.'], function () {
                Route::get('store-setup', 'BusinessSettingsController@store_index')->name('store-setup')->middleware('actch');
                Route::post('update-setup', 'BusinessSettingsController@store_setup')->name('update-setup')->middleware('actch');
                Route::get('delivery-fee-setup', 'BusinessSettingsController@delivery_fee_setup')->name('delivery-fee-setup')->middleware('actch');
                Route::post('delivery-fee-setup', 'BusinessSettingsController@update_delivery_fee')->name('update-delivery-fee')->middleware('actch');
                Route::get('main-branch-setup', 'BusinessSettingsController@main_branch_setup')->name('main-branch-setup')->middleware('actch');
                Route::post('delivery-fee-setup', 'BusinessSettingsController@update_delivery_fee')->name('update-delivery-fee')->middleware('actch');
                Route::get('main-branch-setup', 'BusinessSettingsController@main_branch_setup')->name('main-branch-setup')->middleware('actch');

                //app settings
                Route::get('time-schedule', 'TimeScheduleController@time_schedule_index')->name('time_schedule_index');
                Route::post('add-time-schedule', 'TimeScheduleController@add_schedule')->name('time_schedule_add');
                Route::get('time-schedule-remove', 'TimeScheduleController@remove_schedule')->name('time_schedule_remove');

                //location
                Route::get('location-setup', 'LocationSettingsController@location_index')->name('location-setup')->middleware('actch');
                Route::post('update-location', 'LocationSettingsController@location_setup')->name('update-location')->middleware('actch');

                //cookies
                Route::get('cookies-setup', 'BusinessSettingsController@cookies_setup')->name('cookies-setup');
                Route::post('cookies-setup-update', 'BusinessSettingsController@cookies_setup_update')->name('cookies-setup-update');

                Route::get('otp-setup', 'BusinessSettingsController@otp_setup')->name('otp-setup');
                Route::post('otp-setup-update', 'BusinessSettingsController@otp_setup_update')->name('otp-setup-update');

                Route::get('customer-settings', 'BusinessSettingsController@customer_settings')->name('customer.settings');
                Route::post('customer-settings-update', 'BusinessSettingsController@customer_settings_update')->name('customer.settings.update');

                Route::get('order-index', 'BusinessSettingsController@order_index')->name('order-index');
                Route::post('order-update', 'BusinessSettingsController@order_update')->name('order-update');

                Route::get('qrcode-index', 'QRCodeController@index')->name('qrcode-index');
                Route::post('qrcode/store', 'QRCodeController@store')->name('qrcode.store');
                Route::get('qrcode/download-pdf', 'QRCodeController@download_pdf')->name('qrcode.download-pdf');
                Route::get('qrcode/print', 'QRCodeController@print_qrcode')->name('qrcode.print');
            });

            //web-app
            Route::group(['prefix' => 'web-app', 'as' => 'web-app.', 'middleware' => ['module:system_management']], function () {
                Route::get('third-party/mail-config', 'BusinessSettingsController@mail_index')->name('mail-config')->middleware('actch');
                Route::post('third-party/mail-config', 'BusinessSettingsController@mail_config')->middleware('actch');
                Route::post('mail-send', 'BusinessSettingsController@mail_send')->name('mail-send');

                Route::get('third-party/sms-module', 'SMSModuleController@sms_index')->name('sms-module');
                Route::post('sms-module-update/{sms_module}', 'SMSModuleController@sms_update')->name('sms-module-update');

                Route::get('third-party/payment-method', 'BusinessSettingsController@payment_index')->name('payment-method')->middleware('actch');
                Route::post('payment-method-update/{payment_method}', 'BusinessSettingsController@payment_update')->name('payment-method-update')->middleware('actch');
                Route::post('payment-config-update', 'BusinessSettingsController@payment_config_update')->name('payment-config-update')->middleware('actch');
                Route::post('payment-method-status', 'BusinessSettingsController@payment_method_status')->name('payment-method-status')->middleware('actch');

                //system-setup
                Route::group(['prefix' => 'system-setup', 'as' => 'system-setup.'], function () {
                    //app settings
                    Route::get('app-setting', 'BusinessSettingsController@app_setting_index')->name('app_setting');
                    Route::post('app-setting', 'BusinessSettingsController@app_setting_update');

                    //clean db
                    Route::get('db-index', 'DatabaseSettingsController@db_index')->name('db-index');
                    Route::post('db-clean', 'DatabaseSettingsController@clean_db')->name('clean-db');

                    //firebase message
                    Route::get('firebase-message-config', 'BusinessSettingsController@firebase_message_config_index')->name('firebase_message_config_index');
                    Route::post('firebase-message-config', 'BusinessSettingsController@firebase_message_config')->name('firebase_message_config');

                    //language
                    Route::group(['prefix' => 'language', 'as' => 'language.', 'middleware' => []], function () {
                        Route::get('', 'LanguageController@index')->name('index');
                        Route::post('add-new', 'LanguageController@store')->name('add-new');
                        Route::get('update-status', 'LanguageController@update_status')->name('update-status');
                        Route::get('update-default-status', 'LanguageController@update_default_status')->name('update-default-status');
                        Route::post('update', 'LanguageController@update')->name('update');
                        Route::get('translate/{lang}', 'LanguageController@translate')->name('translate');
                        Route::post('translate-submit/{lang}', 'LanguageController@translate_submit')->name('translate-submit');
                        Route::post('remove-key/{lang}', 'LanguageController@translate_key_remove')->name('remove-key');
                        Route::get('delete/{lang}', 'LanguageController@delete')->name('delete');
                    });
                });

                //third-party
                Route::group(['prefix' => 'third-party', 'as' => 'third-party.', 'middleware' => ['module:system_management']], function () {
                    //map api
                    Route::get('map-api-settings', 'BusinessSettingsController@map_api_settings')->name('map_api_settings');
                    Route::post('map-api-settings', 'BusinessSettingsController@update_map_api');
                    //Social Icon
                    Route::get('fetch', 'BusinessSettingsController@fetch')->name('fetch');
                    Route::post('social-media-store', 'BusinessSettingsController@social_media_store')->name('social-media-store');
                    Route::post('social-media-edit', 'BusinessSettingsController@social_media_edit')->name('social-media-edit');
                    Route::post('social-media-update', 'BusinessSettingsController@social_media_update')->name('social-media-update');
                    Route::post('social-media-delete', 'BusinessSettingsController@social_media_delete')->name('social-media-delete');
                    Route::get('social-media-status-update', 'BusinessSettingsController@social_media_status_update')->name('social-media-status-update');
                    //recaptcha
                    Route::get('recaptcha', 'BusinessSettingsController@recaptcha_index')->name('recaptcha_index');
                    Route::post('recaptcha-update', 'BusinessSettingsController@recaptcha_update')->name('recaptcha_update');

                    //fcm-index
                    Route::get('fcm-index', 'BusinessSettingsController@fcm_index')->name('fcm-index')->middleware('actch');
                    Route::get('fcm-config', 'BusinessSettingsController@fcm_config')->name('fcm-config')->middleware('actch');
                    Route::post('update-fcm', 'BusinessSettingsController@update_fcm')->name('update-fcm')->middleware('actch');

                    // on new release > after v9.2
                    Route::get('social-login', 'BusinessSettingsController@social_login')->name('social-login');
                    Route::get('social-login-status', 'BusinessSettingsController@social_login_status')->name('social-login-status');
                    Route::post('update-apple-login', 'BusinessSettingsController@update_apple_login')->name('update-apple-login');

                    Route::get('chat', 'BusinessSettingsController@chat_index')->name('chat');
                    Route::post('chat-update/{name}', 'BusinessSettingsController@chat_update')->name('chat-update');

                    Route::group(['prefix' => 'offline-payment', 'as' => 'offline-payment.'], function(){
                        Route::get('list', 'OfflinePaymentMethodController@list')->name('list');
                        Route::get('add', 'OfflinePaymentMethodController@add')->name('add');
                        Route::post('store', 'OfflinePaymentMethodController@store')->name('store');
                        Route::get('edit/{id}', 'OfflinePaymentMethodController@edit')->name('edit');
                        Route::post('update/{id}', 'OfflinePaymentMethodController@update')->name('update');
                        Route::get('status/{id}/{status}', 'OfflinePaymentMethodController@status')->name('status');
                        Route::post('delete', 'OfflinePaymentMethodController@delete')->name('delete');
                    });

                    Route::get('firebase-otp-verification', 'BusinessSettingsController@firebase_otp_verification')->name('firebase-otp-verification');
                    Route::post('firebase-otp-verification-update', 'BusinessSettingsController@firebase_otp_verification_update')->name('firebase-otp-verification-update');

                });
                Route::group(['as' => 'third-party.', 'middleware' => ['module:system_management']], function () {
                    Route::get('social-media', 'BusinessSettingsController@social_media')->name('social-media');
                });

            });

            Route::post('update-fcm-messages', 'BusinessSettingsController@update_fcm_messages')->name('update-fcm-messages');

            /*Route::get('currency-add', 'BusinessSettingsController@currency_index')->name('currency-add');
            Route::post('currency-add', 'BusinessSettingsController@currency_store');
            Route::get('currency-update/{id}', 'BusinessSettingsController@currency_edit')->name('currency-update');
            Route::put('currency-update/{id}', 'BusinessSettingsController@currency_update');
            Route::delete('currency-delete/{id}', 'BusinessSettingsController@currency_delete')->name('currency-delete');*/

            Route::group(['prefix' => 'page-setup', 'as' => 'page-setup.', 'middleware' => ['module:system_management']], function () {
                Route::get('terms-and-conditions', 'BusinessSettingsController@terms_and_conditions')->name('terms-and-conditions')->middleware('actch');
                Route::post('terms-and-conditions', 'BusinessSettingsController@terms_and_conditions_update')->middleware('actch');

                Route::get('privacy-policy', 'BusinessSettingsController@privacy_policy')->name('privacy-policy')->middleware('actch');
                Route::post('privacy-policy', 'BusinessSettingsController@privacy_policy_update')->middleware('actch');

                Route::get('about-us', 'BusinessSettingsController@about_us')->name('about-us')->middleware('actch');
                Route::post('about-us', 'BusinessSettingsController@about_us_update')->middleware('actch');

                //pages
                Route::get('return-page', 'BusinessSettingsController@return_page_index')->name('return_page_index');
                Route::post('return-page-update', 'BusinessSettingsController@return_page_update')->name('return_page_update');

                Route::get('refund-page', 'BusinessSettingsController@refund_page_index')->name('refund_page_index');
                Route::post('refund-page-update', 'BusinessSettingsController@refund_page_update')->name('refund_page_update');

                Route::get('cancellation-page', 'BusinessSettingsController@cancellation_page_index')->name('cancellation_page_index');
                Route::post('cancellation-page-update', 'BusinessSettingsController@cancellation_page_update')->name('cancellation_page_update');

                // on next release > after 9.2
                Route::get('faq-page', 'BusinessSettingsController@faq_page_index')->name('faq-page-index');
            });
            Route::get('currency-position/{position}', 'BusinessSettingsController@currency_symbol_position')->name('currency-position');
            Route::get('maintenance-mode', 'BusinessSettingsController@maintenance_mode')->name('maintenance-mode');

        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['actch', 'module:user_management']], function () {
            Route::post('add-point/{id}', 'CustomerController@add_point')->name('add-point');
            Route::get('set-point-modal-data/{id}', 'CustomerController@set_point_modal_data')->name('set-point-modal-data');
            Route::get('list', 'CustomerController@customer_list')->name('list');
            Route::get('view/{user_id}', 'CustomerController@view')->name('view');
            Route::post('search', 'CustomerController@search')->name('search');
            Route::post('AddPoint/{id}', 'CustomerController@AddPoint')->name('AddPoint');
            Route::get('transaction', 'CustomerController@transaction')->name('transaction');
            Route::get('transaction/{id}', 'CustomerController@customer_transaction')->name('customer_transaction');
            Route::get('subscribed-emails', 'CustomerController@subscribed_emails')->name('subscribed_emails');
            Route::get('update-status/{id}', 'CustomerController@update_status')->name('update_status');
            Route::delete('delete', 'CustomerController@destroy')->name('destroy');
            Route::get('excel-import', 'CustomerController@excel_import')->name('excel_import');

            Route::get('chat', 'CustomerController@chat')->name('chat');
            Route::post('get-user-info', 'CustomerController@get_user_info')->name('get_user_info');
            Route::post('message-notification', 'CustomerController@message_notification')->name('message_notification');
            Route::post('chat-image-upload', 'CustomerController@chat_image_upload')->name('chat_image_upload');

            Route::get('settings', 'CustomerController@settings')->name('settings');
            Route::post('update-settings', 'CustomerController@update_settings')->name('update-settings');

            Route::get('select-list', 'CustomerWalletController@get_customers')->name('select-list');

            Route::get('loyalty-point/report', 'LoyaltyPointController@report')->name('loyalty-point.report');

            Route::group(['prefix' => 'wallet', 'as' => 'wallet.'], function () {
                Route::get('add-fund', 'CustomerWalletController@add_fund_view')->name('add-fund');
                Route::post('add-fund', 'CustomerWalletController@add_fund')->name('add-fund-store');
                Route::get('report', 'CustomerWalletController@report')->name('report');

                Route::group(['prefix' => 'bonus', 'as' => 'bonus.'], function () {
                    Route::get('index', 'WalletBonusController@index')->name('index');
                    Route::post('store', 'WalletBonusController@store')->name('store');
                    Route::get('edit/{id}', 'WalletBonusController@edit')->name('edit');
                    Route::post('update/{id}', 'WalletBonusController@update')->name('update');
                    Route::get('status/{id}/{status}', 'WalletBonusController@status')->name('status');
                    Route::delete('delete/{id}', 'WalletBonusController@delete')->name('delete');
                });
            });
        });

        Route::group(['namespace' => 'System','prefix' => 'system-addon', 'as' => 'system-addon.', 'middleware'=>['module:user_management']], function () {
            Route::get('/', 'AddonController@index')->name('index');
            Route::post('publish', 'AddonController@publish')->name('publish');
            Route::post('activation', 'AddonController@activation')->name('activation');
            Route::post('upload', 'AddonController@upload')->name('upload');
            Route::post('delete', 'AddonController@delete_theme')->name('delete');
        });
    });
});

