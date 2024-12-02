# Kuick Message Broker

[![Latest Version](https://img.shields.io/github/release/milejko/kuick-message-broker.svg)](https://github.com/milejko/kuick-message-broker/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/kuick/message-broker.svg)](https://packagist.org/packages/kuick/message-broker)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![GitHub Actions CI](https://github.com/milejko/kuick-message-broker/actions/workflows/ci.yml/badge.svg)](https://github.com/milejko/kuick-message-broker/actions/workflows/ci.yml)

A small, fast message broker with dedicated publisher based on Kuick(https://github.com/milejko/kuick).
Communication is handled via JSON-API, see some examples below.

## Requirements

* Kuick Message Broker is supported on PHP 8.2 and up.
* For optimal performance Redis storage should be used.

## Usage (Docker)
Ready to deploy images you can find here: https://hub.docker.com/r/kuickphp/message-broker/tags

1. Run using Docker
```
docker run -p 8080:80 kuickphp/message-broker
```
Now you can try it out by opening http://localhost:8080/
2. Specify more options ie. set storage to redis (of course you should specify the real redis address)
```
docker run -p 8080:80 \
  -e KUICK_MB_STORAGE_DSN="redis://127.0.0.1:6379" \
  kuickphp/message-broker
```
3. Let's define some channel permissions, below configuration will give:
- "read" permission to "news" channel for "john@pass" and "jane@pass"
- "write" permission to "news" channel for "john@pass" only
```
docker run -p 8080:80 \
  -e KUICK_MB_CONSUMER_MAP="news[]=john@pass&news[]=jane@pass" \
  -e KUICK_MB_PUBLISHER_MAP="news[]=john@pass" \
  kuickphp/message-broker
```
Now Kuick Message Broker runs on: http://localhost:8080/api/messages/news<br>
Posting the message by user "john@pass" to channel "news":
```
curl -X POST -H "Authorization: Bearer john@pass" -d 'Sample message' http://localhost:8080/api/message/news
```
Receiving messages from "news" channel, by "john@pass":
```
curl -H "Authorization: Bearer john@pass" http://localhost:8080/api/messages/news
```
Receiving a single message from "news" channel, by "john@pass", with automatic acknowledgement:
```
curl -H "Authorization: Bearer john@pass" "http://localhost:8080/api/message/news/{messageId}?autoack=true"
```
Manual acknowledgement:
```
curl -X POST -H "Authorization: Bearer john@pass" "http://localhost:8080/api/message/ack/news/{messageId}"
```
## Usage (Standalone)
1. Install PHP>8.2 + Composer
@TODO: finish this chapter