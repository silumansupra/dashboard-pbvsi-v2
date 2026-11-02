<!-- GLOBAL LOADING OVERLAY & TOP PROGRESS BAR -->
<div id="global-top-progress" class="global-top-progress" aria-hidden="true"></div>

<div id="global-loading-overlay" class="global-loading-overlay" aria-hidden="true" role="status" aria-live="polite">
    <div class="loading-card shadow-lg">
        <!-- Animated Spinner & Logo PBVSI -->
        <div class="spinner-container">
            <div class="spinner-orbit"></div>
            <div class="spinner-orbit-secondary"></div>
            <div class="logo-wrapper">
                <img src="{{ asset('assets/images/logo_pbvsi_64px.png') }}" alt="PBVSI" class="loading-logo" onerror="this.onerror=null; this.src='https://pbvsi.or.id/wp-content/uploads/2023/07/logo-pbvsi-150x150.png';">
            </div>
        </div>

        <!-- Judul & Keterangan Status -->
        <h6 id="global-loading-title" class="fw-bold text-dark mt-3 mb-1 loading-title">
            Memproses Permintaan...
        </h6>
        <p id="global-loading-desc" class="text-muted small mb-0 loading-desc">
            Mohon tunggu, sistem sedang memproses dan menghubungkan ke server.
        </p>

        <!-- 3 Bouncing Dots -->
        <div class="loading-dots mt-2">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <!-- Safety Button (muncul jika proses > 6 detik) -->
        <div class="mt-3">
            <button type="button" id="global-loading-dismiss" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 loading-dismiss-btn" style="display: none;" onclick="window.hideLoading()">
                <i class="fa-solid fa-xmark me-1"></i> Tutup Loading
            </button>
        </div>
    </div>
</div>

<style>
/* ========================================================== */
/* TOP PROGRESS BAR (YouTube/GitHub Style)                    */
/* ========================================================== */
.global-top-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(90deg, #2563eb, #38bdf8, #f59e0b, #ef4444);
    background-size: 200% 100%;
    box-shadow: 0 0 10px rgba(56, 189, 248, 0.7);
    z-index: 9999999;
    opacity: 0;
    pointer-events: none;
    transition: width 0.25s ease, opacity 0.2s ease;
}
.global-top-progress.active {
    opacity: 1;
    animation: topBarGradient 2s ease infinite;
}
@keyframes topBarGradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* ========================================================== */
/* FULLSCREEN LOADING OVERLAY                                 */
/* ========================================================== */
.global-loading-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999998;
    background-color: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.22s ease-in-out, visibility 0.22s ease-in-out;
}
.global-loading-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: all;
}

/* Modal Card */
.global-loading-overlay .loading-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 28px 36px;
    max-width: 380px;
    width: 100%;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.8);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
    transform: scale(0.92);
    transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.global-loading-overlay.active .loading-card {
    transform: scale(1);
}

/* Spinner Orbit */
.spinner-container {
    position: relative;
    width: 84px;
    height: 84px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.spinner-orbit {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    border: 3.5px solid rgba(37, 99, 235, 0.15);
    border-top-color: #2563eb;
    border-right-color: #38bdf8;
    animation: pbvsiSpin 0.9s linear infinite;
}
.spinner-orbit-secondary {
    position: absolute;
    inset: 7px;
    border-radius: 50%;
    border: 2px dashed rgba(245, 158, 11, 0.35);
    border-bottom-color: #f59e0b;
    animation: pbvsiSpinReverse 1.4s linear infinite;
}
.logo-wrapper {
    position: relative;
    z-index: 2;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.loading-logo {
    width: 44px;
    height: 44px;
    object-fit: contain;
    animation: pbvsiPulse 1.8s ease-in-out infinite;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.12));
}

@keyframes pbvsiSpin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
@keyframes pbvsiSpinReverse {
    0% { transform: rotate(360deg); }
    100% { transform: rotate(0deg); }
}
@keyframes pbvsiPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.08); }
}

/* Text & Info */
.loading-title {
    font-size: 1.05rem;
    letter-spacing: -0.01em;
}
.loading-desc {
    font-size: 0.815rem;
    line-height: 1.45;
}

/* Animated 3 Dots */
.loading-dots {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    height: 14px;
}
.loading-dots span {
    width: 6px;
    height: 6px;
    background-color: #2563eb;
    border-radius: 50%;
    display: inline-block;
    animation: dotBounce 1.2s infinite ease-in-out both;
}
.loading-dots span:nth-child(1) { animation-delay: -0.32s; }
.loading-dots span:nth-child(2) { animation-delay: -0.16s; }
.loading-dots span:nth-child(3) { animation-delay: 0s; }

@keyframes dotBounce {
    0%, 80%, 100% { transform: scale(0.3); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

.loading-dismiss-btn {
    font-size: 0.75rem;
    color: #64748b;
    border-color: #cbd5e1;
}
.loading-dismiss-btn:hover {
    background-color: #f1f5f9;
    color: #334155;
}
</style>

<script>
(function() {
    'use strict';

    // State
    let safetyTimer = null;
    let progressTimer = null;
    let progressValue = 0;
    let activeAjaxCount = 0;
    let ajaxDebounceTimer = null;

    // Elements
    const getOverlay = () => document.getElementById('global-loading-overlay');
    const getTopBar = () => document.getElementById('global-top-progress');
    const getTitle = () => document.getElementById('global-loading-title');
    const getDesc = () => document.getElementById('global-loading-desc');
    const getDismissBtn = () => document.getElementById('global-loading-dismiss');

    // ==========================================================
    // TOP PROGRESS BAR API
    // ==========================================================
    window.startTopProgress = function(startVal = 25) {
        const bar = getTopBar();
        if (!bar) return;

        bar.classList.add('active');
        progressValue = startVal;
        bar.style.width = progressValue + '%';

        if (progressTimer) clearInterval(progressTimer);
        progressTimer = setInterval(() => {
            if (progressValue < 85) {
                // Slower increment as it approaches 85%
                const increment = Math.max(0.5, (85 - progressValue) * 0.1);
                progressValue += increment;
                bar.style.width = progressValue + '%';
            }
        }, 150);
    };

    window.finishTopProgress = function() {
        const bar = getTopBar();
        if (!bar) return;

        if (progressTimer) {
            clearInterval(progressTimer);
            progressTimer = null;
        }

        progressValue = 100;
        bar.style.width = '100%';

        setTimeout(() => {
            bar.classList.remove('active');
            setTimeout(() => {
                bar.style.width = '0%';
                progressValue = 0;
            }, 250);
        }, 200);
    };

    // ==========================================================
    // GLOBAL SHOW / HIDE LOADING API
    // ==========================================================
    window.showLoading = function(title, desc) {
        const overlay = getOverlay();
        if (!overlay) return;

        const titleEl = getTitle();
        const descEl = getDesc();
        const dismissBtn = getDismissBtn();

        if (titleEl && title) titleEl.textContent = title;
        if (descEl && desc) descEl.textContent = desc;
        if (dismissBtn) dismissBtn.style.display = 'none';

        overlay.classList.add('active');
        window.startTopProgress();

        // Safety Net: Munculkan tombol batal/tutup jika lebih dari 6 detik
        if (safetyTimer) clearTimeout(safetyTimer);
        safetyTimer = setTimeout(() => {
            if (overlay.classList.contains('active') && dismissBtn) {
                dismissBtn.style.display = 'inline-block';
            }
        }, 6000);
    };

    window.hideLoading = function() {
        const overlay = getOverlay();
        if (overlay) {
            overlay.classList.remove('active');
        }
        if (safetyTimer) {
            clearTimeout(safetyTimer);
            safetyTimer = null;
        }
        window.finishTopProgress();
    };

    // ==========================================================
    // 1. FORM SUBMIT HANDLER (ALL FORMS)
    // ==========================================================
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || form.tagName !== 'FORM') return;

        // Jangan intervensi form yang dikecualikan
        if (form.hasAttribute('data-no-loading') || form.getAttribute('target') === '_blank') {
            return;
        }

        // Jika validasi HTML5 bawaan gagal, jangan tampilkan overlay
        if (form.checkValidity && !form.checkValidity()) {
            return;
        }

        let title = 'Memproses Formulir...';
        let desc = 'Mohon tunggu, sistem sedang menyimpan data.';

        // Cek apakah aksi Delete
        const methodInput = form.querySelector('input[name="_method"]');
        const isDelete = (methodInput && methodInput.value.toUpperCase() === 'DELETE') ||
                         (form.action && form.action.includes('destroy'));

        // Cek apakah ada upload file
        const fileInputs = form.querySelectorAll('input[type="file"]');
        let hasFiles = false;
        fileInputs.forEach(fi => {
            if (fi.files && fi.files.length > 0) hasFiles = true;
        });

        if (isDelete) {
            title = 'Menghapus Data...';
            desc = 'Sedang menghapus data dari sistem PBVSI.';
        } else if (hasFiles) {
            title = 'Mengunggah Berkas...';
            desc = 'Sedang mengunggah berkas ke storage cloud BunnyCDN. Mohon tidak menutup halaman.';
        } else if (form.id && form.id.toLowerCase().includes('edit')) {
            title = 'Menyimpan Perubahan...';
            desc = 'Sedang memperbarui data ke sistem PBVSI.';
        } else if (form.id && form.id.toLowerCase().includes('tambah')) {
            title = 'Mendaftarkan Data Baru...';
            desc = 'Sedang menyimpan rekaman baru ke database.';
        } else if (form.querySelector('input[name="search"]') || form.querySelector('select[name="pengprov_id"]')) {
            title = 'Memfilter Data...';
            desc = 'Sedang mengambil data sesuai kriteria filter.';
        }

        window.showLoading(title, desc);
    }, true);

    // ==========================================================
    // 2. PAGE NAVIGATION / LINK CLICK HANDLER
    // ==========================================================
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;

        // Skip jika tombol modifier ditekan (buka tab baru)
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

        // Skip jika download atau target _blank
        if (link.hasAttribute('download') || link.getAttribute('target') === '_blank') return;

        // Skip jika ada tanda data-no-loading
        if (link.hasAttribute('data-no-loading')) return;

        // Skip jika trigger modal Bootstrap / tab / collapse / dropdown
        if (link.hasAttribute('data-bs-toggle') || link.hasAttribute('data-toggle') ||
            link.hasAttribute('data-bs-target') || link.hasAttribute('data-target')) {
            return;
        }

        const href = link.getAttribute('href');
        if (!href) return;

        const trimmed = href.trim();
        if (
            trimmed === '' ||
            trimmed === '#' ||
            trimmed.startsWith('#') ||
            trimmed.startsWith('javascript:') ||
            trimmed.startsWith('mailto:') ||
            trimmed.startsWith('tel:')
        ) {
            return;
        }

        // Cek jika link hanya mengarah ke bookmark hash pada halaman yang sama
        try {
            const targetUrl = new URL(link.href, window.location.href);
            const currentUrl = new URL(window.location.href);

            if (
                targetUrl.origin === currentUrl.origin &&
                targetUrl.pathname === currentUrl.pathname &&
                targetUrl.search === currentUrl.search &&
                targetUrl.hash !== ''
            ) {
                return;
            }
        } catch (err) {}

        window.showLoading('Memuat Halaman...', 'Menyiapkan tampilan untuk Anda...');
    }, true);

    // ==========================================================
    // 3. AJAX & FETCH INTERCEPTION (AUTOMATIC)
    // ==========================================================
    function onAjaxStart(title, desc) {
        activeAjaxCount++;
        if (activeAjaxCount === 1) {
            window.startTopProgress(30);

            // Tampilkan full card jika AJAX memakan waktu > 120ms
            if (ajaxDebounceTimer) clearTimeout(ajaxDebounceTimer);
            ajaxDebounceTimer = setTimeout(() => {
                if (activeAjaxCount > 0) {
                    window.showLoading(title || 'Memproses Data...', desc || 'Sedang berkomunikasi dengan server...');
                }
            }, 120);
        }
    }

    function onAjaxEnd() {
        activeAjaxCount = Math.max(0, activeAjaxCount - 1);
        if (activeAjaxCount === 0) {
            if (ajaxDebounceTimer) {
                clearTimeout(ajaxDebounceTimer);
                ajaxDebounceTimer = null;
            }
            window.finishTopProgress();
            window.hideLoading();
        }
    }

    // Intercept window.fetch
    const originalFetch = window.fetch;
    if (typeof originalFetch === 'function') {
        window.fetch = function() {
            const args = arguments;
            const isSilent = (args[1] && args[1].headers && args[1].headers['X-Silent-Loading']);

            if (!isSilent) {
                onAjaxStart('Memproses Data...', 'Sedang mengambil data dari server...');
            }

            return originalFetch.apply(this, args)
                .then(res => {
                    if (!isSilent) onAjaxEnd();
                    return res;
                })
                .catch(err => {
                    if (!isSilent) onAjaxEnd();
                    throw err;
                });
        };
    }

    // Intercept XMLHttpRequest
    const originalXHR = window.XMLHttpRequest;
    if (typeof originalXHR === 'function') {
        const originalOpen = originalXHR.prototype.open;
        const originalSend = originalXHR.prototype.send;

        originalXHR.prototype.open = function() {
            this._loadingTracked = true;
            return originalOpen.apply(this, arguments);
        };

        originalXHR.prototype.send = function() {
            if (this._loadingTracked) {
                onAjaxStart('Memproses Data...', 'Sedang berkomunikasi dengan server...');
                const handleDone = () => {
                    if (this._loadingTracked) {
                        this._loadingTracked = false;
                        onAjaxEnd();
                    }
                };
                this.addEventListener('loadend', handleDone, { once: true });
                this.addEventListener('error', handleDone, { once: true });
                this.addEventListener('abort', handleDone, { once: true });
                this.addEventListener('timeout', handleDone, { once: true });
            }
            return originalSend.apply(this, arguments);
        };
    }

    // ==========================================================
    // 4. BROWSER BFCACHE & LIFECYCLE (BACK/FORWARD RESTORE)
    // ==========================================================
    window.addEventListener('pageshow', function(event) {
        window.hideLoading();
    });

    window.addEventListener('DOMContentLoaded', function() {
        window.hideLoading();
    });

    window.addEventListener('beforeunload', function() {
        window.startTopProgress(90);
    });

})();
</script>
