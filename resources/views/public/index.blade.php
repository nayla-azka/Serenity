<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('images/logoSerenity.jpg') }}" type="image/x-icon">
    <title>Serenity</title>
    <style>
        html, body {
            height: 100%;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Main wrapper (fills space between navbar and footer) --}}
    <div class="flex-fill scrollspy-example" data-bs-spy="scroll" data-bs-target="#main_menu_area" data-bs-root-margin="0px 0px -40%"
         data-bs-smooth-scroll="true" tabindex="0">
         
        @include('public.layouts.navbar')

        {{-- Show carousel only on homepage --}}
        {{-- @if (request()->is('/')) 
            @include('public.carousel')
        @endif

         @if (request()->is('/')) 
            @include('public.artikeldepan')
        @endif
        <@yield('content') --}}
    
{{-- 
        @if (request()->is('/'))
            @include('public.dashboard')
        @endif
        @yield('content') --}}

    {{-- Footer at the very bottom --}}

@include('public.sections.carousel')
@include('public.sections.disArtikel')
@include('public.sections.guru')
@include('public.sections.bergabunglah')

    @include('public.layouts.footer')
    </div>
    @stack('scripts')
    
</body>
</html>
