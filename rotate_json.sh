#!/bin/bash
# Script: rotate_json.sh
# Purpose: Rename existing JSON files with current date and time

cd /var/www/html/mage/raptor_ses || exit

# Get timestamp (e.g., 2025-11-13_00-00-00)
timestamp=$(date +"%Y-%m-%d_%H-%M-%S")

# Rename (move) the files with timestamp
for file in centralized_email_count_stats.json centralized_email_log.json; do
  if [ -f "$file" ]; then
    mv "$file" "${file%.json}_$timestamp.json"
  fi
done

# Optional: log the operation
echo "$(date '+%Y-%m-%d %H:%M:%S') - Rotated JSON files" >> /var/log/raptor_ses_json_rotate.log

