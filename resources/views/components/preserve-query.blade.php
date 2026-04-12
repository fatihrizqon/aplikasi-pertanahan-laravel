@foreach(request()->except($except) as $key => $value)
    @if(is_array($value))
        @foreach($value as $k => $v)
            <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
        @endforeach
    @else
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endif
@endforeach
