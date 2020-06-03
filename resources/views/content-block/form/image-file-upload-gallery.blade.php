@if( isset($images) && count($images) )
    <div id="dd{{ Psytelepat\Lootbox\ContentBlock\ContentBlock::uni() }}" class="image-drag-drop ttDragDrop ttImageEditor" url="{{ $uploadURL }}" cellInLine="6">
@foreach( $images as $image )
      <div class="item pv js-image-edit" data-id="{{ $image->lnk }}" style="background-image:url({{ url($image->url()) }});"><input class="drop-id" type="checkbox" name="delete[]" value="{{ $image->lnk }}" /></div>
@endforeach
    </div>
    <div class="controls" data-for="dd{{ Psytelepat\Lootbox\ContentBlock\ContentBlock::uni() }}">
      <span class="select-all">{{ trans('lootbox::content-block.form.select_all') }}</span> | <span class="delete-selected" url="{{ $uploadURL }}/delete">{{ trans('lootbox::content-block.form.delete_selected') }}</span>
    </div>
@endif