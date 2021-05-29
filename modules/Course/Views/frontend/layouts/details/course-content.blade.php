<div class="cs_row_three csv2">
    <div class="course_content">
        <div class="cc_headers">
            <h4 class="title">{{__('Course Content')}}</h4>
            <ul class="course_schdule float-right">
                <li class="list-inline-item" id="course_content_lectures"></li>
                <li class="list-inline-item" id="course_content_durations"></li>
            </ul>
        </div>
        <br>

        <div class="details">
            <div id="accordion" class="panel-group cc_tab accordion">
                @php($allLectures = $allDurations = 0)
                @if(!empty($section_list))
                    @foreach($section_list as $key => $item)
                        <div class="panel">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a href="javascript:void(0)" class="accordion-toggle link" data-toggle="collapse" data-target="#panel{{$item->slug}}">{{$item->name}}</a>
                                </h4>
                            </div>
                            <div id="panel{{$item->slug}}" class="panel-collapse collapse {{$key == 0 ? 'show' : ''}}" data-parent="#accordion">
                                <div class="panel-body">
                                    <ul class="cs_list mb0">
                                        @if(!empty($item->lessons))
                                            @php($allLectures += count($item->lessons))
                                            @foreach($item->lessons as $lesson)
                                                @php($allDurations += $lesson->duration)
                                                <li>
                                                    <a href="javascript:void(0)"><span class="flaticon-play-button-1 icon"></span>
                                                        {{$lesson->name}}<span class="cs_time">{{convertToHoursMinutes($lesson->duration)}}</span>
                                                        @if(!empty($lesson->preview_url))
                                                            <span
                                                                data-title="{{$lesson->name}}"
                                                                data-url="{{getYoutubeEmbedUrl($lesson->preview_url)}}"
                                                                class="cs_preiew preview_url_lesson">{{ __("Preview") }}</span></a>
                                                        @endif
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
@section('script.body')
<script type="text/javascript">
    $('#course_content_lectures').html('{{$allLectures}}'+' Lectures');
    $('#course_content_durations').html('{{convertToHoursMinutes($allDurations)}}');
</script>
@endsection
