/**
 * NAVASENA MANAGEMENT — Client-side Scripts
 * Vanilla JavaScript (No Frameworks, No Dependencies)
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Sidebar Drawer Toggle
    const toggleBtn = document.getElementById('menuToggleBtn');
    const sidebar = document.querySelector('.app-sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    if (toggleBtn && sidebar && backdrop) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop.classList.toggle('show');
        });

        backdrop.addEventListener('click', () => {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
        });
    }

    // 2. Format Rupiah Input Helper
    const rupiahInputs = document.querySelectorAll('.input-rupiah');
    rupiahInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            let val = e.target.value.replace(/[^0-9]/g, '');
            if (val) {
                e.target.value = new Intl.NumberFormat('id-ID').format(val);
            } else {
                e.target.value = '';
            }
        });
    });

    // 3. Modal Dialog Helper (Rejection, Cancellation)
    window.openModal = function(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        if (options.formAction && modal.querySelector('form')) {
            modal.querySelector('form').action = options.formAction;
        }

        if (options.targetName && modal.querySelector('.modal-target-name')) {
            modal.querySelector('.modal-target-name').textContent = options.targetName;
        }

        modal.classList.add('active');
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    };

    // Close modal on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    // 4. Payment Creation Interactive Remaining & Allocation Calculation
    const studentSelect = document.getElementById('studentSelect');
    const amountInput = document.getElementById('paymentAmount');
    const remainingNotice = document.getElementById('studentRemainingNotice');
    const autoAllocSummary = document.getElementById('autoAllocSummary');

    if (studentSelect && remainingNotice) {
        studentSelect.addEventListener('change', () => {
            const selectedOpt = studentSelect.options[studentSelect.selectedIndex];
            const remaining = parseFloat(selectedOpt.dataset.remaining || 0);
            const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(remaining);
            
            remainingNotice.textContent = `Sisa Tagihan Siswa: ${formatted}`;
            if (remaining <= 0) {
                remainingNotice.className = 'form-text text-success font-weight-bold';
                remainingNotice.textContent += ' (LUNAS)';
            } else {
                remainingNotice.className = 'form-text text-brand';
            }
        });
    }

    // 5. Auto print trigger if query param ?print=1 is present
    if (window.location.search.includes('print=1')) {
        window.print();
    }
});
