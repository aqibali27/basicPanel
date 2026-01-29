<div class="mt-5 mb-5">
    <div class="inline-page-menu my-4">
        <ul class="list-unstyled">
            <li class="{{Request::is('admin/business-settings/store/store-setup')? 'active': ''}}"><a href="{{route('admin.business-settings.store.store-setup')}}">{{translate('Business_Settings')}}</a></li>
            <li class="{{Request::is('admin/business-settings/store/cookies-setup')? 'active' : ''}}"><a href="{{route('admin.business-settings.store.cookies-setup')}}">{{translate('Cookies Setup')}}</a></li>
        </ul>
    </div>
</div>
