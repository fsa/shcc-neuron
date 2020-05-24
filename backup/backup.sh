#!/bin/bash

pg_dump -U shcc shcc -h 127.0.0.1 > shcc.sql
