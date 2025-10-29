<style>
.footer-mini {
    background: linear-gradient(rgb(131, 122, 182), rgb(162, 122, 182));
    color: white;
    padding: 15px 0; /* lebih tipis */
    margin-top: auto;
    font-size: 0.7rem; /* ekstra mini */
}

.footer-container {
    max-width: 1000px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    text-align: left;
    gap: 10px;
}

.footer-section {
    flex: 1;
    min-width: 180px;
    margin: 5px;
}
.footer-section h3 {
    margin-bottom: 6px;
    font-size: 0.85rem;
}
.footer-section p {
    margin: 0 0 4px 0;
    font-size: 0.65rem;
    line-height: 1.3;
}

.footer-section i {
    margin-right: 6px;
    font-size: 0.65rem;
}

.footer-bottom {
    text-align: center;
    margin-top: 12px;
    font-size: 0.6rem;
    border-top: 1px solid rgba(255,255,255,0.2);
    padding-top: 6px;
}

</style>

<footer class="footer-mini">
    <div class="footer-container">
        <!-- Left: App Info -->
        <div class="footer-section">
            <h3>Serenity</h3>
            <p>
                Aplikasi bimbingan konseling untuk mendukung perkembangan karakter siswa SMK Negeri 13 Bandung.
            </p>
        </div>

        <!-- Right: Contact Info -->
        <div class="footer-section">
            <h3>Kontak Kami</h3>
            <p><i class="fab fa-whatsapp"></i> +62 896 5877 6754</p>
            <p><i class="fas fa-envelope"></i> bk13bandung@gmail.com</p>
            <p><i class="fab fa-instagram"></i> ruangbksmkn13bandung</p>
        </div>

        <!-- Address -->
        <div class="footer-section">
            <h3>Alamat</h3>
            <p>
                Jl. Soekarno-Hatta No.KM. 10, Jatisari, Kec. Buahbatu, Kota Bandung, Jawa Barat 40286, Indonesia
            </p>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; {{ date('Y') }} Serenity. All rights reserved.
    </div>
</footer>
