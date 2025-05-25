@inject('ValidatePassword', 'App\Helpers\ValidatePassword')

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-light bg-info text-white shadow" role="alert">
            <strong>{{ __('messages.components.ValidatePassword.passwordRulesTitle') }}</strong>
            <br />
            <ul class="p-0 m-0">
                @foreach ($ValidatePassword::getRulesTexts() as $rulesText)
                    <li>- {{ $rulesText }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
