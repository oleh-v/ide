FROM python:2.7-alpine

RUN pip install elasticsearch-curator

ADD curator-cron /etc/cron.d/curator-cron
RUN chmod 0644 /etc/cron.d/curator-cron
RUN crontab /etc/cron.d/curator-cron

CMD ["crond", "-f", "-l", "8"]
