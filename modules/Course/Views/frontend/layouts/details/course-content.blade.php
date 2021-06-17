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
                                            @foreach($item->lessons as $counter => $lesson)
                                                @php($allDurations += $lesson->duration)
                                                <li>
                                                    {{$lesson->name}}
                                                    <span class="cs_time float-right">{{convertToHoursMinutes($lesson->duration)}}</span>
                                                    @if(($is_student && $is_student->active) || ($key == 0 && $counter == 0))
                                                        <a title="Download Video" target="_blank" download="{{$lesson->name}}" href="{{$lesson->getStudyUrlAttribute()}}" class="float-right icon custom-icon cs_time">
                                                            <img src="data:image/svg+xml;base64,PHN2ZyBpZD0iQ2FwYV8xIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCA1MTIgNTEyIiBoZWlnaHQ9IjUxMiIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHdpZHRoPSI1MTIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGc+PHBhdGggZD0ibTM4LjkzOCAzMDkuNDk5di0zNy40OTloLTE1djM3LjQ5OWMwIDEyLjQwNiAxMC4wOTQgMjIuNSAyMi41IDIyLjVoMjA5LjA2NHYtMTVoLTIwOS4wNjNjLTQuMTM2IDAtNy41MDEtMy4zNjQtNy41MDEtNy41eiIvPjxwYXRoIGQ9Im0zOC45MzggMjIuNWMwLTQuMTM2IDMuMzY1LTcuNSA3LjUtNy41aDQxLjI0OXYtMTVoLTQxLjI0OGMtMTIuNDA3IDAtMjIuNSAxMC4wOTQtMjIuNSAyMi41djIzNC40OTloMTV2LTIzNC40OTl6Ii8+PHBhdGggZD0ibTM3MC45NCAyMi41djk5Ljc0OWgxNXYtOTkuNzQ5YzAtMTIuNDA2LTEwLjA5NC0yMi41LTIyLjUtMjIuNWgtMjYwLjc1MnYxNWgyNjAuNzUyYzQuMTM1IDAgNy41IDMuMzY0IDcuNSA3LjV6Ii8+PHBhdGggZD0ibTQ4OC4wNjIgMzE2Ljk5OWgtNDIuMTIydi0zNy40OTloLTE1djUyLjQ5OWgyMS41MDRsLTc0LjAwNCA3NS43NjYtNzQuMDAzLTc1Ljc2NmgyMS41MDN2LTE3OS43NTFoMTA1djExMi4yNTFoMTV2LTEyNy4yNTFoLTEzNXYxNzkuNzUxaC00Mi4xMjFsMTA5LjYyMSAxMTIuMjMyeiIvPjxwYXRoIGQ9Im0zODUuOTQxIDQ2N2g3Ni44MTF2MzBoLTE2OC42MjR2LTMwaDc2LjgxMnYtMTVoLTkxLjgxMnY2MGgxOTguNjI0di02MGgtOTEuODExeiIvPjxwYXRoIGQ9Im0zNDAuOTQgNjBoMTV2NDVoLTE1eiIvPjxwYXRoIGQ9Im0zNDAuOTQgMzBoMTV2MTVoLTE1eiIvPjxwYXRoIGQ9Im00MDAuOTQgMTk3LjI0OGgxNXY0NWgtMTV6Ii8+PHBhdGggZD0ibTQwMC45NCAxNjcuMjQ4aDE1djE1aC0xNXoiLz48cGF0aCBkPSJtODMuOTM4IDI4Ni45OTloMTV2MTVoLTE1eiIvPjxwYXRoIGQ9Im01My45MzggMjg2Ljk5OWgxNXYxNWgtMTV6Ii8+PHBhdGggZD0ibTI3Ny4zMTMgMTY2LTEzNy42MjQtODIuMjE4djE2NC40MzV6bS0xMjIuNjI0LTU1Ljc4NCA5My4zNzYgNTUuNzg0LTkzLjM3NiA1NS43ODN6Ii8+PHBhdGggZD0ibTM1NS45NCAzMTYuOTk5aDE1djE1aC0xNXoiLz48cGF0aCBkPSJtMzg1Ljk0IDMxNi45OTloMTV2MTVoLTE1eiIvPjxwYXRoIGQ9Im0xNjkuNjg5IDE1OC41aDE1djE1aC0xNXoiLz48L2c+PC9zdmc+" />
                                                        </a>
                                                        @if(!empty($lesson->getDownloadableLink()))
                                                            <a title="Download File" target="_blank" download="{{$lesson->name}}" href="{{$lesson->getDownloadableLink()}}" class="float-right icon custom-icon cs_time">
                                                                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iNTEycHQiIHZlcnNpb249IjEuMSIgdmlld0JveD0iLTIzIDAgNTEyIDUxMi4wMDA3MiIgd2lkdGg9IjUxMnB0Ij4KPGcgaWQ9InN1cmZhY2UxIj4KPHBhdGggZD0iTSAzNDguOTQ1MzEyIDIyMS42NDA2MjUgTCAzNDguOTQ1MzEyIDEyNC43NDYwOTQgQyAzNDguOTQ1MzEyIDEyMS45NzI2NTYgMzQ3LjY2NDA2MiAxMTkuNDEwMTU2IDM0NS44NTE1NjIgMTE3LjM4MjgxMiBMIDIzNy4yMTg3NSAzLjMwODU5NCBDIDIzNS4xOTE0MDYgMS4xNzU3ODEgMjMyLjMwODU5NCAwIDIyOS40Mjk2ODggMCBMIDU3LjE5NTMxMiAwIEMgMjUuMzk4NDM4IDAgMCAyNS45Mjk2ODggMCA1Ny43MzA0NjkgTCAwIDM4My40MTQwNjIgQyAwIDQxNS4yMTQ4NDQgMjUuMzk4NDM4IDQ0MC43MTg3NSA1Ny4xOTUzMTIgNDQwLjcxODc1IEwgMTkzLjE0ODQzOCA0NDAuNzE4NzUgQyAyMTguODYzMjgxIDQ4My40MDIzNDQgMjY1LjYwNTQ2OSA1MTIgMzE4Ljg1MTU2MiA1MTIgQyAzOTkuNzM4MjgxIDUxMiA0NjUuNzkyOTY5IDQ0Ni4yNjU2MjUgNDY1Ljc5Mjk2OSAzNjUuMjczNDM4IEMgNDY1LjkwMjM0NCAyOTQuNTIzNDM4IDQxNS4xMDU0NjkgMjM1LjQwNjI1IDM0OC45NDUzMTIgMjIxLjY0MDYyNSBaIE0gMjQwLjEwMTU2MiAzNy40NTcwMzEgTCAzMTIuOTg0Mzc1IDExNC4xNzk2ODggTCAyNjUuNzEwOTM4IDExNC4xNzk2ODggQyAyNTEuNjI1IDExNC4xNzk2ODggMjQwLjEwMTU2MiAxMDIuNTUwNzgxIDI0MC4xMDE1NjIgODguNDY0ODQ0IFogTSA1Ny4xOTUzMTIgNDE5LjM3NSBDIDM3LjI0MjE4OCA0MTkuMzc1IDIxLjM0Mzc1IDQwMy4zNjcxODggMjEuMzQzNzUgMzgzLjQxNDA2MiBMIDIxLjM0Mzc1IDU3LjczMDQ2OSBDIDIxLjM0Mzc1IDM3LjY2Nzk2OSAzNy4yNDIxODggMjEuMzQzNzUgNTcuMTk1MzEyIDIxLjM0Mzc1IEwgMjE4Ljc1NzgxMiAyMS4zNDM3NSBMIDIxOC43NTc4MTIgODguNDY0ODQ0IEMgMjE4Ljc1NzgxMiAxMTQuMzk0NTMxIDIzOS43ODEyNSAxMzUuNTIzNDM4IDI2NS43MTA5MzggMTM1LjUyMzQzOCBMIDMyNy42MDE1NjIgMTM1LjUyMzQzOCBMIDMyNy42MDE1NjIgMjE4Ljg2MzI4MSBDIDMyNC40MDIzNDQgMjE4Ljc1NzgxMiAzMjEuODM5ODQ0IDIxOC40Mzc1IDMxOS4wNjY0MDYgMjE4LjQzNzUgQyAyODEuODI0MjE5IDIxOC40Mzc1IDI0Ny41NzAzMTIgMjMyLjczODI4MSAyMjEuNzQ2MDk0IDI1NS4xNDg0MzggTCA4Ni4yMjI2NTYgMjU1LjE0ODQzOCBDIDgwLjM1MTU2MiAyNTUuMTQ4NDM4IDc1LjU1MDc4MSAyNTkuOTQ5MjE5IDc1LjU1MDc4MSAyNjUuODE2NDA2IEMgNzUuNTUwNzgxIDI3MS42ODc1IDgwLjM1MTU2MiAyNzYuNDg4MjgxIDg2LjIyMjY1NiAyNzYuNDg4MjgxIEwgMjAxLjg5ODQzOCAyNzYuNDg4MjgxIEMgMTk0LjMyMDMxMiAyODcuMTYwMTU2IDE4OC4wMjM0MzggMjk3LjgzMjAzMSAxODMuMTE3MTg4IDMwOS41NzAzMTIgTCA4Ni4yMjI2NTYgMzA5LjU3MDMxMiBDIDgwLjM1MTU2MiAzMDkuNTcwMzEyIDc1LjU1MDc4MSAzMTQuMzcxMDk0IDc1LjU1MDc4MSAzMjAuMjQyMTg4IEMgNzUuNTUwNzgxIDMyNi4xMDkzNzUgODAuMzUxNTYyIDMzMC45MTQwNjIgODYuMjIyNjU2IDMzMC45MTQwNjIgTCAxNzYuMTc5Njg4IDMzMC45MTQwNjIgQyAxNzMuNTExNzE5IDM0MS41ODU5MzggMTcyLjEyNSAzNTMuNDI5Njg4IDE3Mi4xMjUgMzY1LjI3MzQzOCBDIDE3Mi4xMjUgMzg0LjQ4MDQ2OSAxNzUuODU5Mzc1IDQwMy40NzY1NjIgMTgyLjU4MjAzMSA0MTkuNDg0Mzc1IEwgNTcuMTk1MzEyIDQxOS40ODQzNzUgWiBNIDMxOC45NjA5MzggNDkwLjc2NTYyNSBDIDI0OS44MTI1IDQ5MC43NjU2MjUgMTkzLjU3NDIxOSA0MzQuNTI3MzQ0IDE5My41NzQyMTkgMzY1LjM3ODkwNiBDIDE5My41NzQyMTkgMjk2LjIzMDQ2OSAyNDkuNzAzMTI1IDIzOS45OTIxODggMzE4Ljk2MDkzOCAyMzkuOTkyMTg4IEMgMzg4LjIxNDg0NCAyMzkuOTkyMTg4IDQ0NC4zNDM3NSAyOTYuMjMwNDY5IDQ0NC4zNDM3NSAzNjUuMzc4OTA2IEMgNDQ0LjM0Mzc1IDQzNC41MjczNDQgMzg4LjEwOTM3NSA0OTAuNzY1NjI1IDMxOC45NjA5MzggNDkwLjc2NTYyNSBaIE0gMzE4Ljk2MDkzOCA0OTAuNzY1NjI1ICIgc3R5bGU9IiBzdHJva2U6bm9uZTtmaWxsLXJ1bGU6bm9uemVybztmaWxsOnJnYigwJSwwJSwwJSk7ZmlsbC1vcGFjaXR5OjE7IiAvPgo8cGF0aCBkPSJNIDg2LjIyMjY1NiAyMjMuMDI3MzQ0IEwgMTk0LjMyMDMxMiAyMjMuMDI3MzQ0IEMgMjAwLjE5MTQwNiAyMjMuMDI3MzQ0IDIwNC45OTIxODggMjE4LjIyMjY1NiAyMDQuOTkyMTg4IDIxMi4zNTU0NjkgQyAyMDQuOTkyMTg4IDIwNi40ODQzNzUgMjAwLjE5MTQwNiAyMDEuNjgzNTk0IDE5NC4zMjAzMTIgMjAxLjY4MzU5NCBMIDg2LjIyMjY1NiAyMDEuNjgzNTk0IEMgODAuMzUxNTYyIDIwMS42ODM1OTQgNzUuNTUwNzgxIDIwNi40ODQzNzUgNzUuNTUwNzgxIDIxMi4zNTU0NjkgQyA3NS41NTA3ODEgMjE4LjIyMjY1NiA4MC4zNTE1NjIgMjIzLjAyNzM0NCA4Ni4yMjI2NTYgMjIzLjAyNzM0NCBaIE0gODYuMjIyNjU2IDIyMy4wMjczNDQgIiBzdHlsZT0iIHN0cm9rZTpub25lO2ZpbGwtcnVsZTpub256ZXJvO2ZpbGw6cmdiKDAlLDAlLDAlKTtmaWxsLW9wYWNpdHk6MTsiIC8+CjxwYXRoIGQ9Ik0gMzczLjU5Mzc1IDM2My4xMzY3MTkgTCAzMjkuNzM4MjgxIDQxMC40MTAxNTYgTCAzMjkuNzM4MjgxIDI5My44ODI4MTIgQyAzMjkuNzM4MjgxIDI4OC4wMTE3MTkgMzI0LjkzMzU5NCAyODMuMjEwOTM4IDMxOS4wNjY0MDYgMjgzLjIxMDkzOCBDIDMxMy4xOTUzMTIgMjgzLjIxMDkzOCAzMDguMzk0NTMxIDI4OC4wMTE3MTkgMzA4LjM5NDUzMSAyOTMuODgyODEyIEwgMzA4LjM5NDUzMSA0MTAuNDEwMTU2IEwgMjY0LjIxNDg0NCAzNjMuMTM2NzE5IEMgMjYwLjE2MDE1NiAzNTguODcxMDk0IDI1My4zMzIwMzEgMzU4LjU1MDc4MSAyNDkuMDYyNSAzNjIuNjA1NDY5IEMgMjQ0Ljc5Mjk2OSAzNjYuNjYwMTU2IDI0NC40NzI2NTYgMzczLjM4MjgxMiAyNDguNTMxMjUgMzc3LjY1MjM0NCBMIDMxMC45NTcwMzEgNDQ0Ljc3MzQzOCBDIDMxMi45ODQzNzUgNDQ2LjkwNjI1IDMxNS43NTc4MTIgNDQ4LjE4NzUgMzE4Ljc0NjA5NCA0NDguMTg3NSBDIDMyMS43MzQzNzUgNDQ4LjE4NzUgMzI0LjUwNzgxMiA0NDYuOTA2MjUgMzI2LjUzNTE1NiA0NDQuNzczNDM4IEwgMzg5LjA3MDMxMiAzNzcuNjUyMzQ0IEMgMzkzLjEyNSAzNzMuMzgyODEyIDM5Mi45MTAxNTYgMzY2LjU1NDY4OCAzODguNjQwNjI1IDM2Mi42MDU0NjkgQyAzODQuMjY1NjI1IDM1OC41NTA3ODEgMzc3LjY1MjM0NCAzNTguODcxMDk0IDM3My41OTM3NSAzNjMuMTM2NzE5IFogTSAzNzMuNTkzNzUgMzYzLjEzNjcxOSAiIHN0eWxlPSIgc3Ryb2tlOm5vbmU7ZmlsbC1ydWxlOm5vbnplcm87ZmlsbDpyZ2IoMCUsMCUsMCUpO2ZpbGwtb3BhY2l0eToxOyIgLz4KPC9nPgo8L3N2Zz4=" />
                                                            </a>
                                                        @endif
                                                        <span
                                                            data-title="{{$lesson->name}}"
                                                            data-url="{{$lesson->getStudyUrlAttribute()}}"
                                                            class="cs_preiew preview_url_lesson icon flaticon-play-button-1 float-right custom-icon"
                                                            title="Play Video"
                                                        </span>
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
