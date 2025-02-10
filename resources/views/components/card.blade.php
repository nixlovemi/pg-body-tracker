<div @class([
    'card',
    'shadow',
    'mb-4' => !$noMarginBottom,
])>
    <!-- Card Header - Accordion -->
    <a href="#card{{ $cardId }}" @class([
        'd-block',
        'card-header',
        'py-3',
        'font-bold' => true,
        'collapsed' => $closed,
        ]) data-toggle="collapse"
        role="button" aria-expanded="true" aria-controls="card{{ $cardId }}"
    >
        <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
    </a>
    <!-- Card Content - Collapse -->
    <div @class(['collapse', 'show' => !$closed]) class="" id="card{{ $cardId }}" style="">
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>
