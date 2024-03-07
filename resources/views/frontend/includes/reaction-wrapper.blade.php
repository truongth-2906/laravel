@if ($align == 'right')
    <div class="reacts-wrapper reacts-wrapper__right" data-id="{{ $id ?? '' }}">
        <div class="reacts-selected" style="display: none;">
            @foreach ($reactions ?? [] as $reaction)
                <span class="react {{ $reaction->is_reacted ? 'has_reacted' : '' }}" data-content="{{ $reaction->emoji_content }}" data-id="{{ $reaction->emoji_id }}">
                    <span class="count" data-count="{{ $reaction->count }}">{{ $reaction->count }}</span>
                </span>
            @endforeach
        </div>
        <div class="btn-open-react">
            <div class="reacts-suggest"></div>
            <img src="{{ asset('/img/add_reaction.svg') }}" alt="" class="cursor-pointer">
        </div>
    </div>
@else
    <div class="reacts-wrapper reacts-wrapper__left"
        data-id="{{ $id ?? '' }}">
        <div class="btn-open-react">
            <img src="{{ asset('/img/add_reaction.svg') }}" alt="" class="cursor-pointer">
            <div class="reacts-suggest"></div>
        </div>
        <div class="reacts-selected" style="display: none;">
            @foreach ($reactions ?? [] as $reaction)
                <span class="react {{ $reaction->is_reacted ? 'has_reacted' : '' }}" data-content="{{ $reaction->emoji_content }}" data-id="{{ $reaction->emoji_id }}">
                    <span class="count" data-count="{{ $reaction->count }}">{{ $reaction->count }}</span>
                </span>
            @endforeach
        </div>
    </div>
@endif
