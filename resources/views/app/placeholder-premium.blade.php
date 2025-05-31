@php
/*
View variables:
===============
    - $DIV_CLASSES: string
    - $TITLE: string
    - $DESCRIPTION: string
===============
*/
@endphp

<style>
    .logo-placeholder-premium {
        width: 200px;
        height: 200px;
        border: 2px dashed #ccc;
        border-radius: 6px;
        background: linear-gradient(45deg, #f8f9fc, #e3e6f0);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #999;
        text-align: center;
        font-family: sans-serif;
        padding: 10px;
        position: relative;
    }
    .logo-placeholder-premium span {
        font-weight: bold;
        color: #6c757d;
    }
    .logo-placeholder-premium small {
        font-size: 0.75rem;
        margin-top: 4px;
        color: #aaa;
    }
</style>

<div class="logo-placeholder-premium {{ $DIV_CLASSES ?? '' }}">
    <span>{{ $TITLE ?? __('messages.components.Features.labelPremiumPlan') }}</span>
    <small>{{ $DESCRIPTION ?? '' }}</small>
</div>
