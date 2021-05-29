@php $main_color = setting_item('style_main_color','#555555');
$style_typo = json_decode(setting_item_with_lang_raw('style_typo',false,"{}"),true);
@endphp
<style id="custom-css">



    body{
    @if(!empty($style_typo) && is_array($style_typo))
        @foreach($style_typo as $k=>$v)
            @if($v)
                {{str_replace('_','-',$k)}}:{!! $v !!};
            @endif
        @endforeach
    @endif
    }

    {!! clean(setting_item('style_custom_css')) !!}
    {!! clean(setting_item_with_lang_raw('style_custom_css')) !!}
</style>
