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
    const closeBtn = document.getElementById('sidebarCloseBtn');
    const sidebar = document.querySelector('.app-sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');

    function openSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.add('show');
            backdrop.classList.add('show');
            document.body.classList.add('sidebar-open');
        }
    }

    function closeSidebar() {
        if (sidebar && backdrop) {
            sidebar.classList.remove('show');
            backdrop.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }
    }

    if (toggleBtn && sidebar && backdrop) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (sidebar.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                closeSidebar();
            });
        }

        backdrop.addEventListener('click', closeSidebar);

        // Tutup drawer otomatis ketika salah satu nav-item diklik (pada mobile/tablet)
        const navItems = sidebar.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    closeSidebar();
                }
            });
        });

        // Tutup drawer jika tombol ESC ditekan
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar.classList.contains('show')) {
                closeSidebar();
            }
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

        const studentSearchFilter = document.getElementById('studentSearchFilter');
        if (studentSearchFilter) {
            studentSearchFilter.addEventListener('input', function() {
                const query = this.value.toLowerCase().trim();
                const options = studentSelect.options;
                let firstMatch = null;

                for (let i = 1; i < options.length; i++) {
                    const opt = options[i];
                    const text = opt.text.toLowerCase();
                    if (query === '' || text.includes(query)) {
                        opt.hidden = false;
                        opt.disabled = false;
                        if (!firstMatch) firstMatch = opt;
                    } else {
                        opt.hidden = true;
                        opt.disabled = true;
                    }
                }

                if (query !== '' && firstMatch) {
                    studentSelect.value = firstMatch.value;
                    updateStudentNotice();
                }
            });
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
    // 8. Executive Glass Alert System Helpers & Auto-Dismiss
    // -------------------------------------------------------------
    window.dismissAlert = function(alertEl) {
        if (!alertEl || alertEl.classList.contains('alert-dismissing')) return;
        alertEl.classList.add('alert-dismissing');
        setTimeout(() => {
            if (alertEl.parentNode) {
                alertEl.parentNode.removeChild(alertEl);
            }
        }, 360);
    };

    function setupAlertAutoDismiss(alertEl) {
        if (!alertEl || alertEl.dataset.initializedAutoDismiss) return;
        alertEl.dataset.initializedAutoDismiss = 'true';

        const timeoutMs = parseInt(alertEl.getAttribute('data-auto-dismiss') || '0', 10);
        if (timeoutMs <= 0) return;

        // Set CSS duration for the progress bar animation
        const progressFill = alertEl.querySelector('.alert-progress-fill');
        if (progressFill) {
            progressFill.style.animationDuration = `${timeoutMs}ms`;
        }

        let remaining = timeoutMs;
        let startTime = Date.now();
        let timer = null;

        function startTimer() {
            startTime = Date.now();
            timer = setTimeout(() => {
                window.dismissAlert(alertEl);
            }, remaining);
        }

        function pauseTimer() {
            if (timer) {
                clearTimeout(timer);
                timer = null;
                remaining -= Date.now() - startTime;
                if (remaining < 100) remaining = 100;
            }
        }

        alertEl.addEventListener('mouseenter', pauseTimer);
        alertEl.addEventListener('mouseleave', () => {
            if (!alertEl.classList.contains('alert-dismissing')) {
                startTimer();
            }
        });

        startTimer();
    }

    // Initialize all existing alerts on page load
    document.querySelectorAll('.alert[data-auto-dismiss]').forEach(setupAlertAutoDismiss);

    // Global Client-Side Alert / Toast Dispatcher
    window.showAlert = function(type = 'success', message = '', title = '') {
        let container = document.getElementById('flash-container');
        if (!container) {
            const mainContent = document.querySelector('.app-content') || document.querySelector('.portal-container-shell') || document.querySelector('.login-body') || document.body;
            container = document.createElement('div');
            container.id = 'flash-container';
            container.className = 'flash-alert-container';
            if (mainContent.firstChild) {
                mainContent.insertBefore(container, mainContent.firstChild);
            } else {
                mainContent.appendChild(container);
            }
        }

        const validTypes = {
            success: {
                badge: 'BERHASIL',
                defaultTitle: 'Aksi Berhasil Dilakukan',
                duration: 6000,
                icon: '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
            },
            danger: {
                badge: 'PERHATIAN',
                defaultTitle: 'Terjadi Kesalahan',
                duration: 9000,
                icon: '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>'
            },
            warning: {
                badge: 'PERINGATAN',
                defaultTitle: 'Pemberitahuan Penting',
                duration: 8000,
                icon: '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>'
            },
            info: {
                badge: 'INFORMASI',
                defaultTitle: 'Informasi Sistem',
                duration: 6000,
                icon: '<svg class="alert-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
            }
        };

        const cfg = validTypes[type] || validTypes.info;
        const finalTitle = title || cfg.defaultTitle;

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible`;
        alertDiv.setAttribute('role', 'alert');
        alertDiv.setAttribute('data-auto-dismiss', cfg.duration);

        alertDiv.innerHTML = `
            <div class="alert-icon-wrapper">
                ${cfg.icon}
            </div>
            <div class="alert-content">
                <div class="alert-header">
                    <span class="alert-badge">${cfg.badge}</span>
                    <strong class="alert-title">${finalTitle}</strong>
                </div>
                <div class="alert-message">${message}</div>
            </div>
            <button type="button" class="alert-close" aria-label="Tutup notifikasi">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
            <div class="alert-progress-bar"><div class="alert-progress-fill"></div></div>
        `;

        alertDiv.querySelector('.alert-close').addEventListener('click', () => {
            window.dismissAlert(alertDiv);
        });

        container.appendChild(alertDiv);
        setupAlertAutoDismiss(alertDiv);
    };

    // -------------------------------------------------------------
    // 9. Mobile Bottom Navigation Active State Highlighter
    // -------------------------------------------------------------
    const currentPath = window.location.pathname;
    const dockItems = document.querySelectorAll('.dock-item');

    dockItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href) {
            const cleanHref = href.replace(/\/$/, '');
            if (cleanHref && currentPath.includes(cleanHref)) {
                dockItems.forEach(d => d.classList.remove('active'));
                item.classList.add('active');
            }
        }
    });

    // -------------------------------------------------------------
    // 10. Instant Live Filter for Data Tables
    // -------------------------------------------------------------
    const tableSearchInput = document.querySelector('.filter-bar input[name="q"]');
    const dataTableBody = document.querySelector('.data-table tbody');
    if (tableSearchInput && dataTableBody) {
        tableSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const rows = dataTableBody.querySelectorAll('tr');
            if (!rows.length) return;

            rows.forEach(row => {
                if (row.children.length <= 1) return;
                const text = row.innerText.toLowerCase();
                row.style.display = (query === '' || text.includes(query)) ? '' : 'none';
            });
        });
    }

    // -------------------------------------------------------------
    // 11. Auto Print Trigger on query ?print=1
    // -------------------------------------------------------------
    if (window.location.search.includes('print=1')) {
        window.print();
    }

    // -------------------------------------------------------------
    // 12. Universal Password Visibility Toggle (Inside Input Field)
    // -------------------------------------------------------------
    document.querySelectorAll('.password-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const container = btn.closest('.password-field-container') || btn.parentElement;
            const input = container ? container.querySelector('input') : null;
            if (!input) return;

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            const eyeOpen = btn.querySelector('.eye-icon-open') || btn.querySelector('#eyeIconOpen');
            const eyeClose = btn.querySelector('.eye-icon-close') || btn.querySelector('#eyeIconClose');
            if (eyeOpen && eyeClose) {
                eyeOpen.style.display = isPassword ? 'none' : 'block';
                eyeClose.style.display = isPassword ? 'block' : 'none';
            }
            btn.style.color = isPassword ? '#dfb256' : '#94a3b8';
            btn.setAttribute('title', isPassword ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');
            input.focus();
        });
    });

    // -------------------------------------------------------------
    // 13. Global Logout Confirmation Interceptor
    // -------------------------------------------------------------
    document.querySelectorAll('a[href*="/logout"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Apakah Anda yakin ingin keluar dari sistem Guardian of Navasena 27?')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });
});
