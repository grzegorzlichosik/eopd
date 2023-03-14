#!/usr/bin/bash
# Create Hourly Cron Job With Clamscan

generate_slack_data() {
  cat <<EOF
  {"text":"VIRUS PROBLEM on $APP_ENV\n$VIRUSES"}
EOF
}

send_slack_message() {
  curl -X POST -H 'Content-type: application/json' --data "$(generate_slack_data)" \
    https://hooks.slack.com/services/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 >/dev/null
}

# Directories to scan
scan_dir="/var/www/html"

# Temporary file
list_file=$(mktemp -t clamscan.XXXXXX) || exit 1

# Location of log file
log_file="/var/log/clamav/hourly_clamscan.log"

# Make list of new files
if [ -f  "$log_file" ]
then
        # use newer files then logfile
        find "$scan_dir" -type f -cnewer "$log_file" -fprint "$list_file"
else
        # scan last 60 minutes
        find "$scan_dir" -type f -cmin -60 -fprint "$list_file"
fi

if [ -s "$list_file" ]
then
        # Scan files and remove (--remove) infected
        clamscan -i -f "$list_file" --remove=yes > "$log_file"

        # If there were infected files detected, send slack alert
        if [ `cat $log_file | grep Infected | grep -v 0 | wc -l` != 0 ]
        then
                VIRUSES=$(egrep "FOUND" $log_file)
                send_slack_message
        fi
else
        # remove the empty file, contains no info
        rm -f "$list_file"
fi
exit 0

