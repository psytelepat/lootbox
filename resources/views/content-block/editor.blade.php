<script>var content_block_config_{{ $trg }}_{{ $usr }} = {!! json_encode(\Psytelepat\Lootbox\ContentBlock\ContentBlock::jscfg($trg),JSON_UNESCAPED_UNICODE) !!};</script>
<div class="contentEditor">
  <div class="contentBlockController js-content-block-controller" trg="{{ $trg }}" usr="{{ $usr }}" url="{{ \Psytelepat\Lootbox\ContentBlock\ContentBlock::url() }}">
    {!! \Psytelepat\Lootbox\ContentBlock\ContentBlock::htmlContentFor($trg,$usr) !!}
  </div>
</div>