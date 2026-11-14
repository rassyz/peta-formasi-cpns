/*
 * Toolkit Berkas — 100% client-side (PRD FR-D1 & FR-D2).
 * File pengguna diproses di browser dan TIDAK pernah dikirim ke server.
 * PDF dikompres dengan merasterisasi halaman (pdf.js) lalu menyusun ulang
 * sebagai PDF berisi gambar JPEG (pdf-lib) — cocok untuk berkas hasil pindai.
 * Foto dikompres/diubah ukurannya lewat canvas.
 *
 * Revisi Tahap 2.1: worker pdf.js dimuat sebagai worker terbundel Vite
 * (?worker + workerPort), bukan URL file .mjs terpisah — menghindari kegagalan
 * muat worker karena tipe MIME .mjs di server. Pesan error kini menyertakan
 * detail teknis agar kegagalan berikutnya bisa didiagnosis dari UI langsung.
 */
import * as pdfjsLib from "pdfjs-dist";
import PdfWorker from "pdfjs-dist/build/pdf.worker.min.mjs?worker";
import { PDFDocument } from "pdf-lib";

pdfjsLib.GlobalWorkerOptions.workerPort = new PdfWorker();

/* ---------------- Helper umum ---------------- */

const $ = (id) => document.getElementById(id);

const formatUkuran = (bytes) => {
    if (bytes >= 1024 * 1024) {
        return `${(bytes / (1024 * 1024)).toLocaleString("id-ID", { maximumFractionDigits: 2 })} MB`;
    }
    return `${Math.round(bytes / 1024).toLocaleString("id-ID")} KB`;
};

const canvasToBlob = (canvas, tipe, kualitas) =>
    new Promise((resolve, reject) => {
        canvas.toBlob(
            (blob) =>
                blob
                    ? resolve(blob)
                    : reject(new Error("Gagal membuat hasil gambar.")),
            tipe,
            kualitas,
        );
    });

const unduhBlob = (anchor, blob, namaFile) => {
    if (anchor.dataset.url) {
        URL.revokeObjectURL(anchor.dataset.url);
    }
    const url = URL.createObjectURL(blob);
    anchor.dataset.url = url;
    anchor.href = url;
    anchor.download = namaFile;
};

const namaHasil = (file, suffix, ext) => {
    const dasar = file.name.replace(/\.[^.]+$/, "");
    return `${dasar}-${suffix}.${ext}`;
};

const detailError = (err) =>
    err && err.message ? ` Detail: ${err.message}` : "";

/** Pemilih preset chip: satu aktif, nilai dalam KB. */
function buatPemilihPreset(tombolSelector, nilaiAwal, onChange) {
    const tombol = [...document.querySelectorAll(tombolSelector)];
    let nilai = nilaiAwal;

    const tandai = () => {
        tombol.forEach((btn) => {
            const aktif =
                Number(btn.dataset.presetPdf ?? btn.dataset.presetFoto) ===
                nilai;
            btn.classList.toggle("bg-ink", aktif);
            btn.classList.toggle("text-white", aktif);
            btn.classList.toggle("border-ink", aktif);
        });
    };

    tombol.forEach((btn) => {
        btn.addEventListener("click", () => {
            nilai = Number(btn.dataset.presetPdf ?? btn.dataset.presetFoto);
            tandai();
            onChange?.(nilai);
        });
    });

    tandai();

    return {
        get nilai() {
            return nilai;
        },
        set(nilaiBaru) {
            nilai = nilaiBaru;
            tandai();
            onChange?.(nilai);
        },
    };
}

/** Pasang drag & drop + info file pada label/input. */
function pasangPemilihFile(input, label, infoEl, onFile) {
    const tampilkan = (file) => {
        infoEl.textContent = file
            ? `${file.name} · ${formatUkuran(file.size)}`
            : "Belum ada file dipilih";
        onFile(file ?? null);
    };

    input.addEventListener("change", () => tampilkan(input.files?.[0] ?? null));

    label.addEventListener("dragover", (e) => {
        e.preventDefault();
        label.classList.add("bg-sunken");
    });
    label.addEventListener("dragleave", () =>
        label.classList.remove("bg-sunken"),
    );
    label.addEventListener("drop", (e) => {
        e.preventDefault();
        label.classList.remove("bg-sunken");
        const file = e.dataTransfer?.files?.[0] ?? null;
        if (file) {
            tampilkan(file);
        }
    });
}

function tampilkanError(el, pesan) {
    if (!pesan) {
        el.classList.add("hidden");
        el.textContent = "";
        return;
    }
    el.textContent = pesan;
    el.classList.remove("hidden");
}

/* ---------------- Preset dari URL (tautan dari checklist, FR-D3) ---------------- */

const params = new URLSearchParams(window.location.search);
const targetUrl = Number(params.get("target"));
const targetSah = [200, 500, 1024].includes(targetUrl) ? targetUrl : null;

/* ---------------- Alat PDF ---------------- */

let filePdf = null;

const pemilihPdf = buatPemilihPreset(".preset-pdf", targetSah ?? 500, () => {
    $("pdf-custom").value = "";
});

$("pdf-custom").addEventListener("input", (e) => {
    const v = Number(e.target.value);
    if (v > 0) {
        pemilihPdf.set(-1); // lepas chip aktif; nilai diambil dari input saat proses
    }
});

const targetPdfKb = () => {
    const custom = Number($("pdf-custom").value);
    return custom > 0 ? custom : pemilihPdf.nilai;
};

pasangPemilihFile(
    $("pdf-file"),
    document.querySelector('label[for="pdf-file"]'),
    $("pdf-file-info"),
    (file) => {
        filePdf = file;
        $("pdf-process").disabled = !file;
        tampilkanError($("pdf-error"), null);
        $("pdf-result").classList.add("hidden");
    },
);

const TINGKAT_PDF = [
    { skala: 2.0, kualitas: 0.8, label: "tinggi (±150 dpi)" },
    { skala: 1.66, kualitas: 0.75, label: "sedang (±125 dpi)" },
    { skala: 1.33, kualitas: 0.7, label: "hemat (±100 dpi)" },
    { skala: 1.0, kualitas: 0.65, label: "hemat sekali (±72 dpi)" },
    { skala: 0.83, kualitas: 0.55, label: "minimum (±60 dpi)" },
];

async function bangunPdfRaster(file, tingkat, onProgress) {
    // Uint8Array segar tiap tangga kualitas (buffer ditransfer ke pdf.js).
    const data = new Uint8Array(await file.arrayBuffer());
    const task = pdfjsLib.getDocument({ data });
    const sumber = await task.promise;
    const hasil = await PDFDocument.create();

    for (let i = 1; i <= sumber.numPages; i++) {
        const halaman = await sumber.getPage(i);
        const viewportAsli = halaman.getViewport({ scale: 1 });
        const viewport = halaman.getViewport({ scale: tingkat.skala });

        const canvas = document.createElement("canvas");
        canvas.width = Math.ceil(viewport.width);
        canvas.height = Math.ceil(viewport.height);
        const ctx = canvas.getContext("2d");
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        await halaman.render({ canvas, canvasContext: ctx, viewport }).promise;

        const jpg = await canvasToBlob(canvas, "image/jpeg", tingkat.kualitas);
        const gambar = await hasil.embedJpg(await jpg.arrayBuffer());
        const lembar = hasil.addPage([viewportAsli.width, viewportAsli.height]);
        lembar.drawImage(gambar, {
            x: 0,
            y: 0,
            width: viewportAsli.width,
            height: viewportAsli.height,
        });

        onProgress(i, sumber.numPages, tingkat.label);
    }

    const bytes = await hasil.save();
    await task.destroy();

    return new Blob([bytes], { type: "application/pdf" });
}

$("pdf-process").addEventListener("click", async () => {
    if (!filePdf) return;

    const tombol = $("pdf-process");
    const targetBytes = targetPdfKb() * 1024;

    tampilkanError($("pdf-error"), null);
    $("pdf-result").classList.add("hidden");
    tombol.disabled = true;
    tombol.textContent = "Memproses…";

    try {
        if (
            filePdf.type !== "application/pdf" &&
            !filePdf.name.toLowerCase().endsWith(".pdf")
        ) {
            throw new Error(
                "File yang dipilih bukan PDF. Pilih file berakhiran .pdf.",
            );
        }

        if (filePdf.size <= targetBytes) {
            $("pdf-result-text").innerHTML =
                `Ukuran asli <strong>${formatUkuran(filePdf.size)}</strong> sudah di bawah target ` +
                `${formatUkuran(targetBytes)} — tidak perlu dikompres. Kamu bisa mengunduh salinannya apa adanya.`;
            unduhBlob(
                $("pdf-download"),
                filePdf,
                namaHasil(filePdf, "siap-unggah", "pdf"),
            );
            $("pdf-result").classList.remove("hidden");
            return;
        }

        $("pdf-progress-wrap").classList.remove("hidden");
        let hasil = null;

        for (const tingkat of TINGKAT_PDF) {
            hasil = await bangunPdfRaster(
                filePdf,
                tingkat,
                (halaman, total, label) => {
                    $("pdf-progress").style.width =
                        `${Math.round((halaman / total) * 100)}%`;
                    $("pdf-progress-text").textContent =
                        `Kualitas ${label} — halaman ${halaman} dari ${total}…`;
                },
            );
            if (hasil.size <= targetBytes) break;
        }

        const persen = Math.max(
            0,
            Math.round((1 - hasil.size / filePdf.size) * 100),
        );
        let teks =
            `<strong>${formatUkuran(filePdf.size)}</strong> → <strong>${formatUkuran(hasil.size)}</strong> ` +
            `(turun ${persen}%). Target: ${formatUkuran(targetBytes)}.`;
        if (hasil.size > targetBytes) {
            teks +=
                " Hasil masih di atas target pada kualitas terendah yang wajar — periksa kembali batas " +
                "resmi instansi, atau bagi dokumen menjadi dua file.";
        }
        $("pdf-result-text").innerHTML = teks;
        unduhBlob(
            $("pdf-download"),
            hasil,
            namaHasil(filePdf, `kompres-${targetPdfKb()}kb`, "pdf"),
        );
        $("pdf-result").classList.remove("hidden");
    } catch (err) {
        console.error(err);
        tampilkanError(
            $("pdf-error"),
            "PDF tidak bisa diproses. Pastikan file tidak terproteksi kata sandi dan tidak rusak, lalu coba lagi." +
                detailError(err),
        );
    } finally {
        $("pdf-progress-wrap").classList.add("hidden");
        tombol.disabled = false;
        tombol.textContent = "Kompres PDF";
    }
});

/* ---------------- Alat Foto ---------------- */

let fileFoto = null;

const pemilihFoto = buatPemilihPreset(".preset-foto", targetSah ?? 200);

pasangPemilihFile(
    $("foto-file"),
    document.querySelector('label[for="foto-file"]'),
    $("foto-file-info"),
    (file) => {
        fileFoto = file;
        $("foto-process").disabled = !file;
        tampilkanError($("foto-error"), null);
        $("foto-result").classList.add("hidden");
    },
);

function gambarFotoKeCanvas(bitmap, mode) {
    const canvas = document.createElement("canvas");

    if (mode === "pasfoto") {
        // Pas foto 4x6 cm pada ±300 dpi = 472 x 709 px, potong-tengah (cover).
        canvas.width = 472;
        canvas.height = 709;
        const skala = Math.max(
            canvas.width / bitmap.width,
            canvas.height / bitmap.height,
        );
        const lebar = bitmap.width * skala;
        const tinggi = bitmap.height * skala;
        canvas
            .getContext("2d")
            .drawImage(
                bitmap,
                (canvas.width - lebar) / 2,
                (canvas.height - tinggi) / 2,
                lebar,
                tinggi,
            );
        return canvas;
    }

    const maks = mode === "asli" ? Infinity : Number(mode);
    const skala = Math.min(1, maks / Math.max(bitmap.width, bitmap.height));
    canvas.width = Math.max(1, Math.round(bitmap.width * skala));
    canvas.height = Math.max(1, Math.round(bitmap.height * skala));
    canvas
        .getContext("2d")
        .drawImage(bitmap, 0, 0, canvas.width, canvas.height);
    return canvas;
}

const KUALITAS_FOTO = [0.92, 0.85, 0.78, 0.7, 0.62, 0.55, 0.48, 0.4];

$("foto-process").addEventListener("click", async () => {
    if (!fileFoto) return;

    const tombol = $("foto-process");
    const targetBytes = pemilihFoto.nilai * 1024;
    const mode = $("foto-dimensi").value;

    tampilkanError($("foto-error"), null);
    $("foto-result").classList.add("hidden");
    tombol.disabled = true;
    tombol.textContent = "Memproses…";

    try {
        const bitmap = await createImageBitmap(fileFoto);
        let canvas = gambarFotoKeCanvas(bitmap, mode);
        let hasil = null;
        let ketemu = false;

        // Coba tangga kualitas; bila belum cukup dan dimensi bebas, kecilkan dimensi bertahap.
        for (let putaran = 0; putaran < 6 && !ketemu; putaran++) {
            for (const q of KUALITAS_FOTO) {
                const blob = await canvasToBlob(canvas, "image/jpeg", q);
                hasil = blob; // kualitas menurun = ukuran menurun; simpan selalu yang terakhir
                if (blob.size <= targetBytes) {
                    ketemu = true;
                    break;
                }
            }
            if (ketemu || mode === "pasfoto") break;

            const kecil = document.createElement("canvas");
            kecil.width = Math.max(1, Math.round(canvas.width * 0.85));
            kecil.height = Math.max(1, Math.round(canvas.height * 0.85));
            kecil
                .getContext("2d")
                .drawImage(canvas, 0, 0, kecil.width, kecil.height);
            canvas = kecil;
        }

        const persen = Math.max(
            0,
            Math.round((1 - hasil.size / fileFoto.size) * 100),
        );
        let teks =
            `<strong>${formatUkuran(fileFoto.size)}</strong> → <strong>${formatUkuran(hasil.size)}</strong> ` +
            `(turun ${persen}%) · ${canvas.width}×${canvas.height} px. Target: ${formatUkuran(targetBytes)}.`;
        if (hasil.size > targetBytes) {
            teks +=
                " Hasil masih di atas target pada pengaturan terendah — coba target yang lebih longgar bila instansi mengizinkan.";
        }
        $("foto-result-text").innerHTML = teks;
        $("foto-preview").src = URL.createObjectURL(hasil);
        unduhBlob(
            $("foto-download"),
            hasil,
            namaHasil(fileFoto, `hasil-${pemilihFoto.nilai}kb`, "jpg"),
        );
        $("foto-result").classList.remove("hidden");
    } catch (err) {
        console.error(err);
        tampilkanError(
            $("foto-error"),
            "Foto tidak bisa diproses. Pastikan formatnya JPG, PNG, atau WebP dan filenya tidak rusak." +
                detailError(err),
        );
    } finally {
        tombol.disabled = false;
        tombol.textContent = "Proses Foto";
    }
});
