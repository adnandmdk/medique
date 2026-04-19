@props(['doctors' => collect(), 'schedules' => collect(), 'isAdmin' => false])

<div class="cal-wrap" x-data="calendarApp()" x-init="init()">
    <div class="cal-header">
        <div class="cal-title">📅 Kalender</div>
        <div class="cal-nav">
            <div class="cal-nav-btn" @click="prevMonth()">
                <svg viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
            </div>
            <div class="cal-month" x-text="monthLabel"></div>
            <div class="cal-nav-btn" @click="nextMonth()">
                <svg viewBox="0 0 24 24"><polyline points="9,18 15,12 9,6"/></svg>
            </div>
        </div>
    </div>

    {{-- Day Labels --}}
    <div class="cal-grid">
        <div class="cal-day-label">Sen</div>
        <div class="cal-day-label">Sel</div>
        <div class="cal-day-label">Rab</div>
        <div class="cal-day-label">Kam</div>
        <div class="cal-day-label">Jum</div>
        <div class="cal-day-label weekend">Sab</div>
        <div class="cal-day-label weekend">Min</div>

        {{-- Cells --}}
        <template x-for="cell in cells" :key="cell.key">
            <div class="cal-cell"
                 :class="{
                     'today': cell.isToday,
                     'other-month': !cell.currentMonth,
                     'holiday': cell.holiday
                 }"
                 @click="selectDay(cell)">
                <div class="cal-date"
                     :class="{'weekend': cell.isWeekend}"
                     x-text="cell.day"></div>
                <div x-show="cell.holiday" class="cal-holiday-name" x-text="cell.holiday" style="display:none;"></div>
                @if($isAdmin)
                    <template x-for="doc in cell.doctors" :key="doc">
                        <span class="cal-doctor-chip" x-text="doc"></span>
                    </template>
                    <div x-show="cell.doctors && cell.doctors.length > 2" class="cal-more" x-text="'+' + (cell.doctors.length - 2) + ' lagi'" style="display:none;"></div>
                @endif
            </div>
        </template>
    </div>

    {{-- Detail Modal --}}
    <div x-show="selectedDay" x-cloak
         style="padding:16px 20px;border-top:1px solid var(--border);background:var(--surface2);display:none;"
         :style="selectedDay ? 'display:block' : 'display:none'">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <div style="font-size:13px;font-weight:700;" x-text="selectedLabel"></div>
            <button @click="selectedDay=null" style="background:none;border:none;cursor:pointer;color:var(--text3);font-size:18px;">×</button>
        </div>
        <div x-show="selectedHoliday" style="display:none;">
            <div class="badge badge-cancelled" style="margin-bottom:8px;" x-text="'🎌 ' + selectedHoliday"></div>
        </div>
        @if($isAdmin)
            <div x-show="selectedDoctors && selectedDoctors.length > 0" style="display:none;">
                <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:6px;">Dokter Hadir</div>
                <template x-for="doc in selectedDoctors" :key="doc">
                    <div style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--border);">
                        <div style="width:28px;height:28px;border-radius:7px;background:var(--brand-light);color:var(--brand);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;" x-text="doc.substring(0,2).toUpperCase()"></div>
                        <span style="font-size:13px;font-weight:600;" x-text="doc"></span>
                        <span class="badge badge-hadir" style="margin-left:auto;">Hadir</span>
                    </div>
                </template>
            </div>
            <div x-show="!selectedDoctors || selectedDoctors.length === 0" style="font-size:12px;color:var(--text3);display:none;">Tidak ada dokter yang terjadwal</div>
        @endif
    </div>
</div>

<script>
function calendarApp() {
    return {
        today: new Date(),
        current: new Date(),
        cells: [],
        selectedDay: null,
        selectedLabel: '',
        selectedHoliday: '',
        selectedDoctors: [],

        // ── Hari Libur Nasional Indonesia 2025-2026 ──
        holidays: {
            '2025-01-01': 'Tahun Baru Masehi',
            '2025-01-27': 'Isra Miraj',
            '2025-01-29': 'Tahun Baru Imlek',
            '2025-03-29': 'Hari Suci Nyepi',
            '2025-03-30': 'Idulfitri',
            '2025-03-31': 'Idulfitri',
            '2025-04-01': 'Cuti Bersama Idulfitri',
            '2025-04-02': 'Cuti Bersama Idulfitri',
            '2025-04-03': 'Cuti Bersama Idulfitri',
            '2025-04-04': 'Cuti Bersama Idulfitri',
            '2025-04-18': 'Wafat Isa Almasih',
            '2025-05-01': 'Hari Buruh',
            '2025-05-12': 'Hari Raya Waisak',
            '2025-05-29': 'Kenaikan Isa Almasih',
            '2025-06-01': 'Hari Lahir Pancasila',
            '2025-06-06': 'Iduladha',
            '2025-06-27': 'Tahun Baru Islam',
            '2025-08-17': 'HUT RI',
            '2025-09-05': 'Maulid Nabi',
            '2025-12-25': 'Hari Natal',
            '2025-12-26': 'Cuti Bersama Natal',
            '2026-01-01': 'Tahun Baru Masehi',
            '2026-02-17': 'Tahun Baru Imlek',
            '2026-03-19': 'Hari Suci Nyepi',
            '2026-03-20': 'Isra Miraj',
            '2026-03-31': 'Wafat Isa Almasih',
            '2026-04-01': 'Cuti Bersama Wafat Isa',
            '2026-04-10': 'Idulfitri',
            '2026-04-13': 'Cuti Bersama',
            '2026-05-01': 'Hari Buruh',
            '2026-05-14': 'Kenaikan Isa Almasih',
            '2026-05-31': 'Hari Raya Waisak',
            '2026-06-01': 'Hari Lahir Pancasila',
            '2026-06-17': 'Iduladha',
            '2026-08-17': 'HUT RI',
            '2026-12-25': 'Hari Natal',
        },

        // Dokter per hari (dikirim dari PHP)
        doctorSchedules: @json($schedules ?? []),

        get monthLabel() {
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return months[this.current.getMonth()] + ' ' + this.current.getFullYear();
        },

        init() { this.buildCells(); },

        buildCells() {
            const year  = this.current.getFullYear();
            const month = this.current.getMonth();
            const first = new Date(year, month, 1);
            const last  = new Date(year, month + 1, 0);

            // Start from Monday
            let startDay = first.getDay(); // 0=Sun
            startDay = startDay === 0 ? 6 : startDay - 1;

            this.cells = [];

            // Prev month padding
            for (let i = startDay - 1; i >= 0; i--) {
                const d = new Date(year, month, -i);
                this.cells.push(this.makeCell(d, false));
            }

            // Current month
            for (let d = 1; d <= last.getDate(); d++) {
                this.cells.push(this.makeCell(new Date(year, month, d), true));
            }

            // Next month padding
            const remaining = 42 - this.cells.length;
            for (let d = 1; d <= remaining; d++) {
                this.cells.push(this.makeCell(new Date(year, month + 1, d), false));
            }
        },

        makeCell(date, currentMonth) {
            const key = this.dateKey(date);
            const dow = date.getDay(); // 0=Sun
            const isWeekend = dow === 0 || dow === 6;
            const isToday   = this.dateKey(this.today) === key;
            const holiday   = this.holidays[key] || '';

            // Doctors scheduled this day
            const dayNames = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
            const dayName  = dayNames[dow];
            const docs     = (this.doctorSchedules[dayName] || []);

            return { date, day: date.getDate(), key, currentMonth, isWeekend, isToday, holiday, doctors: docs };
        },

        dateKey(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth()+1).padStart(2,'0');
            const dd= String(d.getDate()).padStart(2,'0');
            return `${y}-${m}-${dd}`;
        },

        prevMonth() {
            this.current = new Date(this.current.getFullYear(), this.current.getMonth() - 1, 1);
            this.buildCells();
            this.selectedDay = null;
        },
        nextMonth() {
            this.current = new Date(this.current.getFullYear(), this.current.getMonth() + 1, 1);
            this.buildCells();
            this.selectedDay = null;
        },

        selectDay(cell) {
            this.selectedDay = cell.date;
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            this.selectedLabel  = days[cell.date.getDay()] + ', ' + cell.day + ' ' + months[cell.date.getMonth()] + ' ' + cell.date.getFullYear();
            this.selectedHoliday= cell.holiday;
            this.selectedDoctors= cell.doctors;
        }
    }
}
</script>