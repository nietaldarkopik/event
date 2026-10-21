@if(\App\Services\SessionService::usesChoice($event))
    @php
        $sessionOptions = \App\Services\SessionService::overview($event);
        $firstOpen = $sessionOptions->first(fn($s) => !$s->is_full);
    @endphp
    <div class="form-group">
        <label class="font-weight-bold">Pilih Sesi <span class="text-danger">*</span></label>
        @foreach($sessionOptions as $s)
            <div class="custom-control custom-radio border rounded p-2 pl-5 mb-2 {{ $s->is_full ? 'bg-light text-muted' : '' }}">
                <input type="radio" class="custom-control-input" id="sess-{{ $event->id }}-{{ $s->id }}-{{ $loop->parent->index ?? 0 }}" name="session_id" value="{{ $s->id }}"
                    {{ $s->is_full ? 'disabled' : '' }}
                    {{ old('session_id', $firstOpen ? $firstOpen->id : null) == $s->id && !$s->is_full ? 'checked' : '' }} required>
                <label class="custom-control-label d-block" for="sess-{{ $event->id }}-{{ $s->id }}-{{ $loop->parent->index ?? 0 }}">
                    <b>{{ $s->title }}</b>
                    @if($s->start_datetime)
                        <br><small>{{ \Carbon\Carbon::parse($s->start_datetime)->translatedFormat('l, d F Y') }} &middot;
                            {{ \Carbon\Carbon::parse($s->start_datetime)->format('H.i') }}@if($s->end_datetime)&ndash;{{ \Carbon\Carbon::parse($s->end_datetime)->format('H.i') }}@endif WIB</small>
                    @endif
                    @if($s->room)<br><small>{{ $s->room }}</small>@endif
                    <br>
                    @if($s->is_full)
                        <span class="badge badge-danger">Penuh</span>
                    @elseif($s->remaining !== null)
                        <span class="badge badge-{{ $s->remaining <= 10 ? 'warning' : 'success' }}">Sisa {{ $s->remaining }} kursi</span>
                    @else
                        <span class="badge badge-success">Tersedia</span>
                    @endif
                </label>
            </div>
        @endforeach
        @error('session_id')<span class="text-danger small">{{ $message }}</span>@enderror
    </div>
@endif
