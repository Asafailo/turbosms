@extends('layouts.app')
@section('content')
    <main id="success-page">
        <section class="page-form">
            <div class="page-form__container">
                <div class="page-form__wrapper">

                    <form action="{{route('send.loginRequest')}}"  method="POST" class="page-form__info form">
                        @csrf
                        <h3 class="form__title">Вхід</h3>
                        <div class="form__wrap">
                            <div class="form__item">
                                <input data-error="Введіть номер телефону" data-required="phone" data-validate
                                       name="phone" type="tel" placeholder="Введіть номер телефону"
                                       class="form__input phone-mask">
                            </div>
                        </div>

                        @if($errors->any())
                            <p style="font-size:12px; color:red; padding-bottom:10px;">{{(session('errors')->first('error'))}}</p>
                        @endif
                        <button type="submit" class="form__button btn">Увійти</button>
                        <a href="{{route('auth.register')}}" class="form__link">Зареєструватися</a>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection
