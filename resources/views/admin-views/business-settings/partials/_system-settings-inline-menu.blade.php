<div class="mb-5 mt-5">
    <ul class="nav nav-tabs border-0 mb-3">
        <li class="nav-item">
            <a class="nav-link {{Request::is('admin/business-settings/web-app/system-setup/language*')? 'active' : ''}}" href="{{route('admin.business-settings.web-app.system-setup.language.index')}}">
                {{translate('Language Setup')}}
            </a>
        </li>
    </ul>
</div>
