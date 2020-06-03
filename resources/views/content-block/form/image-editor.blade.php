@include('adm.header')



<div class="image-editor">
@foreach( $images as $image )
  <div class="image">
    <form action="/ru/adm/events/edit/{{ $image->usr }}/image/{{ $image->id }}/replace" method="POST" enctype="multipart/form-data">
      <div class="title">TITLE: {{ $image->title }}</div>
      <div class="url">URL: {{ $image->url() }}</div>
      <div class="size">SIZE: {{ $image->w }} x {{ $image->h }}</div>
      <img src="{{ $image->url() }}" border="0" />
      
      <br><br>
      <input type="file" name="photo" />
      <br><br>
      <input type="submit" value="Заменить" />
    </form>
  </div>
@endforeach
</div>



@include('adm.footer')