<fieldset class="image-file-upload ttUploadGallery">

  <div class="image-file-upload-gallery">
@include('lootbox::content-block.form.image-file-upload-gallery')
  </div>

  <div class="file-upload ttBaseUploader" url="{{ $uploadURL }}" style="display: {{ (!isset($canUpload) || $canUpload) ? 'block' : 'none' }}">
    <div class="file-upload-field">
      <div class="caption">{{ trans('lootbox::content-block.form.upload') }}</div>
      <input type="file" name="{{ $handle }}"{{ isset($multiple) && $multiple ? ' multiple' : '' }} />
    </div>
  </div>

</fieldset>