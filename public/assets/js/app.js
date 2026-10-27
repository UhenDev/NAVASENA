/**
 * GUARDIAN OF NAVASENA 27 — SMKN 1 PANYINGKIRAN
 * Executive Client-Side Interactive Engine
 * Pure Vanilla ES6 JavaScript (Zero Dependencies)
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // 1. Mobile Sidebar & Backdrop Drawer
    // -------------------------------------------------------------
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

    // -------------------------------------------------------------
    // 2. Format Rupiah Input Helper
    // -------------------------------------------------------------
    const rupiahInputs = document.querySelectorAll('.input-rupiah');
    
    function formatNumberToRupiah(numStr) {
        let clean = (numStr || '').toString().replace(/[^0-9]/g, '');
        if (!clean) return '';
        return new Intl.NumberFormat('id-ID').format(clean);
    }

    rupiahInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            e.target.value = formatNumberToRupiah(e.target.value);
        });
    });

    // -------------------------------------------------------------
    // 3. Quick-fill Nominal Chips (Bendahara Payment)
    // -------------------------------------------------------------
    const quickChips = document.querySelectorAll('.chip-btn');
    const amountInput = document.getElementById('paymentAmount');

    quickChips.forEach(chip => {
        chip.addEventListener('click', (e) => {
            e.preventDefault();
            const val = chip.getAttribute('data-value');
            if (val && amountInput) {
                amountInput.value = formatNumberToRupiah(val);
                amountInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    });

    // -------------------------------------------------------------
    // 4. Interactive Student Selector with Dynamic Balance & Chip Update
    // -------------------------------------------------------------
    const studentSelect = document.getElementById('studentSelect');
    const remainingNotice = document.getElementById('studentRemainingNotice');
    const lunasChip = document.getElementById('chipPayFull');

    function updateStudentNotice() {
        if (!studentSelect || !remainingNotice) return;
        const selectedOpt = studentSelect.options[studentSelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            remainingNotice.textContent = 'Silakan pilih siswa untuk melihat sisa tagihan.';
            remainingNotice.style.color = 'var(--text-muted)';
            return;
        }

        const remaining = parseFloat(selectedOpt.dataset.remaining || 0);
        const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(remaining);

        if (remaining <= 0) {
            remainingNotice.textContent = `Sisa Tagihan Siswa: ${formatted} — (LUNAS PENUH)`;
            remainingNotice.style.color = 'var(--verified-fg)';
        } else {
            remainingNotice.textContent = `Sisa Tagihan Siswa: ${formatted}`;
            remainingNotice.style.color = 'var(--gold-solid)';
        }

        // Update nominal chip "Pelunasan Sisa"
        if (lunasChip) {
            lunasChip.setAttribute('data-value', Math.max(0, remaining));
            lunasChip.textContent = `Rp ${formatNumberToRupiah(remaining)} (Lunas)`;
        }
    }

    if (studentSelect) {
        studentSelect.addEventListener('change', updateStudentNotice);
        // Initial trigger if student already pre-selected
        if (studentSelect.value) {
            updateStudentNotice();
        }
    }

    // -------------------------------------------------------------
    // 5. Payment Method Visual Toggle Sync
    // -------------------------------------------------------------
    const methodToggleBtns = document.querySelectorAll('.method-toggle-btn');
    const methodSelect = document.getElementById('payment_method');

    if (methodToggleBtns.length && methodSelect) {
        methodToggleBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedMethod = btn.getAttribute('data-method');
                if (selectedMethod) {
                    methodSelect.value = selectedMethod;
                    methodToggleBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                }
            });
        });
    }

    // -------------------------------------------------------------
    // 6. Tactile Mobile Numeric Glass Keypad
    // -------------------------------------------------------------
    const keypadButtons = document.querySelectorAll('.keypad-btn');
    if (keypadButtons.length && amountInput) {
        keypadButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const key = btn.getAttribute('data-key');
                let currentVal = (amountInput.value || '').replace(/[^0-9]/g, '');

                if (key === 'clear') {
                    currentVal = '';
                } else if (key === 'backspace') {
                    currentVal = currentVal.slice(0, -1);
                } else if (key === '000') {
                    if (currentVal && currentVal !== '0') {
                        currentVal += '000';
                    }
                } else {
                    currentVal += key;
                }

                amountInput.value = formatNumberToRupiah(currentVal);
                amountInput.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    }

    // -------------------------------------------------------------
    // 7. Modal Dialog Helpers (Reject, Cancel / Reversal)
    // -------------------------------------------------------------
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

    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    });

    // -------------------------------------------------------------
    // 8. Mobile Bottom Navigation Active State Highlighter
    // -------------------------------------------------------------
    const currentPath = window.location.pathname;
    const dockItems = document.querySelectorAll('.dock-item');

    dockItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href.replace(/\/$/, ''))) {
            dockItems.forEach(d => d.classList.remove('active'));
            item.classList.add('active');
        }
    });

    // -------------------------------------------------------------
    // 9. Auto Print Trigger on query ?print=1
    // -------------------------------------------------------------
    if (window.location.search.includes('print=1')) {
        window.print();
    }
});
