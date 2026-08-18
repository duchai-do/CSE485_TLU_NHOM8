
<div class="form-grid">
    <div class="form-group">
        <label>Tài khoản sinh viên *</label>
        <select name="user_id" required>
            <option value="">-- Chọn tài khoản --</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}"
                    @selected((string) old('user_id', $student->user_id ?? '') === (string) $user->id)>
                    {{ $user->name }} — {{ $user->email }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Mã sinh viên *</label>
        <input name="student_code"
               value="{{ old('student_code', $student->student_code ?? '') }}"
               maxlength="30" required>
    </div>

    <div class="form-group">
        <label>Ngày sinh</label>
        <input type="date" name="date_of_birth"
               value="{{ old('date_of_birth', isset($student) && $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '') }}">
    </div>

    <div class="form-group">
        <label>Giới tính *</label>
        <select name="gender" required>
            <option value="">-- Chọn --</option>
            @foreach(['Nam', 'Nữ', 'Khác'] as $gender)
                <option value="{{ $gender }}"
                    @selected(old('gender', $student->gender ?? '') === $gender)>
                    {{ $gender }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Lớp</label>
        <input name="class_name"
               value="{{ old('class_name', $student->class_name ?? '') }}">
    </div>

    <div class="form-group">
        <label>Khoa</label>
        <input name="faculty"
               value="{{ old('faculty', $student->faculty ?? '') }}">
    </div>

    <div class="form-group">
        <label>Số điện thoại</label>
        <input name="phone"
               value="{{ old('phone', $student->phone ?? '') }}">
    </div>

    <div class="form-group">
        <label>Đối tượng ưu tiên</label>
        <input name="priority_type"
               value="{{ old('priority_type', $student->priority_type ?? '') }}"
               placeholder="Ví dụ: Hộ nghèo, Con thương binh...">
    </div>
</div>

<div class="form-group">
    <label>Địa chỉ</label>
    <textarea name="address">{{ old('address', $student->address ?? '') }}</textarea>
</div>
