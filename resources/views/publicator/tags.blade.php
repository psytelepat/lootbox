<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>{{ __('lootbox::publicator.tags') }} ({{ $tags->count() }})</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-12">
							<div class="table-responsive">
                                <table class="table table-striped table-sortable">
                                    <tbody>
@foreach( $tags as $tag )
										<tr>
											<td class="table-col-control"><a href="{{ route( config('publicator.route.alias') . 'post.with-tag', [ 'slug' => $tag->slug ] ) }}">{{ $tag->name }}</a></td>
										</tr>
@endforeach
									</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>