# -*- coding: utf-8 -*-
import time
import random
import sys
import string

# --- Kode Warna ANSI untuk Terminal ---
HIJAU = '\033[92m'  # Hijau
KUNING = '\033[93m' # Kuning
BIRU = '\033[94m'   # Biru
RESET = '\033[0m'   # Mengembalikan warna ke default

def clear_line():
    """Membersihkan baris saat ini di terminal."""
    sys.stdout.write("\r\033[K")
    sys.stdout.flush()

def generate_fake_user_agent():
    """Membuat string User-Agent acak yang terlihat asli."""
    android_version = f"{random.randint(9, 13)}.0"
    device_model = random.choice(["SM-G998B", "Pixel 7 Pro", "SM-A528B", "Xiaomi 2201116SG"])
    build = ''.join(random.choices('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', k=6))
    instagram_version = f"{random.randint(200, 250)}.0.0.{random.randint(10, 99)}.{random.randint(100, 120)}"
    return f"Instagram {instagram_version} Android ({android_version}; 480dpi; 1080x2280; samsung; {device_model}; {build}; en_US)"

def generate_dummy_users(count):
    """Membuat daftar akun dummy dengan username yang lebih realistis."""
    accounts = []
    nama_depan = ["adi", "budi", "cici", "dedi", "eko", "fitri", "gita", "hari", "indah", "joni"]
    nama_belakang = ["saputra", "wijaya", "lestari", "kusuma", "putri", "mahardika", "setiawan"]
    pemisah = ["_", ".", ""]
    
    for _ in range(count):
        user = f"{random.choice(nama_depan)}{random.choice(pemisah)}{random.choice(nama_belakang)}{random.randint(1, 99)}"
        password = ''.join(random.choices(string.ascii_lowercase + string.digits, k=8))
        accounts.append({'username': user, 'password': password})
    return accounts

def generate_instagram_data():
    """Menghasilkan data followers dan following secara acak."""
    followers = random.randint(50, 5000)
    following = random.randint(50, 1000)
    return {'followers': followers, 'following': following}

def main():
    """Fungsi utama untuk menjalankan simulasi pengecekan akun."""
    
    # --- Konfigurasi jumlah hasil yang diinginkan ---
    OK_COUNT = 15
    CP_COUNT = 3  # CP (Checkpoint)
    total_accounts = OK_COUNT + CP_COUNT

    # --- Membuat daftar tugas/hasil yang akan dijalankan ---
    outcomes = ['success'] * OK_COUNT + ['checkpoint'] * CP_COUNT
    random.shuffle(outcomes) # Acak urutan tugas agar tidak monoton

    # --- Menghasilkan data akun dummy sesuai jumlah yang dibutuhkan ---
    accounts = generate_dummy_users(total_accounts)
    
    ok_count = 0
    cp_count = 0

    print(f"{BIRU}--- Memulai Simulasi Pengecekan Akun Instagram ---{RESET}")
    time.sleep(1.5)

    for i, account in enumerate(accounts, 1):
        username = account['username']
        password = account['password']
        
        # Ambil hasil dari daftar tugas yang sudah diacak
        outcome = outcomes.pop()
        
        # Tampilkan status proses
        clear_line()
        print(f"[*] Memproses {i}/{total_accounts}...", end="")
        time.sleep(random.uniform(0.3, 1.0)) # Jeda acak agar terlihat realistis
        clear_line()

        # Tentukan hasil berdasarkan 'outcome'
        if outcome == 'success':
            ok_count += 1
            ig_data = generate_instagram_data()
            print(f"[{HIJAU}OK{RESET}] {HIJAU}Login Berhasil - Akun Ditemukan{RESET}")
            print(f"├─ Username : {username}")
            print(f"├─ Password : {password}")
            print(f"├─ Followers: {ig_data['followers']:,}") # Format angka dengan pemisah ribuan
            print(f"└─ Following: {ig_data['following']:,}")
        
        elif outcome == 'checkpoint':
            cp_count += 1
            print(f"[{KUNING}CP{RESET}] {KUNING}Akun Terkena Checkpoint{RESET}")
            print(f"├─ Username : {username}")
            print(f"├─ Password : {password}")
            print(f"└─ User-Agent: {KUNING}{generate_fake_user_agent()}{RESET}")
            
        # Menampilkan status bar di bawah setiap hasil
        status = f"[PROSES: {i}/{total_accounts}] [{HIJAU}OK: {ok_count}{RESET}] [{KUNING}CP: {cp_count}{RESET}]"
        print(status)
        print("=" * 45) # Garis pemisah

    print(f"\n[✓] Proses Selesai. Hasil: {HIJAU}{ok_count} OK{RESET}, {KUNING}{cp_count} CP{RESET}.")

if __name__ == "__main__":
    main()
