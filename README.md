# Kuick Message Broker

[![Latest Version](https://img.shields.io/github/release/milejko/kuick-message-broker.svg)](https://github.com/milejko/kuick-message-broker/releases)
[![GitHub Actions CI](https://github.com/milejko/kuick-message-broker/actions/workflows/ci.yml/badge.svg)](https://github.com/milejko/kuick-message-broker/actions/workflows/ci.yml)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/kuick/message-broker.svg)](https://packagist.org/packages/kuick/message-broker)

## What is Kuick Message Broker?

* It is a small, fast message broker with dedicated publisher, and PHP client, but it also can be used everywhere else via rest-json API.<br>
Ready to deploy images you can find here: https://hub.docker.com/r/kuickphp/message-broker/tags

## Requirements

* Message Broker is supported on PHP 8.2 and up.
* For optimal performance Redis storage should be used.

## Usage

1. Install Docker (if you don't have it already)
2. Run using Docker
```
docker run -p 8080:80 kuickphp/message-broker:alpine
```
Now you can try it out by opening http://localhost:8080/
3. Specify more options ie. set storage to redis (of course you should specify the real redis address)
```
docker run -p 8080:80 \
  -e KUICK_MB_STORAGE_DSN="redis://127.0.0.1:6379" \
  kuickphp/message-broker:alpine
```
4. Let's define some channel permissions, below configuration will give:
- "read" permission to "news" channel for "john@pass" and "jane@pass"
- "write" permission to "news" channel for "john@pass" only
```
docker run -p 8080:80 \
  -e KUICK_MB_CONSUMER_MAP="news[]=john@pass&news[]=jane@pass" \
  -e KUICK_MB_PUBLISHER_MAP="news[]=john@pass" \
  kuickphp/message-broker:alpine
```
Now you can check out message list, by calling http://localhost:8080/api/messages?channel=news<br>
Remember to set header "Authorization: Bearer john@pass"
