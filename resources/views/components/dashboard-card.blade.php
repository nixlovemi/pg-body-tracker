<style>
    .cardIsClickable {
        cursor: pointer;
    }
</style>

@php
$jsOnClick = $card->getClickUrl() ? "window.location.href='{$card->getClickUrl()}'" : 'javascript:;';
@endphp

<div onclick="{{$jsOnClick}}" @class(["card border-left-{$card->getCardClass()} shadow h-100 py-2", 'cardIsClickable' => !empty($card->getClickUrl())])>
    <div class="card-body">
        <div class="row no-gutters align-items-center">
            <div class="col mr-2">
                <div class="text-xs font-weight-bold text-{{$card->getCardClass()}} text-uppercase mb-1">
                    {{$card->getTitle()}}
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{$card->getValue()}}
                </div>
            </div>
            <div class="col-auto fa-2x">
                {!!$card->getIcon()!!}
            </div>
        </div>
    </div>
</div>
