#!/bin/bash

while true
do
    curl "http://192.168.0.10:8000/construction/"
    sleep 10
    curl "http://192.168.0.10:8000/construction/"
    sleep 10
    curl "http://192.168.0.10:8000/construction/"
    sleep 10
    curl "http://192.168.0.10:8000/resources/"
    sleep 10
    curl "http://192.168.0.10:8000/construction/"
    sleep 10
    curl "http://192.168.0.10:8000/construction/"
    sleep 10
done
