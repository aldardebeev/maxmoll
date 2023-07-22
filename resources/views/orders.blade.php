@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Список товаров</h1>
            <div class="card-deck">
                @foreach($products as $product)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Цена: ${{ $product->price }}</p>
                            <p class="card-text">Наличе: {{ $product->stock }} штук</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <hr>
{{--                <div class="col-md-6">--}}
{{--                    <h3>Количество складов: {{ count($warehouses) }}</h3>--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
</div>
@endsection
