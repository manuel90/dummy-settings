@extends('voyager::master')

@section('content')
    <div class="page-content">
        @include('voyager::alerts')
        @include('voyager::dimmers')
        <h1 class="page-title"><i class="voyager-treasure-open"></i> Settings</h1>
        <div class="page-content edit-add container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        @foreach($listSettings as $sKey)
                        @php
                            $setting = \TCG\Voyager\Models\Setting::where('key',$sKey)->first();
                            if(!$setting || $setting->type !== 'text') {
                                continue;
                            }
                        @endphp
                        <form class="form-save-single-gsetting" action="{{ route('dummysettings.store_custom_setting') }}" method="post">
                            <div class="panel-body">
                                {{ csrf_field() }}
                                <div class="form-group  col-md-12">              
                                    <label class="control-label">{{ $setting->display_name }}</label>
                                    <input type="text" class="form-control" name="valset" value="{{ $setting->value }}" required/>
                                    <input type="hidden" name="setting" value="{{ $setting->key }}" />
                                </div>
                                <button class="btn btn-primary save">@lang('dummysettings::general.save')</button>
                            </div>
                        </form>
                        <br/>
                        <br/>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script type="text/javascript">

        $('document').ready(function () {


            var fail = function(error,hrxr) {
                var id_panel = 'lfail_bg_mzra_panel51441';

                console.log(error);
                console.log(error.status);
                console.log(error.statusText);
                console.log(hrxr);

                var options = {
                    seconds: 10,
                    text: 'Connection is lost. Try connect in {t} Message: '+error.statusText,
                };

                var args = {};

                for(var i in options) {
                    if( !args[i] ) {
                        args[i] = options[i];
                    }
                }

                if( document.getElementById(id_panel) ) {
                    return;
                }

                var element = document.createElement('div');
                element.setAttribute('id', id_panel);
                element.style.display = 'block';
                element.style.position = 'fixed';
                element.style.left = '0';
                element.style.top = '0';
                element.style.width = '100%';
                element.style.height = '100%';
                element.style.background = 'rgba(255,255,255,0.5)';
                element.style.zIndex = '9999';
                element.innerHTML = '<span style="color: #fff;position: absolute;left: 50%;top: 50px;transform: translate(-50%,-50%);font-style: italic;font-size: 18px;font-weight: bold;background: rgba(0,0,0,0.9);border-radius:20px;padding: 20px 40px 20px 30px;"><span id="text-lost_1014">'+args.text.replace("{t}",args.seconds)+'</span><i id="dots_1014" style="text-decoration: none;position: absolute;"></i></span>';

                document.body.appendChild(element);

                document.getElementById('text-lost_1014').data_time = args.seconds;

                window.dotsTAnimation = window.setInterval(function(){
                    var e = document.getElementById('text-lost_1014');
                    var t = parseInt(e.data_time);
                    if(t < 0) {
                        return;
                    }
                    document.getElementById('text-lost_1014').innerHTML = args.text.replace(/{t}/g,t);
                    t--;
                    document.getElementById('text-lost_1014').data_time = t;
                    if(t < 0 && window.dotsTAnimation) {
                        clearInterval(window.dotsTAnimation);
                        window.location.reload();
                    }
                },1000);

            };

            var $loader = jQuery('#voyager-loader');
        
           
            $('.form-save-single-gsetting').each(function(idx,el){
                $(el).on('submit',function(e){
                    e.preventDefault();
                    var _this = $(this);
                    if( _this.data('submitting') ) {
                        return;
                    }
                    _this.data('submitting',true);
                    $loader.show();

                    $.ajax({
                        url: '{{ route('dummysettings.store_custom_setting') }}',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            ajax: 'yes',
                            setting: _this.find('input[name="setting"]').first().val(),
                            valset: _this.find('input[name="valset"]').first().val(),
                            _token: '{{ csrf_token() }}',
                        }
                    }).done(function(result){
                        $loader.fadeOut();
                        _this.data('submitting',false);
                        if( result.success ) {
                            toastr.success(result.message);
                        } else {
                            toastr.error(result.message);
                        }
                    }).fail(fail);
                });
            });
        });
    </script>
@stop