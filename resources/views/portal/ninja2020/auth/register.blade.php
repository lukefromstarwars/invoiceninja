@extends('portal.ninja2020.layout.clean', ['custom_body_class' => 'bg-gray-100'])
@section('meta_title', ctrans('texts.register'))

@section('body')
    <div class="grid lg:grid-cols-12 py-8">
        <div class="col-span-12 lg:col-span-8 lg:col-start-3 xl:col-span-6 xl:col-start-4 px-6">
            <div class="flex justify-center">
                <img class="h-32 w-auto" src="{{ $company->present()->logo() }}" alt="{{ ctrans('texts.logo') }}">
            </div>
            <h1 class="text-center text-3xl mt-8">{{ ctrans('texts.register') }}</h1>
            <p class="block text-center text-gray-600">{{ ctrans('texts.register_label') }}</p>

            <form action="{{ route('client.register', request()->route('company_key')) }}" method="POST" x-data="{ more: false }">
                @csrf

                <div class="grid grid-cols-12 gap-4 mt-10">
                    @foreach($company->client_registration_fields as $field)
                        <div class="col-span-12 md:col-span-6">
                            <section class="flex items-center">
                                <label 
                                    for="{{ $field['key'] }}" 
                                    class="input-label">
                                    {{ ctrans("texts.{$field['key']}") }}
                                </label>
                                
                                @if($field['required'])
                                    <section class="text-red-400 ml-1 text-sm">*</section>
                                @endif
                            </section>

                            @if($field['key'] === 'email')
                                <input 
                                    id="{{ $field['key'] }}" 
                                    class="input w-full" 
                                    type="email"
                                    name="{{ $field['key'] }}"
                                    {{ $field['required'] ? 'required' : '' }} />
                            @elseif($field['key'] === 'password')
                                <input 
                                    id="{{ $field['key'] }}" 
                                    class="input w-full" 
                                    type="password"
                                    name="{{ $field['key'] }}"
                                    {{ $field['required'] ? 'required' : '' }} />
                            @elseif($field['key'] === 'country_id')
                                <select 
                                    id="shipping_country"
                                    class="input w-full form-select"
                                    name="shipping_country">
                                        <option value="none"></option>
                                    @foreach(App\Utils\TranslationHelper::getCountries() as $country)
                                        <option
                                            {{ $country == isset(auth()->user()->client->shipping_country->id) ? 'selected' : null }} value="{{ $country->id }}">
                                            {{ $country->iso_3166_2 }}
                                            ({{ $country->name }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input 
                                    id="{{ $field['key'] }}" 
                                    class="input w-full" 
                                    name="{{ $field['key'] }}"
                                    {{ $field['required'] ? 'required' : '' }} />
                            @endif

                            @error($field['key'])
                                <div class="validation validation-fail">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        @if($field['key'] === 'password') 
                            <div class="col-span-12 md:col-span-6">
                                <section class="flex items-center">
                                    <label 
                                        for="password_confirmation" 
                                        class="input-label">
                                        {{ ctrans('texts.password_confirmation') }}
                                    </label>
                                    
                                    @if($field['required'])
                                        <section class="text-red-400 ml-1 text-sm">*</section>
                                    @endif
                                </section>

                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    class="input w-full" 
                                    name="password_confirmation"
                                    {{ $field['required'] ? 'required' : '' }} />
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="flex justify-between items-center mt-8">
                    <span class="inline-flex items-center" x-data="{ terms_of_service: false, privacy_policy: false }">
                            @if(!empty($company->settings->client_portal_terms) || !empty($company->settings->client_portal_privacy_policy))
                                <input type="checkbox" name="terms" class="form-checkbox mr-2 cursor-pointer" checked>
                                <span class="text-sm text-gray-800">

                                {{ ctrans('texts.i_agree_to_the') }}
                            @endif

                            @includeWhen(!empty($company->settings->client_portal_terms), 'portal.ninja2020.auth.includes.register.popup', ['property' => 'terms_of_service', 'title' => ctrans('texts.terms_of_service'), 'content' => $company->settings->client_portal_terms])
                            @includeWhen(!empty($company->settings->client_portal_privacy_policy), 'portal.ninja2020.auth.includes.register.popup', ['property' => 'privacy_policy', 'title' => ctrans('texts.privacy_policy'), 'content' => $company->settings->client_portal_privacy_policy])

                            @error('terms')
                                <p class="text-red-600">{{ $message }}</p>
                            @enderror
                        </span>
                    </span>

                    <button class="button button-primary bg-blue-600">{{ ctrans('texts.register') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
