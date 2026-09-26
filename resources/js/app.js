/**
 * Perilaku global yang dipakai seluruh aplikasi.
 *
 * MOTION 1 dari DESIGN.md: tidak ada animasi dekoratif di sini, hanya state yang
 * berubah karena ada pemicu nyata. Semua elemen yang dikendalikan ada di DOM
 * sejak awal, jadi matinya dan hidupnya bukan soal animasi.
 */

/* ------------------------------------------------------------------ */
/* Tema terang dan gelap                                              */
/* ------------------------------------------------------------------ */

const KUNCI_TEMA = 'tema-perpustakaan';

function terapkanTema(tema) {
    document.documentElement.classList.toggle('dark', tema === 'gelap');
    document.documentElement.dataset.tema = tema;

    try {
        localStorage.setItem(KUNCI_TEMA, tema);
    } catch (error) {
        // Mode privat memblokir penyimpanan. Pilihan tema hanya berlaku untuk
        // sesi ini, itu wajar dan tidak perlu mengganggu pengguna.
    }

    document.querySelectorAll('[data-theme-toggle]').forEach((tombol) => {
        tombol.setAttribute(
            'aria-label',
            tema === 'gelap' ? 'Aktifkan mode terang' : 'Aktifkan mode gelap',
        );
    });
}

document.addEventListener('click', (acara) => {
    const tombol = acara.target.closest('[data-theme-toggle]');

    if (!tombol) {
        return;
    }

    const sekarangGelap = document.documentElement.classList.contains('dark');
    terapkanTema(sekarangGelap ? 'terang' : 'gelap');
});

/* ------------------------------------------------------------------ */
/* Sidebar: dilipat di layar lebar, jadi laci di layar sempit          */
/* ------------------------------------------------------------------ */

const KUNCI_SIDEBAR = 'sidebar-perpustakaan';
const akar = document.documentElement;
const pemicuSidebar = document.querySelectorAll('[data-sidebar-toggle]');

const layarSempit = () => window.matchMedia('(max-width: 1023px)').matches;

function sidebarRingkas() {
    return akar.classList.contains('sidebar-ringkas');
}

function laciTerbuka() {
    return akar.classList.contains('sidebar-terbuka');
}

/* Tombol di topbar dan di sidebar ditulis ke satu sumber yang sama, yaitu
   kelas pada elemen <html>, supaya keduanya selalu menampilkan keadaan yang
   sama tanpa harus saling mencari tahu. */
function perbaruiStatusSidebar() {
    const terbuka = !sidebarRingkas() || laciTerbuka();

    pemicuSidebar.forEach((tombol) => {
        tombol.setAttribute('aria-expanded', terbuka ? 'true' : 'false');
    });
}

function setSidebarRingkas(ringkas) {
    akar.classList.toggle('sidebar-ringkas', ringkas);

    if (layarSempit()) {
        return;
    }

    try {
        localStorage.setItem(KUNCI_SIDEBAR, ringkas ? 'ringkas' : 'lebar');
    } catch (error) {
        // Mode privat memblokir penyimpanan. Ukuran sidebar hanya berlaku untuk
        // sesi ini, itu wajar dan tidak perlu mengganggu pengguna.
    }
}

function setLaciTerbuka(terbuka) {
    akar.classList.toggle('sidebar-terbuka', terbuka);
    perbaruiStatusSidebar();

    if (terbuka) {
        document.querySelector('[data-sidebar-sembunyi]')?.focus();
    }
}

pemicuSidebar.forEach((tombol) => {
    tombol.addEventListener('click', () => {
        if (layarSempit()) {
            setLaciTerbuka(!laciTerbuka());

            return;
        }

        setSidebarRingkas(!sidebarRingkas());
        perbaruiStatusSidebar();
    });
});

document.querySelector('[data-sidebar-sembunyi]')?.addEventListener('click', () => {
    setLaciTerbuka(false);
    document.querySelector('[data-sidebar-toggle]')?.focus();
});

/* Menekan area gelap di luar laci harus menutupnya, dan memilih tautan di
   dalam laci juga menutupnya supaya tidak menutupi halaman tujuan. */
document.querySelector('[data-sidebar-lapis]')?.addEventListener('click', () => {
    setLaciTerbuka(false);
});

document.querySelector('[data-sidebar]')?.addEventListener('click', (acara) => {
    if (layarSempit() && acara.target.closest('a[href]')) {
        setLaciTerbuka(false);
    }
});

document.addEventListener('keydown', (acara) => {
    if (acara.key === 'Escape' && laciTerbuka()) {
        setLaciTerbuka(false);
        document.querySelector('[data-sidebar-toggle]')?.focus();
    }
});

/* Menambah lebar layar berarti laci tidak lagi dipakai, jadi state laci
   dibuang agar tidak memengaruhi tampilan berikutnya. */
window.addEventListener('resize', () => {
    if (!layarSempit()) {
        akar.classList.remove('sidebar-terbuka');
    }

    perbaruiStatusSidebar();
});

perbaruiStatusSidebar();

/* ------------------------------------------------------------------ */
/* Pemberitahuan yang bisa ditutup                                    */
/* ------------------------------------------------------------------ */

document.addEventListener('click', (acara) => {
    const tombol = acara.target.closest('[data-tutup-notifikasi]');

    if (tombol) {
        tombol.closest('[data-notifikasi]')?.remove();
    }
});

/* ------------------------------------------------------------------ */
/* Indikator memuat saat form dikirim                                  */
/* ------------------------------------------------------------------ */

const indikator = document.querySelector('[data-indikator-muat]');
let layarBerhenti = false;

/* Hanya formulir yang mengubah data yang perlu indikator. Formulir pencarian
   GET selesai seketika dan tidak perlu membuat tombolnya berkedip. */
function perluIndikator(form) {
    return form instanceof HTMLFormElement && form.method.toLowerCase() === 'post';
}

function tombolKirim(form) {
    return form.querySelectorAll('button[type="submit"], button:not([type])');
}

function nyalakanIndikator() {
    if (!indikator || layarBerhenti) {
        return;
    }

    indikator.hidden = false;
    document.body.setAttribute('aria-busy', 'true');

    document.querySelectorAll('form').forEach((form) => {
        if (!perluIndikator(form)) {
            return;
        }

        tombolKirim(form).forEach((tombol) => {
            if (!tombol.dataset.teksAwal) {
                tombol.dataset.teksAwal = tombol.innerHTML;
            }

            tombol.disabled = true;
            tombol.insertAdjacentHTML('afterbegin', '<span data-spinner class="inline-block h-3 w-3 shrink-0 rounded-full border-2 border-current border-t-transparent align-[-1px] motion-safe:animate-spin"></span>');
        });
    });
}

function matikanIndikator() {
    if (!indikator) {
        return;
    }

    indikator.hidden = true;
    document.body.removeAttribute('aria-busy');

    document.querySelectorAll('form').forEach((form) => {
        if (!perluIndikator(form)) {
            return;
        }

        tombolKirim(form).forEach((tombol) => {
            tombol.disabled = false;
            tombol.querySelector('[data-spinner]')?.remove();
        });
    });
}

document.addEventListener('submit', (acara) => {
    const form = acara.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    // Form yang isiannya belum valid dihentikan di sisi peramban supaya pesan
    // kesalahannya muncul tepat di kolom yang bermasalah.
    if (!perluIndikator(form) || (form.checkValidity && !form.checkValidity())) {
        return;
    }

    nyalakanIndikator();
});

window.addEventListener('pageshow', () => {
    layarBerhenti = false;
    matikanIndikator();
});

window.addEventListener('beforeunload', () => {
    layarBerhenti = true;
});

/* ------------------------------------------------------------------ */
/* Konfirmasi sebelum tindakan yang tidak bisa dibatalkan              */
/* ------------------------------------------------------------------ */

document.addEventListener('submit', (acara) => {
    const form = acara.target;
    const pesan = form?.dataset?.konfirmasi;

    if (pesan && !window.confirm(pesan)) {
        acara.preventDefault();
        layarBerhenti = true;
        matikanIndikator();
    }
});

/* ------------------------------------------------------------------ */
/* Pencarian yang submitting otomatis, dengan jeda mengetik            */
/* ------------------------------------------------------------------ */

let jedaPencarian = null;

document.addEventListener('input', (acara) => {
    const input = acara.target;

    if (!(input instanceof HTMLInputElement) || !input.dataset.cariOtomatis) {
        return;
    }

    const form = input.closest('form');

    if (!form) {
        return;
    }

    window.clearTimeout(jedaPencarian);
    jedaPencarian = window.setTimeout(() => form.requestSubmit(), 450);
});

/* ------------------------------------------------------------------ */
/* Kirim ulang form pencarian saat tombol filter diklik                */
/* ------------------------------------------------------------------ */

document.querySelectorAll('[data-form-induk]').forEach((tombol) => {
    tombol.addEventListener('click', () => {
        const form = document.getElementById(tombol.dataset.formInduk);

        if (form) {
            form.requestSubmit();
        }
    });
});
