<div class="flex items-center flex-wrap gap-2">
    <span class="text-sm font-normal">
        Page Size:
    </span>

    @props([
        'route',
    ])

    <form action="{{ $route }}" method="GET">
        @foreach(request()->query() as $key => $value)
            @if($key !== 'per-page')
                @if(is_array($value))
                    @foreach($value as $k => $v)
                        <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endif
        @endforeach

        <select name="per-page" class="py-2 px-2 pe-8 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" onchange="this.form.submit()">
            @php
            $options = [10, 25, 50, 100];
            $selected = request('per-page');
            @endphp

            <option disabled {{ $selected ? '' : 'selected' }}>Page Size</option>

            @foreach ($options as $option)
                <option value="{{ $option }}" {{ $selected==$option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    </form>
</div>
