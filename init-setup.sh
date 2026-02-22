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
if [[ "$OSTYPE" == "linux-gnu"* ]]; then
    echo "🐧 Mendeteksi sistem Linux, mengecek group docker..."
    
    # Cek apakah group docker sudah ada
    if ! getent group docker > /dev/null; then
        echo "➕ Membuat group 'docker'..."
        sudo groupadd docker
    fi

    # Tambahkan user ke group docker
    if ! groups $USER | grep &>/dev/null "\bdocker\b"; then
        echo "➕ Menambahkan user $USER ke group 'docker'..."
        sudo usermod -aG docker $USER
        echo "✅ User berhasil ditambahkan ke group docker."
        echo "⚠️  PENTING: Anda harus LOGOUT dan LOGIN kembali agar perubahan ini berlaku."
        echo "Atau jalankan perintah ini jika ingin mencoba langsung: newgrp docker"
    else
        echo "✅ User $USER sudah berada dalam group docker."
    fi
    
    # Perbaiki permission socket jika masih bermasalah (last resort)
    echo "🔧 Mengatur permission untuk docker.sock..."
    sudo chmod 666 /var/run/docker.sock
fi

echo ""
echo "✨ Setup awal selesai!"
echo "Silakan coba jalankan project dengan: docker-compose up -d"
echo ""
