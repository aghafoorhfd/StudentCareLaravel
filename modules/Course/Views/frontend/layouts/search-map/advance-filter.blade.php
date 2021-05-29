@php
    $selected = (array) Request::query('terms');
@endphp
<div id="advance_filters" class="d-none">
    <div class="ad-filter-b">
        @foreach ($attributes as $item)
            @php
                $translate = $item->translateOrOrigin(app()->getLocale());
            @endphp
            <div class="filter-item">
                <div class="filter-title"><strong>{{$translate->name}}</strong></div>
                <ul class="filter-items row">
                    @foreach($item->terms as $term)
                        @php $translate = $term->translateOrOrigin(app()->getLocale()); @endphp
                        <li class="filter-term-item col-xs-6 col-md-4">
                            <label><input @if(in_array($term->id,$selected)) checked @endif type="checkbox" name="terms[]" value="{{$term->id}}"> {{$translate->name}}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
    <div class="ad-filter-f text-right">
        <a href="#" onclick="return false" class="btn btn-primary btn-apply-advances">{{__("Apply Filters")}}</a>
    </div>
</div>