<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 py-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lootbox.dashboard') }}">{{ __('lootbox::common.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('lootbox.translation.index') }}">Языковые файлы</a></li>
        </ol>
        <h2>{{ $folder ? $folder . ' / ' : null }}{{ $file }}</h2>
    </div>
</div>

<div class="row wrapper wrapper-content white-bg js-translations" data-url="{{ $form_path_json }}" data-typo-url="{{ $typo_path_json }}">
    <div class="col-lg-12">
        {!! $form_body ?? null !!}
    </div>
</div>