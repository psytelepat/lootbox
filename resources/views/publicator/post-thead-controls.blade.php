<div class="row py-2">
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">
                {!! config('laravel-table.icon.rowsNumber') !!}
            </span>
        </div>
        <select name="lng" class="form-control">
            <option value="">-- Язык --</option>
@foreach( \Psytelepat\Lootbox\Util::locale_choices() as $locale_id => $title )
            <option value="{{ $locale_id }}"{{ $table->request->lng == $locale_id ? ' selected' : null }}>{{ $title }}</option>
@endforeach
        </select>
        <div class="input-group-append">
            <span class="input-group-text py-0">
                <button class="btn btn-link p-0 text-primary" type="submit">
                    {!! config('laravel-table.icon.validate') !!}
                </button>
            </span>
        </div>
    </div>
    @if( config('publicator.with_categories') )
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">
                {!! config('laravel-table.icon.rowsNumber') !!}
            </span>
        </div>
        <select name="category_id" class="form-control">
            <option value="">-- Категория --</option>
@foreach( \Psytelepat\Lootbox\Publicator\PublicatorCategory::all() as $category )
            <option value="{{ $category->id }}"{{ $table->request->category_id == $category->id ? ' selected' : null }}>{{ $category->title }}</option>
@endforeach
        </select>
        <div class="input-group-append">
            <span class="input-group-text py-0">
                <button class="btn btn-link p-0 text-primary" type="submit">
                    {!! config('laravel-table.icon.validate') !!}
                </button>
            </span>
        </div>
    </div>
    @endif
    @if( config('publicator.with_authors') )
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">
                {!! config('laravel-table.icon.rowsNumber') !!}
            </span>
        </div>
        <select name="author_id" class="form-control">
            <option value="">-- Автор --</option>
@foreach( \Psytelepat\Lootbox\Publicator\PublicatorAuthor::all() as $author )
            <option value="{{ $author->id }}"{{ $table->request->author_id == $author->id ? ' selected' : null }}>{{ $author->name }}</option>
@endforeach
        </select>
        <div class="input-group-append">
            <span class="input-group-text py-0">
                <button class="btn btn-link p-0 text-primary" type="submit">
                    {!! config('laravel-table.icon.validate') !!}
                </button>
            </span>
        </div>
    </div>
    @endif
    @if( config('publicator.with_tags') )
    <div class="input-group col-4">
        <div class="input-group-prepend">
            <span class="input-group-text">
                {!! config('laravel-table.icon.rowsNumber') !!}
            </span>
        </div>
        <select name="tag" class="form-control">
            <option value="">-- Теги --</option>
@foreach( \Psytelepat\Lootbox\Publicator\PublicatorPost::existingTags() as $tag )
            <option value="{{ $tag->slug }}"{{ $table->request->tag == $tag->slug ? ' selected' : null }}>{{ $tag->name }}</option>
@endforeach
        </select>
        <div class="input-group-append">
            <span class="input-group-text py-0">
                <button class="btn btn-link p-0 text-primary" type="submit">
                    {!! config('laravel-table.icon.validate') !!}
                </button>
            </span>
        </div>
    </div>
    @endif
</div>