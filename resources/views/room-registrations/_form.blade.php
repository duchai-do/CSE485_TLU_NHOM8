@php
    $isStudentRole = auth()->user()->role === 'student';
@endphp

<div class="form-grid">
    <div class="form-group">
        <label>Sinh viên *</label>

        @if($isStudentRole)
            @php $ownStudent = $students->first(); @endphp
            <input type="text"
                   value="{{ $ownStudent?->student_code }} — {{ $ownStudent?->user?->name }}"
                   disabled>
            <input type="hidden" name="student_id" value="{{ $ownStudent?->id }}">
        @else
            <select name="student_id" required>
                <option value="">-- Chọn sinh viên --</option>
                @foreach($students as $studentItem)
                    <option value="{{ $studentItem->id }}"
                        @selected(
                            (string) old(
                                'student_id',
                                $roomRegistration->student_id ?? request('student_id')
                            ) === (string) $studentItem->id
                        )>
                        {{ $studentItem->student_code }} — {{ $studentItem->user?->name }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    <div class="form-group">
        <label>Học kỳ *</label>
        <select name="semester" required>
            @foreach(['1' => 'Học kỳ 1', '2' => 'Học kỳ 2', 'Hè' => 'Học kỳ Hè'] as $value => $label)
                <option value="{{ $value }}"
                    @selected(old('semester', $roomRegistration->semester ?? '1') === $value)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Năm học *</label>
        <input name="academic_year"
               value="{{ old('academic_year', $roomRegistration->academic_year ?? '2026-2027') }}"
               placeholder="2026-2027" required>
    </div>

    <div class="form-group">
        <label>Loại phòng mong muốn</label>
        <select name="preferred_room_type">
            <option value="">Không yêu cầu</option>
            @foreach(['Phòng 4 người', 'Phòng 6 người', 'Phòng 8 người'] as $type)
                <option value="{{ $type }}"
                    @selected(old('preferred_room_type', $roomRegistration->preferred_room_type ?? '') === $type)>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="form-group">
    <label>Ghi chú</label>
    <textarea name="note"
              placeholder="Nguyện vọng hoặc thông tin bổ sung">{{ old('note', $roomRegistration->note ?? '') }}</textarea>
</div>
