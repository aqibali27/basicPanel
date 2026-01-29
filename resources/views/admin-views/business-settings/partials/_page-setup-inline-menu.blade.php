<div class="mt-5 mb-5">
    <div class="inline-page-menu my-4">
        <ul class="list-unstyled">
            <li class="{{Request::is('admin/business-settings/page-setup/about-us')? 'active': ''}}"><a href="{{route('admin.business-settings.page-setup.about-us')}}">{{translate('About Us')}}</a></li>
            <li class="{{Request::is('admin/business-settings/page-setup/terms-and-conditions')?'active':''}}"><a href="{{route('admin.business-settings.page-setup.terms-and-conditions')}}">{{translate('Terms and Condition')}}</a></li>
            <li class="{{Request::is('admin/business-settings/page-setup/privacy-policy')?'active':''}}"><a href="{{route('admin.business-settings.page-setup.privacy-policy')}}">{{translate('Privacy Policy')}}</a></li>
        </ul>
    </div>
</div>

