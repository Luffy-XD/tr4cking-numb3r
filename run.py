# -*- coding: utf-8 -*-
import time
import random
import sys
import string
from datetime import datetime

# --- Kode Warna ANSI untuk Terminal ---
HIJAU = '\033[92m'       # Hijau
KUNING = '\033[93m'      # Kuning
BIRU_LANGIT = '\033[96m' # Cyan/Light Blue
RESET = '\033[0m'        # Mengembalikan warna ke default

def clear_line():
    """Membersihkan baris saat ini di terminal."""
    sys.stdout.write("\r\033[K")
    sys.stdout.flush()

def generate_fake_user_agent():
    """Membuat string User-Agent Instagram acak yang terlihat asli."""
    android_version = f"{random.randint(10, 13)}"
    api_level = f"{random.randint(29, 33)}"
    device_model = random.choice(["SM-G998B", "Pixel 7 Pro", "SM-A536B", "Xiaomi 2201116SG"])
    brand = random.choice(["samsung", "google", "xiaomi"])
    instagram_version = f"{random.randint(240, 280)}.0.0.{random.randint(15, 30)}.{random.randint(100, 120)}"
    return f"Instagram {instagram_version} Android ({api_level}/{android_version}; 480dpi; 1080x2340; {brand}; {device_model}; {device_model}; en_US)"

def generate_dummy_users(count):
    """Membuat daftar akun dummy dengan username yang lebih realistis."""
    accounts = []
    nama_depan = ["rizky", "budi", "ayu", "dewi", "eka", "fitri", "gita", "indah", "jaya", "kurnia"]
    nama_belakang = ["saputra", "wijaya", "lestari", "kusuma", "putri", "mahardika", "setiawan"]
    pemisah = ["_", ".", ""]
    
    for _ in range(count):
        user = f"{random.choice(nama_depan)}{random.choice(pemisah)}{random.choice(nama_belakang)}{random.randint(10, 999)}"
        password = ''.join(random.choices(string.ascii_lowercase + string.digits, k=random.randint(6, 8)))
        accounts.append({'username': user, 'password': password})
    return accounts

def tampilkan_hasil_kotak(title, data, color_code):
    """Fungsi untuk menampilkan informasi akun dalam sebuah kotak bergaya."""
    baris_konten = [f"  {kunci.capitalize():<10}: {nilai}" for kunci, nilai in data.items()]
    lebar = max(len(baris) for baris in [title] + baris_konten) + 2

    print(f"{color_code}┌─{title.ljust(lebar - 4)}─┐{RESET}")
    for baris in baris_konten:
        print(f"{color_code}│{baris.ljust(lebar - 2)}│{RESET}")
    print(f"{color_code}└{'─' * (lebar - 2)}┘{RESET}")
    print()

def main():
    """Fungsi utama untuk menjalankan simulasi."""
    
    # --- Konfigurasi ---
    OK_COUNT = 10
    CP_COUNT = 2
    total_accounts = OK_COUNT + CP_COUNT

    outcomes = ['success'] * OK_COUNT + ['checkpoint'] * CP_COUNT
    random.shuffle(outcomes)
    accounts = generate_dummy_users(total_accounts)
    
    ok_count = 0
    cp_count = 0

    # Mapping angka bulan ke nama bulan dalam Bahasa Indonesia
    bulan_map = {
        1: "Januari", 2: "Februari", 3: "Maret", 4: "April",
        5: "Mei", 6: "Juni", 7: "Juli", 8: "Agustus",
        9: "September", 10: "Oktober", 11: "November", 12: "Desember"
    }

    print(f"{BIRU_LANGIT}--- Simulasi Pengecekan Akun Dimulai ---{RESET}")
    time.sleep(1)

    for i, account in enumerate(accounts, 1):
        status_proses = f"[Mencoba Login] [{i}/{total_accounts}] [{HIJAU}OK:{ok_count}{RESET}] [{KUNING}CP:{cp_count}{RESET}]"
        clear_line()
        sys.stdout.write(status_proses)
        sys.stdout.flush()
        time.sleep(random.uniform(0.5, 1.2))

        outcome = outcomes.pop()
        
        # Dapatkan tanggal dan format sesuai permintaan
        sekarang = datetime.now()
        nama_bulan = bulan_map[sekarang.month]
        judul_file_ok = f"OK-{sekarang.day}-{nama_bulan}-{sekarang.year}"
        judul_file_cp = f"CP-{sekarang.day}-{nama_bulan}-{sekarang.year}"

        if outcome == 'success':
            ok_count += 1
            data = {
                "Username": account['username'],
                "Password": account['password'],
                "Followers": f"{random.randint(50, 9000):,}",
                "Following": f"{random.randint(50, 1500):,}"
            }
            clear_line()
            tampilkan_hasil_kotak(judul_file_ok, data, HIJAU)
        
        elif outcome == 'checkpoint':
            cp_count += 1
            data = {
                "Username": account['username'],
                "Password": account['password'],
                "User-Agent": generate_fake_user_agent()
            }
            clear_line()
            tampilkan_hasil_kotak(judul_file_cp, data, KUNING)
    
    clear_line()
    print(f"[✓] Proses Selesai. Hasil Akhir: [{HIJAU}OK: {ok_count}{RESET}] [{KUNING}CP: {cp_count}{RESET}].")

if __name__ == "__main__":
    main()
