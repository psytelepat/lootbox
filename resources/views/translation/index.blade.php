<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12 py-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('lootbox.dashboard') }}">{{ __('lootbox::common.dashboard') }}</a></li>
        </ol>
        <h2>Языковые файлы</h2>
    </div>
</div>

<div class="row wrapper wrapper-content white-bg">
    <div class="col-lg-12">
        <div class="form-group row"><div class="col-12">
@foreach( $locales as $localetid => $locale )
    <h3>{{ strtoupper( $localetid ) }}</h3>
    @foreach( $locale['files'] as $file )
        <a href="{{ route('lootbox.translation.form',$file) }}" class="btn btn-primary">{{ $file['folder'] ? $file['folder'] . '/' : '' }}{{ $file['file'] }}</a><br><br>
    @endforeach
@endforeach
        </div></div>
    </div>
</div>