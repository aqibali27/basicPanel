<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\Currency;
use App\Model\SocialMedia;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Support\Renderable;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use App\Model\Translation;


class BusinessSettingsController extends Controller
{
    public function __construct(
        private BusinessSetting $business_setting,
        private Currency        $currency,
        private SocialMedia     $social_media,
    )
    {
    }

    public function store_index()
    {
        return view('admin-views.business-settings.store-index');
    }

    /**
     * @return JsonResponse
     */
    public function maintenance_mode(): JsonResponse
    {
        $mode = Helpers::get_business_settings('maintenance_mode');
        $this->business_setting->updateOrInsert(['key' => 'maintenance_mode'], [
            'value' => isset($mode) ? !$mode : 1
        ]);
        if (!$mode) {
            return response()->json(['message' => translate('Maintenance Mode is On.')]);
        }
        return response()->json(['message' => translate('Maintenance Mode is Off.')]);
    }

    /**
     * @param $side
     * @return JsonResponse
     */
    public function currency_symbol_position($side): JsonResponse
    {
        $this->business_setting->updateOrInsert(['key' => 'currency_symbol_position'], [
            'value' => $side
        ]);
        return response()->json(['message' => translate('Symbol position is ') . $side]);
    }


    /**
     * @return Renderable
     */
    public function mail_index(): Renderable
    {
        return view('admin-views.business-settings.mail-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function mail_config(Request $request): RedirectResponse
    {
        $request->has('status') ? $request['status'] = 1 : $request['status'] = 0;
        $this->business_setting->where(['key' => 'mail_config'])->update([
            'value' => json_encode([
                "status" => $request['status'],
                "name" => $request['name'],
                "host" => $request['host'],
                "driver" => $request['driver'],
                "port" => $request['port'],
                "username" => $request['username'],
                "email_id" => $request['email'],
                "encryption" => $request['encryption'],
                "password" => $request['password'],
            ]),
        ]);

        Toastr::success(translate('Configuration updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function mail_send(Request $request): JsonResponse
    {
        $response_flag = 0;
        try {
            $emailServices = Helpers::get_business_settings('mail_config');

            if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                Mail::to($request->email)->send(new \App\Mail\TestEmailSender());
                $response_flag = 1;
            }
        } catch (\Exception $exception) {
            $response_flag = 2;
        }

        return response()->json(['success' => $response_flag]);
    }

    /**
     * @return Renderable
     */
    public function payment_index(): Renderable
    {
        $published_status = 0; // Set a default value
        $payment_published_status = config('get_payment_publish_status');
        if (isset($payment_published_status[0]['is_published'])) {
            $published_status = $payment_published_status[0]['is_published'];
        }

        $routes = config('addon_admin_routes');
        $desiredName = 'payment_setup';
        $payment_url = '';

        foreach ($routes as $routeArray) {
            foreach ($routeArray as $route) {
                if ($route['name'] === $desiredName) {
                    $payment_url = $route['url'];
                    break 2;
                }
            }
        }

        $data_values = Setting::whereIn('settings_type', ['payment_config'])
            ->whereIn('key_name', ['paypal','stripe'])
            ->get();

        return view('admin-views.business-settings.payment-index',  compact('published_status', 'payment_url', 'data_values'));
    }

    public function payment_method_status(Request $request)
    {
        $request['cash_on_delivery'] = $request->has('cash_on_delivery') ? 1 : 0;
        $request['digital_payment'] = $request->has('digital_payment') ? 1 : 0;
        $request['offline_payment'] = $request->has('offline_payment') ? 1 : 0;

        $cod = $this->business_setting->updateOrInsert(['key' => 'cash_on_delivery'],[
            'value' => json_encode([
                'status' => $request['cash_on_delivery']
                ])
            ]);

        $cod = $this->business_setting->updateOrInsert(['key' => 'digital_payment'],[
            'value' => json_encode([
                'status' => $request['digital_payment']
                ])
            ]);

        $cod = $this->business_setting->updateOrInsert(['key' => 'offline_payment'],[
            'value' => json_encode([
                'status' => $request['offline_payment']
                ])
            ]);

        Toastr::success(translate('updated successfully!'));
        return back();
    }

    public function payment_config_update(Request $request)
    {
        $validation = [
            'gateway' => 'required|in:ssl_commerz,paypal,stripe,razor_pay,senang_pay,paystack,paymob_accept,flutterwave,bkash,mercadopago',
            'mode' => 'required|in:live,test'
        ];

        $request['status'] = $request->has('status') ? 1 : 0;

        $additional_data = [];

        if ($request['gateway'] == 'paypal') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'client_id' => 'required_if:status,1',
                'client_secret' => 'required_if:status,1'
            ];
        } elseif ($request['gateway'] == 'stripe') {
            $additional_data = [
                'status' => 'required|in:1,0',
                'api_key' => 'required_if:status,1',
                'published_key' => 'required_if:status,1',
            ];
        }

        $request->validate(array_merge($validation, $additional_data));

        $settings = Setting::where('key_name', $request['gateway'])->where('settings_type', 'payment_config')->first();

        $additional_data_image = $settings['additional_data'] != null ? json_decode($settings['additional_data']) : null;

        if ($request->has('gateway_image')) {
            $gateway_image = Helpers::upload('payment_modules/gateway_image/', 'png', $request['gateway_image']);
        } else {
            $gateway_image = $additional_data_image != null ? $additional_data_image->gateway_image : '';
        }

        if ($request['gateway_title'] == null){
            Toastr::error(translate('payment_gateway_title_is_required!'));
            return back();
        }

        $payment_additional_data = [
            'gateway_title' => $request['gateway_title'],
            'gateway_image' => $gateway_image,
        ];

        $validator = Validator::make($request->all(), array_merge($validation, $additional_data));

        Setting::updateOrCreate(['key_name' => $request['gateway'], 'settings_type' => 'payment_config'], [
            'key_name' => $request['gateway'],
            'live_values' => $validator->validate(),
            'test_values' => $validator->validate(),
            'settings_type' => 'payment_config',
            'mode' => $request['mode'],
            'is_active' => $request->status,
            'additional_data' => json_encode($payment_additional_data),
        ]);

        Toastr::success(GATEWAYS_DEFAULT_UPDATE_200['message']);
        return back();

    }

    /**
     * @return Renderable
     */
    public function currency_index(): Renderable
    {
        return view('admin-views.business-settings.currency-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function currency_store(Request $request): RedirectResponse
    {
        $request->validate([
            'currency_code' => 'required|unique:currencies',
        ]);

        $this->currency->create([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);

        Toastr::success(translate('Currency added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Renderable
     */
    public function currency_edit($id): Renderable
    {
        $currency = Currency::find($id);
        return view('admin-views.business-settings.currency-update', compact('currency'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function currency_update(Request $request, $id): RedirectResponse
    {
        $this->currency->where(['id' => $id])->update([
            "country" => $request['country'],
            "currency_code" => $request['currency_code'],
            "currency_symbol" => $request['symbol'],
            "exchange_rate" => $request['exchange_rate'],
        ]);

        Toastr::success(translate('Currency updated successfully!'));
        return redirect('admin/business-settings/currency-add');
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function currency_delete($id): RedirectResponse
    {
        $this->currency->where(['id' => $id])->delete();

        Toastr::success(translate('Currency removed successfully!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function terms_and_conditions(): Renderable
    {
        $tnc = $this->business_setting->where(['key' => 'terms_and_conditions'])->first();
        if ($tnc == false) {
            $this->business_setting->insert([
                'key' => 'terms_and_conditions',
                'value' => '',
            ]);
        }
        return view('admin-views.business-settings.terms-and-conditions', compact('tnc'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function terms_and_conditions_update(Request $request): RedirectResponse
    {
        $this->business_setting->where(['key' => 'terms_and_conditions'])->update([
            'value' => $request->tnc,
        ]);

        Toastr::success(translate('Terms and Conditions updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function privacy_policy(): Renderable
    {
        $data = $this->business_setting->where(['key' => 'privacy_policy'])->first();
        if ($data == false) {
            $data = [
                'key' => 'privacy_policy',
                'value' => '',
            ];
            $this->business_setting->insert($data);
        }

        return view('admin-views.business-settings.privacy-policy', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function privacy_policy_update(Request $request): RedirectResponse
    {
        $this->business_setting->where(['key' => 'privacy_policy'])->update([
            'value' => $request->privacy_policy,
        ]);

        Toastr::success(translate('Privacy policy updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function about_us(): Renderable
    {
        $data = $this->business_setting->where(['key' => 'about_us'])->first();
        if ($data == false) {
            $data = [
                'key' => 'about_us',
                'value' => '',
            ];
            $this->business_setting->insert($data);
        }

        return view('admin-views.business-settings.about-us', compact('data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function about_us_update(Request $request): RedirectResponse
    {
        $this->business_setting->where(['key' => 'about_us'])->update([
            'value' => $request->about_us,
        ]);

        Toastr::success(translate('About us updated!'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function app_setting_index(): Renderable
    {
        return view('admin-views.business-settings.app-setting-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function app_setting_update(Request $request): RedirectResponse
    {
        if ($request->platform == 'android') {
            $this->business_setting->updateOrInsert(['key' => 'play_store_config'], [
                'value' => json_encode([
                    'status' => $request['play_store_status'],
                    'link' => $request['play_store_link'],
                    'min_version' => $request['android_min_version'],

                ]),
            ]);

            Toastr::success(translate('Updated Successfully for Android'));
            return back();
        }

        if ($request->platform == 'ios') {
            $this->business_setting->updateOrInsert(['key' => 'app_store_config'], [
                'value' => json_encode([
                    'status' => $request['app_store_status'],
                    'link' => $request['app_store_link'],
                    'min_version' => $request['ios_min_version'],
                ]),
            ]);
            Toastr::success(translate('Updated Successfully for IOS'));
            return back();
        }

        Toastr::error(translate('Updated failed'));
        return back();
    }

    /**
     * @return Renderable
     */
    public function social_media(): Renderable
    {
        return view('admin-views.business-settings.social-media');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function fetch(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $data = $this->social_media->orderBy('id', 'desc')->get();
            return response()->json($data);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function social_media_store(Request $request): JsonResponse
    {
        try {
            $this->social_media->updateOrInsert([
                'name' => $request->get('name'),
            ], [
                'name' => $request->get('name'),
                'link' => $request->get('link'),
            ]);

            return response()->json([
                'success' => 1,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'error' => 1,
            ]);
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function social_media_edit(Request $request): JsonResponse
    {
        $data = $this->social_media->where('id', $request->id)->first();
        return response()->json($data);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function social_media_update(Request $request): JsonResponse
    {
        $social_media = $this->social_media->find($request->id);
        $social_media->name = $request->name;
        $social_media->link = $request->link;
        $social_media->save();

        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function social_media_delete(Request $request): JsonResponse
    {
        $br = $this->social_media->find($request->id);
        $br->delete();
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function social_media_status_update(Request $request): JsonResponse
    {
        $this->social_media->where(['id' => $request['id']])->update([
            'status' => $request['status'],
        ]);
        return response()->json([
            'success' => 1,
        ], 200);
    }

    /**
     * @return Renderable
     */
    public function web_footer_index(): Renderable
    {
        return View('admin-views.business-settings.web-footer-index');
    }

    /**
     * @return Renderable
     */
    public function cookies_setup(): Renderable
    {
        return view('admin-views.business-settings.cookies-setup-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cookies_setup_update(Request $request): RedirectResponse
    {
        $this->business_setting->updateOrInsert(['key' => 'cookies'], [
            'value' => json_encode([
                'status' => $request['status'],
                'text' => $request['text'],
            ])
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }

    public function store_setup(Request $request): RedirectResponse
    {
        if ($request->has('self_pickup')) {
            $request['self_pickup'] = 1;
        }
        if ($request->has('delivery')) {
            $request['delivery'] = 1;
        }
        if ($request->has('dm_self_registration')) {
            $request['dm_self_registration'] = 1;
        }
        if ($request->has('toggle_veg_non_veg')) {
            $request['toggle_veg_non_veg'] = 1;
        }

        if ($request->has('email_verification')) {
            $request['email_verification'] = 1;
            $request['phone_verification'] = 0;
        } elseif ($request->has('phone_verification')) {
            $request['email_verification'] = 0;
            $request['phone_verification'] = 1;
        }

        $request['guest_checkout'] = $request->has('guest_checkout') ? 1 : 0;
        $request['partial_payment'] = $request->has('partial_payment') ? 1 : 0;

        $this->business_setting->updateOrInsert(['key' => 'country'], [
            'value' => $request['country']
        ]);

        $this->business_setting->updateOrInsert(['key' => 'time_zone'], [
            'value' => $request['time_zone'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'phone_verification'], [
            'value' => $request['phone_verification']
        ]);

        $this->business_setting->updateOrInsert(['key' => 'email_verification'], [
            'value' => $request['email_verification']
        ]);

        $this->business_setting->updateOrInsert(['key' => 'self_pickup'], [
            'value' => $request['self_pickup'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'delivery'], [
            'value' => $request['delivery'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'platform_open_time'], [
            'value' => $request['platform_open_time'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'platform_close_time'], [
            'value' => $request['platform_close_time'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'platform_name'], [
            'value' => $request['platform_name'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'currency'], [
            'value' => $request['currency'],
        ]);

        $curr_logo = $this->business_setting->where(['key' => 'logo'])->first();
        $this->business_setting->updateOrInsert(['key' => 'logo'], [
            'value' => $request->has('logo') ? Helpers::update('platform/', $curr_logo->value, 'png', $request->file('logo')) : $curr_logo->value
        ]);

        $this->business_setting->updateOrInsert(['key' => 'phone'], [
            'value' => $request['phone'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'email_address'], [
            'value' => $request['email'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'address'], [
            'value' => $request['address'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'email_verification'], [
            'value' => $request['email_verification'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'footer_text'], [
            'value' => $request['footer_text'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'point_per_currency'], [
            'value' => $request['point_per_currency'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'pagination_limit'], [
            'value' => $request['pagination_limit'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'decimal_point_settings'], [
            'value' => $request['decimal_point_settings']
        ]);

        $this->business_setting->updateOrInsert(['key' => 'time_format'], [
            'value' => $request['time_format']
        ]);

        $curr_fav_icon = $this->business_setting->where(['key' => 'fav_icon'])->first();
        $this->business_setting->updateOrInsert(['key' => 'fav_icon'], [
            'value' => $request->has('fav_icon') ? Helpers::update('platform/', $curr_fav_icon->value, 'png', $request->file('fav_icon')) : $curr_fav_icon->value
        ]);

        $this->business_setting->updateOrInsert(['key' => 'dm_self_registration'], [
            'value' => $request['dm_self_registration'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'toggle_veg_non_veg'], [
            'value' => $request['toggle_veg_non_veg'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'guest_checkout'], [
            'value' => $request['guest_checkout'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'partial_payment'], [
            'value' => $request['partial_payment'],
        ]);

        $this->business_setting->updateOrInsert(['key' => 'partial_payment_combine_with'], [
            'value' => $request['partial_payment_combine_with'],
        ]);

        Toastr::success(translate('settings_updated!'));
        return back();
    }
}
