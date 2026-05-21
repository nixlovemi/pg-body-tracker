@php
/*
View variables:
===============
    - $DIV_CLASSES: string
    - $TITLE: string
    - $DESCRIPTION: string
    - $CTA_LABEL: ?string
    - $CTA_URL: ?string
    - $CTA_TARGET: ?string
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
    .logo-placeholder-premium .btn {
        margin-top: 10px;
        white-space: normal;
    }
</style>

<div class="logo-placeholder-premium {{ $DIV_CLASSES ?? '' }}">
    <span>{{ $TITLE ?? __('messages.components.Features.labelPremiumPlan') }}</span>
    <small>{{ $DESCRIPTION ?? '' }}</small>

    @if (!empty($CTA_LABEL) && !empty($CTA_URL))
        <a href="{{ $CTA_URL }}" class="btn btn-sm btn-warning text-white font-weight-bold mt-2" target="{{ $CTA_TARGET ?? '_self' }}" rel="{{ ($CTA_TARGET ?? '_self') === '_blank' ? 'noopener noreferrer' : '' }}">
            {{ $CTA_LABEL }}
        </a>
    @endif
</div>
