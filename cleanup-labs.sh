#!/bin/bash

# Script to cleanup all lab containers that might have stale network references
# These are the container names defined in api.php

LAB_CONTAINERS=(
    "neobank-csrf-container"
    "cinemax-xss-container"
    "neohms-sqli-container"
    "shop-bac-container"
    "ticket-idor-container"
    "safevault-cryptofail-container"
    "quickloan-insecuredesign-container"
    "devops-secmisconfig-container"
    "securelogin-authfail-container"
    "audittrail-loggingfail-container"
    "webfetcher-ssrf-container"
    "insecurelibrary-legacycms-container"
    "integrityfail-objectrelay-container"
)

echo "🧹 Cleaning up stale lab containers..."

for container in "${LAB_CONTAINERS[@]}"; do
    if [ "$(docker ps -a -q -f name=^/${container}$)" ]; then
        echo "🗑️ Removing $container..."
        docker rm -f "$container"
    fi
done

echo "✅ Cleanup complete! You can now start labs fresh from the dashboard."
