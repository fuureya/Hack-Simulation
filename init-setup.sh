#!/bin/bash

# LabSec Setup Script - Docker Permission Fix
# Digunakan untuk memperbaiki "permission denied" pada Docker di Linux

set -e

echo "🚀 Memulai setup lingkungan Docker..."

# 1. Cek apakah Docker terpasang
if ! command -v docker &> /dev/null; then
    echo "❌ Error: Docker belum terpasang. Silakan pasang Docker terlebih dahulu."
    exit 1
fi

# 2. Perbaiki Permission (Linux Only)
# 2. Perbaiki Permission (Linux Only)
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    echo "🐧 Mendeteksi sistem Linux..."
    
    # Perbaiki permission socket secara universal
    # Ini PENTING agar dashboard PHP bisa mengontrol Docker tanpa bentrok GID
    echo "🔧 Mengatur permission universal untuk docker.sock agar Dashboard bisa berjalan..."
    sudo chmod 666 /var/run/docker.sock
    echo "✅ Izin socket berhasil diperbarui (666)."

    # Tambahkan user ke group docker (Opsional namun direkomendasikan untuk CLI)
    if ! getent group docker > /dev/null; then
        echo "➕ Membuat group 'docker'..."
        sudo groupadd docker
    fi

    if ! groups $USER | grep &>/dev/null "\bdocker\b"; then
        echo "➕ Menambahkan user $USER ke group 'docker'..."
        sudo usermod -aG docker $USER
        echo "✅ User $USER berhasil ditambahkan ke group docker."
        echo "⚠️  CATATAN: Anda mungkin perlu LOGOUT dan LOGIN kembali agar perintah 'docker' tanpa sudo bisa digunakan."
    else
        echo "✅ User $USER sudah berada dalam group docker."
    fi
fi

echo ""
echo "✨ Setup universal selesai! Proyek sekarang siap dijalankan."
echo "Jalankan perintah ini untuk memulai:"
echo "👉 docker-compose up -d --build"
echo ""
