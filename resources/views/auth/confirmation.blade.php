@extends('layouts.app')
@section('content')
    <header data-scroll="60" data-scroll-show class="header">
        <div class="header__container">
            <div class="header__wrapper">
                <a href="" class="header__logo">php<span>laravel</span></a>
                <div class="header__wrap">
                    <div class="header__block">
                        <div class="header__overlay"></div>
                        <nav class="header__menu menu">
                            <div class="menu__row">
                                <a href="" class="header__logo header__logo--menu">php<span>laravel</span></a>
                                <button type="button" class="header__close">
                                    <div class="header__icon icon-menu"><span></span></div>
                                </button>
                            </div>
                            <ul class="menu__list">
                                <li class="menu__item">
                                    <a href="#main" class="menu__link"><span>01</span>Головна</a>
                                </li>
                                <li class="menu__item">
                                    <a href="#about" class="menu__link"><span>02</span>Про майстер-клас</a>
                                </li>
                                <li class="menu__item">
                                    <a href="#pay" class="menu__link"><span>04</span>Оплата</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <div class="header__info">
                        <a href="" data-da=".menu,767.98,3" class="header__contact btn _icon-arrow-1">Звʼязатись</a>
                        <button type="button" class="menu__button">
                            <div class="menu__icon icon-menu"><span></span></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main id="success-page">
        <section class="page-form">
            <div class="page-form__container">
                <div class="page-form__wrapper">
                    <form action="{{($type=='registration')?route('register.user'):route('login.user')}}" method="post"  id="myForm" class="page-form__info form">
                                @csrf
{{--                        {{$type}}--}}

{{--                        {{($type=='registration')?'register.user':'login.user'}}--}}
                        <h3 class="form__title">Введіть код</h3>
                        <div class="form__wrap">
                            <div class="form__item">
                                <div class="form__number">
                                    <input data-required data-validate name="number-1" class="number_input" type="number" max_n=1 tabindex=1>
                                    <input data-required data-validate name="number-2" class="number_input" type="number" max_n=1 tabindex=2>
                                    <input data-required data-validate name="number-3" class="number_input" type="number" max_n=1 tabindex=3>
                                    <input data-required data-validate name="number-4" class="number_input" type="number" max_n=1 tabindex=4>
                                </div>
                                <input name="all-number" class="number_input--hidden" type="number" max_n=4>
                            </div>
                        </div>
                        @if(isset($error))
                            <p style="font-size:12px; color:red; padding-bottom:10px;">{{$error}}</p>
                        @endif
{{--                        @if($errors->any())--}}
{{--                            <p style="font-size:12px; color:red; padding-bottom:10px;">{{(session('errors')->first('error'))}}</p>--}}
{{--                        @endif--}}
                        <button type="submit" class="form__button btn">Підтвердити</button>
                        <a href="{{route('auth.login')}}" class="form__link">Вхід</a>
                        <a href="{{route('auth.register')}}" class="form__link">Зареєструватися</a>
                    </form>
                </div>
            </div>
        </section>
    </main>

@endsection
