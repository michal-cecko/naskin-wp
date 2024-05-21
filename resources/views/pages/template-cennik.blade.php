@php /* Template Name: Cenník */ @endphp

@extends('layouts.base')

@section('content')
    <section id="cennik">
        @foreach($categories as $category)

            @continue($category->publishedPosts->isEmpty())

            <div class="anchor" id="{{$category->slug}}"></div>

            <div class="category-container">
                <div class="img-container">
                    <img src="{{main()->assets()->static("images/cennik/service-{$category->slug}.jpg")}}"
                         alt="{{$category->name}}">
                </div>
                <div class="container">
                    <div class="tag tag--big tag--white">
                        {{$category->name}}
                    </div>
                    <div class="scrollbox">
                        <div class="prices-container">
                            <table>
                                <thead>
                                <tr>
                                    <th>Služba</th>
                                    <th>Trvanie</th>
                                    <th>Cena</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($category->publishedPosts as $service)
                                    <tr>
                                        <td>{{$service->title}}</td>
                                        <td>{{$service->duration}} min</td>
                                        <td>{{$service->price}} €</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <span>Cenník – NASKIN,s.r.o., prevádzka Centrum 8, M-Park, platný od 1.5.2024</span>
    </section>
@endsection