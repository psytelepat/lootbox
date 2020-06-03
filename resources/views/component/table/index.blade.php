<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    @if($title)
                        <h5>{{$title}}</h5>
                    @endif

                    <?php
                    /*
                     *                 <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#" class="dropdown-item">Config option 1</a>
                            </li>
                            <li><a href="#" class="dropdown-item">Config option 2</a>
                            </li>
                        </ul>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>

                     */
                        ?>
                </div>
                <div class="ibox-content">
                    @if(!empty($notifications))
                        <div class="row">
                            <div class="col-sm-12">
                                {!! $notifications  !!}
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        @if($buttons_body)
                            {!! $buttons_body !!}
                        @endif
                        <?php
                            /*
                             * <div class="col-sm-5 m-b-xs"><select class="form-control-sm form-control input-s-sm inline">
                                <option value="0">Option 1</option>
                                <option value="1">Option 2</option>
                                <option value="2">Option 3</option>
                                <option value="3">Option 4</option>
                            </select>
                        </div>
                        <div class="col-sm-4 m-b-xs">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-sm btn-white ">
                                    <input type="radio" name="options" id="option1" autocomplete="off" checked> Day
                                </label>
                                <label class="btn btn-sm btn-white active">
                                    <input type="radio" name="options" id="option2" autocomplete="off"> Week
                                </label>
                                <label class="btn btn-sm btn-white">
                                    <input type="radio" name="options" id="option3" autocomplete="off"> Month
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group"><input placeholder="Search" type="text"
                                                            class="form-control form-control-sm"> <span
                                        class="input-group-append"> <button type="button" class="btn btn-sm btn-primary">Go!
                                        </button> </span></div>

                        </div>
                             */

                            ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">

                            @if($table_body)
                                <div class="table-responsive">
                                    <table {!! \Psytelepat\lootbox\Helper\Html::attributes($attributes)  !!}>
                                        {!!  $header_body !!}

                                        {!!  $table_body  !!}
                                    </table>
                                </div>
                            @else
                                <div class="jumbotron">
                                    <p>{{ $message_list_empty }}</p>
                                    {!! $buttons_body !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>