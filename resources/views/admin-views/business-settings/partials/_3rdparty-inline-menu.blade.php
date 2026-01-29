<div class="mt-5 mb-5">
    <div class="inline-page-menu my-4">
        <ul class="list-unstyled">
            <li class="{{Request::is('admin/business-settings/web-app/third-party/payment-method')? 'active': ''}}"><a href="{{route('admin.business-settings.web-app.payment-method')}}">{{translate('Payment_Methods')}}</a></li>
            <li class="{{Request::is('admin/business-settings/web-app/third-party/mail-config')? 'active': ''}}"><a href="{{route('admin.business-settings.web-app.mail-config')}}">{{translate('Mail_Config')}}</a></li>
            <li class="{{Request::is('admin/business-settings/web-app/third-party/sms-module')? 'active': ''}}"><a href="{{route('admin.business-settings.web-app.sms-module')}}">{{translate('SMS_Config')}}</a></li>
        </ul>
    </div>
</div>
